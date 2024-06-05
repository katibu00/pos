<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Returns;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Estimate;
use App\Models\Purchase;
use App\Models\CashCredit;
use App\Models\FundTransfer;
use App\Models\User;
use App\Models\Stock;

class UpdateBalances extends Command
{
    protected $signature = 'update:balances';
    protected $description = 'Update cash, POS, and bank transfer balances every minute';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $branch_id = 1; // Replace with your actual branch ID or fetch dynamically
        $todaySales = Sale::where('branch_id', $branch_id)
            ->whereNotIn('stock_id', [1093, 1012])
            ->whereDate('created_at', today())
            ->get();

        $todayReturns = Returns::where('branch_id', $branch_id)
            ->whereNull('channel')
            ->whereDate('created_at', today())
            ->get();

        $todayExpenses = Expense::where('branch_id', $branch_id)
            ->whereDate('created_at', today())
            ->get();

        $creditPayments = Payment::where('branch_id', $branch_id)
            ->whereDate('created_at', today())
            ->get();

        $estimates = Estimate::where('branch_id', $branch_id)
            ->whereDate('created_at', today())
            ->get();

        $purchases = Purchase::select('stock_id', 'quantity')
            ->where('branch_id', $branch_id)
            ->whereDate('created_at', today())
            ->get();

        $cashCreditToday = CashCredit::where('branch_id', $branch_id)
            ->whereDate('created_at', today())
            ->sum(DB::raw('amount'));

        $paymentSums = [
            'cash' => 0,
            'transfer' => 0,
            'pos' => 0,
        ];

        $payments = DB::table('cash_credit_payments')
            ->select('payment_method', DB::raw('SUM(amount_paid) as total_amount_paid'))
            ->whereDate('created_at', today())
            ->groupBy('payment_method')
            ->get();

        foreach ($payments as $payment) {
            if (array_key_exists($payment->payment_method, $paymentSums)) {
                $paymentSums[$payment->payment_method] += $payment->total_amount_paid;
            }
        }

        $creditPaymentSummary = [
            'cash' => $paymentSums['cash'],
            'transfer' => $paymentSums['transfer'],
            'pos' => $paymentSums['pos'],
        ];

        $cashTransfers = FundTransfer::where(function ($query) {
            $query->where('from_account', 'cash')
                  ->orWhere('to_account', 'cash');
        })
        ->whereDate('created_at', Carbon::today())
        ->where('branch_id', $branch_id)
        ->get();

        $transferTransfers = FundTransfer::where(function ($query) {
            $query->where('from_account', 'transfer')
                  ->orWhere('to_account', 'transfer');
        })
        ->whereDate('created_at', Carbon::today())
        ->where('branch_id', $branch_id)
        ->get();

        $posTransfers = FundTransfer::where(function ($query) {
            $query->where('from_account', 'pos')
                  ->orWhere('to_account', 'pos');
        })
        ->whereDate('created_at', Carbon::today())
        ->where('branch_id', $branch_id)
        ->get();

        $totalCashCredit = CashCredit::where('branch_id', $branch_id)
            ->whereRaw('amount > amount_paid')
            ->sum(DB::raw('amount - amount_paid'));

        $deposits = User::where('branch_id', $branch_id)->sum('deposit');
        $totalDiscounts = $todaySales->sum('discount');
        
        $grossSales = $todaySales->sum(function ($sale) {
            return $sale->price * $sale->quantity;
        });

        $uniquePosReceipts = [];
        $posSales = $todaySales
            ->filter(function ($sale) {
                return $sale->payment_method == 'pos' || $sale->payment_method == 'multiple';
            })
            ->reduce(function ($total, $sale) use (&$uniquePosReceipts) {
                if ($sale->payment_method == 'multiple') {
                    if (!in_array($sale->receipt_no, $uniquePosReceipts)) {
                        $paymentAmount = Payment::where('receipt_nos', $sale->receipt_no)
                            ->where('payment_type', 'multiple')
                            ->where('payment_method', 'pos')
                            ->value('payment_amount');
                        $total += $paymentAmount ?? 0;
                        $uniquePosReceipts[] = $sale->receipt_no;
                    }
                } else {
                    $total += ($sale->price * $sale->quantity) - $sale->discount;
                }
                return $total;
            }, 0);

        $uniqueReceipts = [];
        $cashSales = $todaySales
            ->filter(function ($sale) {
                return $sale->payment_method == 'cash' || $sale->payment_method == 'multiple';
            })
            ->reduce(function ($total, $sale) use (&$uniqueReceipts) {
                if ($sale->payment_method == 'multiple') {
                    if (!in_array($sale->receipt_no, $uniqueReceipts)) {
                        $paymentAmount = Payment::where('receipt_nos', $sale->receipt_no)
                            ->where('payment_type', 'multiple')
                            ->where('payment_method', 'cash')
                            ->value('payment_amount');
                        $total += $paymentAmount ?? 0;
                        $uniqueReceipts[] = $sale->receipt_no;
                    }
                } else {
                    $total += ($sale->price * $sale->quantity) - $sale->discount;
                }
                return $total;
            }, 0);

        $uniqueTransferReceipts = [];
        $transferSales = $todaySales
            ->filter(function ($sale) {
                return $sale->payment_method == 'transfer' || $sale->payment_method == 'multiple';
            })
            ->reduce(function ($total, $sale) use (&$uniqueTransferReceipts) {
                if ($sale->payment_method == 'multiple') {
                    if (!in_array($sale->receipt_no, $uniqueTransferReceipts)) {
                        $paymentAmount = Payment::where('receipt_nos', $sale->receipt_no)
                            ->where('payment_type', 'multiple')
                            ->where('payment_method', 'transfer')
                            ->value('payment_amount');
                        $total += $paymentAmount ?? 0;
                        $uniqueTransferReceipts[] = $sale->receipt_no;
                    }
                } else {
                    $total += ($sale->price * $sale->quantity) - $sale->discount;
                }
                return $total;
            }, 0);

        $creditSales = $todaySales->where('payment_method', 'credit')->reduce(function ($total, $sale) {
            $total += ($sale->price * $sale->quantity) - $sale->discount;
            return $total;
        }, 0);

        $depositSales = $todaySales->where('payment_method', 'deposit')->reduce(function ($total, $sale) {
            $total += ($sale->price * $sale->quantity) - $sale->discount;
            return $total;
        }, 0);

        $grossProfit = $todaySales->sum(function ($sale) {
            if($sale->buying_price == 0) {
                return ($sale->price - $sale->product->buying_price) * $sale->quantity;
            } else {
                return ($sale->price - $sale->buying_price) * $sale->quantity;
            }
        });

        $uniqueSalesCount = $todaySales->unique('receipt_no')->count();
        $totalItemsSold = $todaySales->sum('quantity');

        $totalReturn = $todayReturns->sum(function ($return) {
            return ($return->price * $return->quantity);
        });

        $cashReturns = $todayReturns->where('payment_method', 'cash')->reduce(function ($total, $return) {
            $total += ($return->price * $return->quantity) - $return->discount;
            return $total;
        }, 0);

        $posReturns = $todayReturns->where('payment_method', 'pos')->reduce(function ($total, $return) {
            $total += ($return->price * $return->quantity) - $return->discount;
            return $total;
        }, 0);

        $transferReturns = $todayReturns->where('payment_method', 'transfer')->reduce(function ($total, $return) {
            $total += ($return->price * $return->quantity) - $return->discount;
            return $total;
        }, 0);

        $returnDiscounts = $todayReturns->sum('discount');
        $returnProfit = $todayReturns->sum(function ($return) {
            return (($return->price - $return->product->buying_price) * $return->quantity);
        });

        $profileReturns = $todayReturns->where('return_channel', 'profile')->sum(function ($return) {
            return ($return->price * $return->quantity);
        });

        $profileReturnDiscounts = $todayReturns->where('return_channel', 'profile')->sum('discount');

        $totalExpenses = $todayExpenses->sum('amount');
        $cashExpenses = $todayExpenses->where('payment_method', 'cash')->sum('amount');
        $posExpenses = $todayExpenses->where('payment_method', 'pos')->sum('amount');
        $transferExpenses = $todayExpenses->where('payment_method', 'transfer')->sum('amount');

        $totalCreditPayments = $creditPayments->where('payment_type', 'credit')->sum('payment_amount');
        $cashCreditPayments = $creditPayments->where('payment_method', 'cash')->where('payment_type', 'credit')->sum('payment_amount');
        $posCreditPayments = $creditPayments->where('payment_method', 'POS')->where('payment_type', 'credit')->sum('payment_amount');
        $transferCreditPayments = $creditPayments->where('payment_method', 'transfer')->where('payment_type', 'credit')->sum('payment_amount');

        $totalDepositPayments = $creditPayments->where('payment_type', 'deposit')->sum('payment_amount');
        $cashDepositPayments = $creditPayments->where('payment_method', 'cash')->where('payment_type', 'deposit')->sum('payment_amount');
        $posDepositPayments = $creditPayments->where('payment_method', 'POS')->where('payment_type', 'deposit')->sum('payment_amount');
        $transferDepositPayments = $creditPayments->where('payment_method', 'transfer')->where('payment_type', 'deposit')->sum('payment_amount');

        $totalEstimate = $estimates->sum(function ($estimate) {
            return ($estimate->price * $estimate->quantity) - $estimate->discount;
        });

        $totalPurchases = $purchases->sum(function ($purchase) {
            return $purchase['product']['buying_price'] * $purchase->quantity;
        });

        $cashFundTransfer = $transferFundTransfer = $posFundTransfer = 0;

        $cashFundTransfer += $cashTransfers->sum(function ($transfer) {
            return $transfer->from_account === 'cash' ? -$transfer->amount : ($transfer->to_account === 'cash' ? $transfer->amount : 0);
        });

        $transferFundTransfer += $transferTransfers->sum(function ($transfer) {
            return $transfer->from_account === 'transfer' ? -$transfer->amount : ($transfer->to_account === 'transfer' ? $transfer->amount : 0);
        });

        $posFundTransfer += $posTransfers->sum(function ($transfer) {
            return $transfer->from_account === 'pos' ? -$transfer->amount : ($transfer->to_account === 'pos' ? $transfer->amount : 0);
        });

        DB::table('balances')->updateOrInsert(
            ['branch_id' => $branch_id, 'date' => Carbon::today()],
            [
                'cash_balance' => $cashSales - ($cashExpenses + $cashReturns) + $cashCreditPayments + $cashDepositPayments - $cashCreditToday + $creditPaymentSummary['cash'] + $cashFundTransfer,
                'pos_balance' => $posSales - ($posExpenses + $posReturns) + $posCreditPayments + $posDepositPayments + $creditPaymentSummary['pos'] + $posFundTransfer,
                'transfer_balance' => $transferSales - ($transferExpenses + $transferReturns) + $transferCreditPayments + $transferDepositPayments + $creditPaymentSummary['transfer'] + $transferFundTransfer,
                'updated_at' => now(),
            ]
        );
    }
}
