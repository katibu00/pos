<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashCredit;
use App\Models\Estimate;
use App\Models\Expense;
use App\Models\FundTransfer;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierDashboardController extends Controller
{
    public function selectCashier()
    {
        $staff_id = auth()->user()->branch_id;

        $data['branches'] = Branch::all();
        
        $data['staffss'] = User::where('branch_id',$staff_id)->whereNotIn('usertype', ['customer', 'supplier'])->get();

        return view('dashboard.dashboard', $data);
    }




    public function viewCashierDashboard(Request $request)
    {
        $data['branches'] = Branch::all();
        $staff_id = $request->staff_id;


        if ($request->start_date && $request->end_date) {
            // Date range is provided
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);
        
            if ($endDate->isFuture()) {
                Toastr::error('End date cannot be in the future');
                return redirect()->route('admin.home');
            }
        
            $todaySales = Sale::where('user_id', $staff_id)
                ->whereNotIn('stock_id', [1093, 1012])
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->get();
        
            $todayReturns = Returns::where('cashier_id', $staff_id)
                ->whereNull('channel')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->get();
        
            $todayExpenses = Expense::where('payer_id', $staff_id)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->get();
        
            $creditPayments = Payment::where('user_id', $staff_id)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->get();
        
            $estimates = Estimate::where('cashier_id', $staff_id)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->get();
        
            
            $data['start_date'] = $request->start_date;
            $data['end_date'] = $request->end_date;
            $data['date_type'] = 'range';
        
            $data['cashCreditToday'] = CashCredit::where('cashier_id', $staff_id)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->sum('amount');
        
            $paymentSums = [
                'cash' => 0,
                'transfer' => 0,
                'pos' => 0,
            ];
        
            $payments = DB::table('cash_credit_payments')
                ->select('payment_method', DB::raw('SUM(amount_paid) as total_amount_paid'))
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->groupBy('payment_method')
                ->get();
        
            foreach ($payments as $payment) {
                if (array_key_exists($payment->payment_method, $paymentSums)) {
                    $paymentSums[$payment->payment_method] += $payment->total_amount_paid;
                }
            }
        
            $data['CreditPaymentSummary'] = [
                'cash' => $paymentSums['cash'],
                'transfer' => $paymentSums['transfer'],
                'pos' => $paymentSums['pos'],
            ];

            $cashTransfers = FundTransfer::where(function ($query) {
                $query->where('from_account', 'cash')
                      ->orWhere('to_account', 'cash');
            })
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('cashier_id',$staff_id)
            ->get();
            
            // Get transfer transfers created today
            $transferTransfers = FundTransfer::where(function ($query) {
                $query->where('from_account', 'transfer')
                      ->orWhere('to_account', 'transfer');
            })
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('cashier_id',$staff_id)
            ->get();
            
            // Get pos transfers created today
            $posTransfers = FundTransfer::where(function ($query) {
                $query->where('from_account', 'pos')
                      ->orWhere('to_account', 'pos');
            })
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('cashier_id',$staff_id)
            ->get();
        
        } elseif ($request->selected_date) {
            // Single date is provided
            $selectedDate = \Carbon\Carbon::parse($request->selected_date);
        
            $todaySales = Sale::where('user_id', $staff_id)
                ->whereNotIn('stock_id', [1093, 1012])
                ->whereDate('created_at', $selectedDate)
                ->get(); 
        
            $todayReturns = Returns::where('cashier_id', $staff_id)
                ->whereNull('channel')
                ->whereDate('created_at', $selectedDate)
                ->get();
        
            $todayExpenses = Expense::where('payer_id', $staff_id)
                ->whereDate('created_at', $selectedDate)
                ->get();
        
            $creditPayments = Payment::where('user_id', $staff_id)
                ->whereDate('created_at', $selectedDate)
                ->get();
        
            $estimates = Estimate::where('cashier_id', $staff_id)
                ->whereDate('created_at', $selectedDate)
                ->get();
        
            
            $data['selected_date'] = $request->selected_date;
            $data['date_type'] = 'single';
        
            $data['cashCreditToday'] = CashCredit::where('cashier_id', $staff_id)
                ->whereDate('created_at', $selectedDate)
                ->sum(DB::raw('amount'));
        
            $paymentSums = [
                'cash' => 0,
                'transfer' => 0,
                'pos' => 0,
            ];

       
        
            $payments = DB::table('cash_credit_payments')
                ->select('payment_method', DB::raw('SUM(amount_paid) as total_amount_paid'))
                ->whereDate('created_at', $selectedDate)
                ->where('cashier_id',$staff_id)
                ->groupBy('payment_method')
                ->get();
        
            foreach ($payments as $payment) {
                if (array_key_exists($payment->payment_method, $paymentSums)) {
                    $paymentSums[$payment->payment_method] += $payment->total_amount_paid;
                }
            }
        
            $data['CreditPaymentSummary'] = [
                'cash' => $paymentSums['cash'],
                'transfer' => $paymentSums['transfer'],
                'pos' => $paymentSums['pos'],
            ];


            $cashTransfers = FundTransfer::where(function ($query) {
                $query->where('from_account', 'cash')
                      ->orWhere('to_account', 'cash');
            })
            ->whereDate('created_at', $selectedDate)
            ->where('cashier_id',$staff_id)
            ->get();
            
            // Get transfer transfers created today
            $transferTransfers = FundTransfer::where(function ($query) {
                $query->where('from_account', 'transfer')
                      ->orWhere('to_account', 'transfer');
            })
            ->whereDate('created_at', $selectedDate)
            ->where('cashier_id',$staff_id)
            ->get();
            
            // Get pos transfers created today
            $posTransfers = FundTransfer::where(function ($query) {
                $query->where('from_account', 'pos')
                      ->orWhere('to_account', 'pos');
            })
            ->whereDate('created_at', $selectedDate)
            ->where('cashier_id',$staff_id)
            ->get();
        
        } else {
            // No date provided, use today's date
            $todaySales = Sale::where('user_id', $staff_id)
                ->whereNotIn('stock_id', [1093, 1012])
                ->whereDate('created_at', today())
                ->get();
        
            $todayReturns = Returns::where('cashier_id', $staff_id)
                ->whereNull('channel')
                ->whereDate('created_at', today())
                ->get();
        
            $todayExpenses = Expense::where('payer_id', $staff_id)
                ->whereDate('created_at', today())
                ->get();
        
            $creditPayments = Payment::where('user_id', $staff_id)
                ->whereDate('created_at', today())
                ->get();
        
            $estimates = Estimate::where('branch_id', $staff_id)
                ->whereDate('created_at', today())
                ->get();
        
        
            $data['cashCreditToday'] = CashCredit::where('cashier_id', $staff_id)
                ->whereDate('created_at', today())
                ->sum(DB::raw('amount'));
        
            $paymentSums = [
                'cash' => 0,
                'transfer' => 0,
                'pos' => 0,
            ];
        
            $payments = DB::table('cash_credit_payments')
                ->select('payment_method', DB::raw('SUM(amount_paid) as total_amount_paid'))
                ->where('cashier_id',$staff_id)
                ->whereDate('created_at', today())
                ->groupBy('payment_method')
                ->get();
        
            foreach ($payments as $payment) {
                if (array_key_exists($payment->payment_method, $paymentSums)) {
                    $paymentSums[$payment->payment_method] += $payment->total_amount_paid;
                }
            }
        
            $data['CreditPaymentSummary'] = [
                'cash' => $paymentSums['cash'],
                'transfer' => $paymentSums['transfer'],
                'pos' => $paymentSums['pos'],
            ];


            $cashTransfers = FundTransfer::where(function ($query) {
                $query->where('from_account', 'cash')
                      ->orWhere('to_account', 'cash');
            })
            ->whereDate('created_at', Carbon::today())
            ->where('cashier_id',$staff_id)
            ->get();
            
            // Get transfer transfers created today
            $transferTransfers = FundTransfer::where(function ($query) {
                $query->where('from_account', 'transfer')
                      ->orWhere('to_account', 'transfer');
            })
            ->whereDate('created_at', Carbon::today())
            ->where('cashier_id',$staff_id)
            ->get();
            
            // Get pos transfers created today
            $posTransfers = FundTransfer::where(function ($query) {
                $query->where('from_account', 'pos')
                      ->orWhere('to_account', 'pos');
            })
            ->whereDate('created_at', Carbon::today())
            ->where('cashier_id',$staff_id)
            ->get();
        }

        $data['TotalcashCredit'] = CashCredit::where('cashier_id', $staff_id)
            ->whereRaw('amount > amount_paid')
            ->sum(DB::raw('amount - amount_paid'));

        $data['totalDiscounts'] = $todaySales->sum('discount');
        //sales
        $data['grossSales'] = $todaySales->sum(function ($sale) {
            return $sale->price * $sale->quantity;
        });
        $data['totalDiscount'] = $todaySales->sum('discount');
       
        $uniquePosReceipts = [];

        $data['posSales'] = $todaySales
            ->filter(function ($sale) {
                return $sale->payment_method == 'pos' || $sale->payment_method == 'multiple';
            })
            ->reduce(function ($total, $sale) use (&$uniquePosReceipts) {
                if ($sale->payment_method == 'multiple') {
                    // Check if the receipt number is not already in the uniquePosReceipts array
                    if (!in_array($sale->receipt_no, $uniquePosReceipts)) {
                        $paymentAmount = Payment::where('receipt_nos', $sale->receipt_no)
                            ->where('payment_type', 'multiple')
                            ->where('payment_method', 'pos')
                            ->value('payment_amount');

                        $total += $paymentAmount ?? 0;

                        // Add the receipt number to the uniquePosReceipts array to ensure uniqueness
                        $uniquePosReceipts[] = $sale->receipt_no;
                    }
                } else {
                    $total += ($sale->price * $sale->quantity) - $sale->discount;
                }

                return $total;
            }, 0);

       
        $uniqueReceipts = [];

        $data['cashSales'] = $todaySales
            ->filter(function ($sale) {
                return $sale->payment_method == 'cash' || $sale->payment_method == 'multiple';
            })
            ->reduce(function ($total, $sale) use (&$uniqueReceipts) {
                if ($sale->payment_method == 'multiple') {
                    // Check if the receipt number is not already in the uniqueReceipts array
                    if (!in_array($sale->receipt_no, $uniqueReceipts)) {
                        $paymentAmount = Payment::where('receipt_nos', $sale->receipt_no)
                            ->where('payment_type', 'multiple')
                            ->where('payment_method', 'cash')
                            ->value('payment_amount');

                        $total += $paymentAmount ?? 0;

                        // Add the receipt number to the uniqueReceipts array to ensure uniqueness
                        $uniqueReceipts[] = $sale->receipt_no;
                    }
                } else {
                    $total += ($sale->price * $sale->quantity) - $sale->discount;
                }

                return $total;
            }, 0);

       
        $uniqueTransferReceipts = [];

        $data['transferSales'] = $todaySales
            ->filter(function ($sale) {
                return $sale->payment_method == 'transfer' || $sale->payment_method == 'multiple';
            })
            ->reduce(function ($total, $sale) use (&$uniqueTransferReceipts) {
            if ($sale->payment_method == 'multiple') {
                // Check if the receipt number is not already in the uniqueTransferReceipts array
                if (!in_array($sale->receipt_no, $uniqueTransferReceipts)) {
                    $paymentAmount = Payment::where('receipt_nos', $sale->receipt_no)
                        ->where('payment_type', 'multiple')
                        ->where('payment_method', 'transfer')
                        ->value('payment_amount');

                    $total += $paymentAmount ?? 0;

                    // Add the receipt number to the uniqueTransferReceipts array to ensure uniqueness
                    $uniqueTransferReceipts[] = $sale->receipt_no;
                }
            } else {
                $total += ($sale->price * $sale->quantity) - $sale->discount;
            }

            return $total;
        }, 0);




        $data['creditSales'] = $todaySales->where('payment_method', 'credit')->reduce(function ($total, $sale) {
            $total += ($sale->price * $sale->quantity) - $sale->discount;
            return $total;
        }, 0);
        $data['depositSales'] = $todaySales->where('payment_method', 'deposit')->reduce(function ($total, $sale) {
            $total += ($sale->price * $sale->quantity) - $sale->discount;
            return $total;
        }, 0);

        $data['grossProfit'] = $todaySales->sum(function ($sale) {
            
            if($sale->buying_price == 0)
            {
                return ($sale->price - $sale->product->buying_price) * $sale->quantity;

            }else
            {
                return ($sale->price - $sale->buying_price) * $sale->quantity;
            }
        });

        $data['uniqueSalesCount'] = @$todaySales->unique('receipt_no')->count();
        $data['totalItemsSold'] = $todaySales->sum('quantity');
        //returns
        $data['totalReturn'] = $todayReturns->sum(function ($return) {
            return ($return->price * $return->quantity);
        });
        $data['cashReturns'] = $todayReturns->where('payment_method', 'cash')->reduce(function ($total, $return) {
            $total += ($return->price * $return->quantity) - $return->discount;
            return $total;
        }, 0);
        $data['posReturns'] = $todayReturns->where('payment_method', 'pos')->reduce(function ($total, $return) {
            $total += ($return->price * $return->quantity) - $return->discount;
            return $total;
        }, 0);
        $data['transferReturns'] = $todayReturns->where('payment_method', 'transfer')->reduce(function ($total, $return) {
            $total += ($return->price * $return->quantity) - $return->discount;
            return $total;
        }, 0);
        $data['returnDiscounts'] = $todayReturns->sum('discount');

        $data['returnProfit'] = $todayReturns->sum(function ($return) {
            return (($return->price-@$return->product->buying_price) * $return->quantity);
        });

        //Expenses
        $data['totalExpenses'] = $todayExpenses->sum('amount');
        $data['cashExpenses'] = $todayExpenses->where('payment_method', 'cash')->sum('amount');
        $data['posExpenses'] = $todayExpenses->where('payment_method', 'pos')->sum('amount');
        $data['transferExpenses'] = $todayExpenses->where('payment_method', 'transfer')->sum('amount');
        //credit Payments
        $data['totalCreditPayments'] = $creditPayments->where('payment_type', 'credit')->sum('payment_amount');
        $data['cashCreditPayments'] = $creditPayments->where('payment_method', 'cash')->where('payment_type', 'credit')->sum('payment_amount');
        $data['posCreditPayments'] = $creditPayments->where('payment_method', 'POS')->where('payment_type', 'credit')->sum('payment_amount');
        $data['transferCreditPayments'] = $creditPayments->where('payment_method', 'transfer')->where('payment_type', 'credit')->sum('payment_amount');
        //deposits
        $data['totalDepositPayments'] = $creditPayments->where('payment_type', 'deposit')->sum('payment_amount');
        $data['cashDepositPayments'] = $creditPayments->where('payment_method', 'cash')->where('payment_type', 'deposit')->sum('payment_amount');
        $data['posDepositPayments'] = $creditPayments->where('payment_method', 'POS')->where('payment_type', 'deposit')->sum('payment_amount');
        $data['transferDepositPayments'] = $creditPayments->where('payment_method', 'transfer')->where('payment_type', 'deposit')->sum('payment_amount');
        //estimates
        $data['totalEstimate'] = $estimates->sum(function ($estimate) {
            return ($estimate->price * $estimate->quantity) - $estimate->discount;
        });
        //purchases
       
       
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(6);


        $data['cashFundTransfer'] = $data['transferFundTransfer'] = $data['posFundTransfer'] = 0;


        // Adjust the account balances based on funds transfers
        $data['cashFundTransfer'] += $cashTransfers->sum(function ($transfer) {
            return $transfer->from_account === 'cash' ? -$transfer->amount : ($transfer->to_account === 'cash' ? $transfer->amount : 0);
        });

        $data['transferFundTransfer'] += $transferTransfers->sum(function ($transfer) {
            return $transfer->from_account === 'transfer' ? -$transfer->amount : ($transfer->to_account === 'transfer' ? $transfer->amount : 0);
        });

        $data['posFundTransfer'] += $posTransfers->sum(function ($transfer) {
            return $transfer->from_account === 'pos' ? -$transfer->amount : ($transfer->to_account === 'pos' ? $transfer->amount : 0);
        });

        $data['staffss'] = User::where('branch_id',$staff_id)->whereNotIn('usertype', ['customer', 'supplier'])->get();
        $data['staff_id'] =  $staff_id;

        return view('dashboard.dashboard', $data);

    }

}
