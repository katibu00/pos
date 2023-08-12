<?php

namespace App\Http\Controllers;

use App\Models\CashCredit;
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

        $data['customers'] = User::where('usertype','customer')->where('branch_id', auth()->user()->branch_id)->get();
      
        $data['debtors'] = CashCredit::select('customer_id')->whereHas('customer', function ($query) use ($userBranchId) {
            $query->where('branch_id', $userBranchId);
        })->groupBy('customer_id')->get();
        return view('cash_credits.index', $data);
    }

    public function store(Request $request)
    {
        $credit = new CashCredit();
        $credit->customer_id = $request->customer_id;
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

        foreach ($paymentData as $payment) {
            $credit = CashCredit::findOrFail($payment['creditId']);

            if ($payment['paymentType'] === 'no_payment') {
                // Handle no payment (if needed)
            } elseif ($payment['paymentType'] === 'full_payment') {
                // Handle full payment
                $this->handleFullPayment($credit);
            } elseif ($payment['paymentType'] === 'partial_payment') {
                // Handle partial payment
                $partialAmount = floatval($payment['partialAmount']);
                $this->handlePartialPayment($credit, $partialAmount);
            }
        }

        return response()->json(['message' => 'Credit payment(s) processed successfully']);
    }

    protected function handleFullPayment($credit)
    {
        $credit->update([
            'amount_paid' => $credit->amount, 
            'status' => 'paid', 
        ]);
    }

    protected function handlePartialPayment($credit, $partialAmount)
    {
        if ($partialAmount > ($credit->amount - $credit->amount_paid)) {
            return response()->json(['error' => 'Partial amount exceeds balance']);
        }

        $credit->update([
            'amount_paid' => $credit->amount_paid + $partialAmount, 
        ]);
    }


}
