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
}
