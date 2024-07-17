<?php

namespace App\Http\Controllers;

use App\Models\CashCredit;
use App\Models\CashCreditPayment;
use App\Models\Expense;
use App\Models\FundTransfer;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class CashCreditsController extends Controller
{
    public function index()
    {
        $userBranchId = Auth::user()->branch_id;

        $data['customers'] = User::where('usertype', 'customer')->where('branch_id', auth()->user()->branch_id)->get();

        $data['debtors'] = CashCredit::select('customer_id')->whereHas('customer', function ($query) use ($userBranchId) {
            $query->where('branch_id', $userBranchId);
        })->groupBy('customer_id')->get();
        return view('cash_credits.index', $data);
    }

    public function store(Request $request)
    {
        $branch_id = auth()->user()->branch_id;
        
        // Get today's balance for cash
        $balanceInfo = $this->getTodayBalance('cash');
        $currentBalance = $balanceInfo['current_balance'];
        $minimumBalance = $balanceInfo['minimum_balance'];
    
        // Check if there's enough balance
        if ($currentBalance <= 0 || ($currentBalance - $request->amount) < $minimumBalance) {
            Toastr::error('Insufficient funds. Please ensure you maintain a minimum balance of ' . $minimumBalance . '.');
            return redirect()->route('cash_credits.index');
        }
    
        // If balance is sufficient, proceed with creating the cash credit
        $credit = new CashCredit();
        $credit->customer_id = $request->customer_id;
        $credit->cashier_id = auth()->user()->id;
        $credit->branch_id = $branch_id;
        $credit->amount = $request->amount;
        $credit->save();
    
        Toastr::success('Cash Credit Recorded Successfully');
        return redirect()->route('cash_credits.index');
    }


    private function getTodayBalance($paymentMethod)
    {
        $branch_id = auth()->user()->branch_id;
        $today = Carbon::today();

        // Get sales for today
        $sales = Sale::where('branch_id', $branch_id)
            ->whereDate('created_at', $today)
            ->where(function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod)
                    ->orWhere('payment_method', 'multiple');
            })
            ->get();

        // Calculate sales amount
        $salesAmount = $sales->reduce(function ($total, $sale) use ($paymentMethod) {
            if ($sale->payment_method == 'multiple') {
                $paymentAmount = Payment::where('receipt_nos', $sale->receipt_no)
                    ->where('payment_type', 'multiple')
                    ->where('payment_method', $paymentMethod)
                    ->value('payment_amount');
                $total += $paymentAmount ?? 0;
            } else {
                $total += ($sale->price * $sale->quantity) - $sale->discount;
            }
            return $total;
        }, 0);

        // Get returns for today
        $returns = Returns::where('branch_id', $branch_id)
            ->whereDate('created_at', $today)
            ->where('payment_method', $paymentMethod)
            ->sum(DB::raw('price * quantity - discount'));

        // Get expenses for today
        $expenses = Expense::where('branch_id', $branch_id)
            ->whereDate('created_at', $today)
            ->where('payment_method', $paymentMethod)
            ->sum('amount');

        // Get credit payments for today
        $creditPayments = Payment::where('branch_id', $branch_id)
            ->whereDate('created_at', $today)
            ->where('payment_method', $paymentMethod)
            ->where('payment_type', 'credit')
            ->sum('payment_amount');

        // Get deposit payments for today
        $depositPayments = Payment::where('branch_id', $branch_id)
            ->whereDate('created_at', $today)
            ->where('payment_method', $paymentMethod)
            ->where('payment_type', 'deposit')
            ->sum('payment_amount');

        // Get cash credit for today (only applicable for cash)
        $cashCredit = $paymentMethod == 'cash'
        ? CashCredit::where('branch_id', $branch_id)
            ->whereDate('created_at', $today)
            ->sum('amount')
        : 0;

        // Get credit repayments for today
        $creditRepayments = DB::table('cash_credit_payments')
            ->whereDate('created_at', $today)
            ->where('payment_method', $paymentMethod)
            ->sum('amount_paid');

        // Get fund transfers for today
        $fundTransfers = FundTransfer::whereDate('created_at', $today)
            ->where('branch_id', $branch_id)
            ->where(function ($query) use ($paymentMethod) {
                $query->where('from_account', $paymentMethod)
                    ->orWhere('to_account', $paymentMethod);
            })
            ->get()
            ->reduce(function ($total, $transfer) use ($paymentMethod) {
                return $total + ($transfer->from_account === $paymentMethod ? -$transfer->amount : ($transfer->to_account === $paymentMethod ? $transfer->amount : 0));
            }, 0);

        // Calculate total balance
        $balance = $salesAmount - $returns - $expenses + $creditPayments + $depositPayments - $cashCredit + $creditRepayments + $fundTransfers;

        $minimumBalance = 500; // Set the minimum balance

        return [
            'current_balance' => $balance,
            'minimum_balance' => $minimumBalance,
        ];
    }



    public function show($customerId)
    {
        $customer = User::findOrFail($customerId);
        $creditsHistory = CashCredit::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return View::make('cash_credits.credits_history', ['customer' => $customer, 'creditsHistory' => $creditsHistory]);
    }

    public function fetchCreditRecords($customerId)
    {
        $creditRecords = CashCredit::where('customer_id', $customerId)
            ->where(function ($query) {
                $query->where('status', '!=', 'paid')
                    ->orWhereNull('status');
            })
            ->get();

        // Return the credit records as JSON response
        return response()->json(['creditRecords' => $creditRecords]);
    }

    public function processCreditPayment(Request $request)
    {
        $paymentData = $request->input('paymentData');
        $paymentMethod = $request->input('paymentMethod'); 
        foreach ($paymentData as $payment) {
            $credit = CashCredit::findOrFail($payment['creditId']);
    
            if ($payment['paymentType'] === 'no_payment') {
                // Handle no payment (if needed)
            } elseif ($payment['paymentType'] === 'full_payment') {
                // Handle full payment
                $this->handleFullPayment($credit, $paymentMethod); 
            } elseif ($payment['paymentType'] === 'partial_payment') {
                // Handle partial payment
                $partialAmount = floatval($payment['partialAmount']);
                $this->handlePartialPayment($credit, $partialAmount, $paymentMethod); 
            }
        }
    
        return response()->json(['message' => 'Credit payment(s) processed successfully']);
    }
    
   
    protected function handleFullPayment($credit, $paymentMethod)
    {
        $amount = $credit->amount - $credit->amount_paid;

        $credit->update([
            'amount_paid' => $credit->amount,
            'status' => 'paid',
        ]);
    
        $this->createCashCreditPayment($credit->id, $credit->customer_id, $credit->cashier_id, $credit->branch_id, $amount, $paymentMethod);
    }
    
    protected function handlePartialPayment($credit, $partialAmount, $paymentMethod)
    {
        if ($partialAmount > ($credit->amount - $credit->amount_paid)) {
            return response()->json(['error' => 'Partial amount exceeds balance']);
        }
    
        $credit->update([
            'amount_paid' => $credit->amount_paid + $partialAmount,
        ]);
    
        $this->createCashCreditPayment($credit->id, $credit->customer_id,$credit->cashier_id, $credit->branch_id, $partialAmount, $paymentMethod);
    }
    
    protected function createCashCreditPayment($cashCreditsId, $customerId, $cashierId, $branchId, $amountPaid, $paymentMethod)
    {
        CashCreditPayment::create([
            'cash_credits_id' => $cashCreditsId,
            'customer_id' => $customerId,
            'cashier_id' => $cashierId,
            'branch_id' => $branchId,
            'amount_paid' => $amountPaid,
            'payment_method' => $paymentMethod,
        ]);
    }
    

}
