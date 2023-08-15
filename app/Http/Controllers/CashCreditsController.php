<?php

namespace App\Http\Controllers;

use App\Models\CashCredit;
use App\Models\CashCreditPayment;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $todaySales = Sale::where('branch_id', $branch_id)->where('payment_method', 'cash')->whereNotIn('stock_id', [1093, 1012])->whereDate('created_at', today())->get();
        $todayReturns = Returns::where('branch_id', $branch_id)->where('payment_method', 'cash')->whereDate('created_at', today())->get();
        $expenses = Expense::where('branch_id', $branch_id)->where('payment_method', 'cash')->whereDate('created_at', today())->sum('amount');
        $payments = Payment::where('branch_id', $branch_id)->where('payment_method', 'cash')->whereDate('created_at', today())->sum('payment_amount');

        $sales = $todaySales->reduce(function ($total, $sale) {
            return $total + ($sale->price * $sale->quantity) - $sale->discount;
        }, 0);

        $returns = $todayReturns->reduce(function ($total, $return) {
            return $total + ($return->price * $return->quantity) + $return->discount;
        }, 0);

        $net_amount = (float) $sales + (float) $payments - ((float) $returns + (float) $expenses);

        if ((float) $request->amount > (float) $net_amount) {
            Toastr::error('No enough Cash');
            return redirect()->route('cash_credits.index');
        }

        $credit = new CashCredit();
        $credit->customer_id = $request->customer_id;
        $credit->branch_id = auth()->user()->branch_id;
        $credit->amount = $request->amount;
        $credit->save();

        Toastr::success('Cash Credit Recorded Successfully');
        return redirect()->route('cash_credits.index');
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
    
        $this->createCashCreditPayment($credit->id, $credit->customer_id, $credit->branch_id, $amount, $paymentMethod);
    }
    
    protected function handlePartialPayment($credit, $partialAmount, $paymentMethod)
    {
        if ($partialAmount > ($credit->amount - $credit->amount_paid)) {
            return response()->json(['error' => 'Partial amount exceeds balance']);
        }
    
        $credit->update([
            'amount_paid' => $credit->amount_paid + $partialAmount,
        ]);
    
        $this->createCashCreditPayment($credit->id, $credit->customer_id, $credit->branch_id, $partialAmount, $paymentMethod);
    }
    
    protected function createCashCreditPayment($cashCreditsId, $customerId, $branchId, $amountPaid, $paymentMethod)
    {
        CashCreditPayment::create([
            'cash_credits_id' => $cashCreditsId,
            'customer_id' => $customerId,
            'branch_id' => $branchId,
            'amount_paid' => $amountPaid,
            'payment_method' => $paymentMethod,
        ]);
    }
    

}
