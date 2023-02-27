<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Estimate;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Brian2694\Toastr\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function adminaa()
    {
        $branch_id = auth()->user()->branch_id;
        // $todays = Sale::where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->get();
        // $data['discounts'] = Sale::where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->sum('discount');
        // $data['branches'] = Branch::all();

        // $todays_total = 0;
        // foreach($todays as $today)
        // {
        //     $sum1 = $today['product']['selling_price']*$today->quantity - $today->discount;
        //     $todays_total += $sum1;
        // }

        // $weeks = Sale::where('branch_id', $branch_id)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();

        // $weeks_total = 0;
        // foreach($weeks as $week)
        // {
        //     $sum2 = $week['product']['selling_price']*$week->quantity;
        //     $weeks_total += $sum2;
        // }

        // $data['todays_total'] = $todays_total;
        // $data['weeks_total'] = $weeks_total;

        $lows = [];
        $stocks = Stock::all();
        foreach ($stocks as $stock) {

            if ($stock->quantity <= $stock->critical_level) {
                array_push($lows, $stock);
            }
        }
        $data['lows'] = $lows;
        return view('admin', $data);
    }

    public function cashier()
    {

        $todays = Sale::where('branch_id', Auth::user()->branch_id)->whereDate('created_at', Carbon::today())->get();

        $todays_total = 0;
        foreach ($todays as $today) {
            $sum1 = $today['product']['selling_price'] * $today->quantity;
            $todays_total += $sum1;
        }
        $data['todays_total'] = $todays_total;
        return view('cashier', $data);
    }

    public function change_branch(Request $request)
    {

        if ($request->branch_id == '') {
            return redirect()->back();
            Toastr::error("Branch is not selected");
        }
        $user = User::find(auth()->user()->id);
        $user->branch_id = $request->branch_id;
        $user->update();
        return redirect()->route('admin.home');
    }

    public function admin(Request $request)
    {
        $data['branches'] = Branch::all();
        $branch_id = auth()->user()->branch_id;
        $todays = Sale::where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->get();

        $todays_gross = 0;
        $todays_cash = 0;
        $todays_pos = 0;
        $todays_credit = 0;
        $todays_transfer = 0;
        $todays_discount = 0;
        foreach ($todays as $today) {
            $gross = $today->price * $today->quantity;
            $todays_gross += $gross;
            $todays_discount += $today->discount;
            if($today->payment_method == 'cash')
            {
                $todays_cash += $today->price * $today->quantity - $today->discount;
            }
            if($today->payment_method == 'pos')
            {
                $todays_pos += $today->price * $today->quantity - $today->discount;
            }
            if($today->payment_method == 'transfer')
            {
                $todays_transfer += $today->price * $today->quantity - $today->discount ;
            }
            if($today->payment_method == 'credit')
            {
                $todays_credit += $today->price * $today->quantity - $today->discount ;
            }
        }
        $data['todays_gross'] = $todays_gross;
        $data['discounts'] = $todays_discount;

        $data['todays_pos'] = $todays_pos;
        $data['todays_cash'] = $todays_cash;
        $data['todays_credit'] = $todays_credit;
        $data['todays_transfer'] = $todays_transfer;

        $data['sales_count'] = Sale::select('receipt_no')->where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->groupBy('receipt_no')->get()->count();
        $data['items_sold'] = Sale::select('receipt_no')->where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->get()->count();
        $purchases = Purchase::select('stock_id', 'quantity')->where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->get();

        $todays_purchases = 0;
        foreach ($purchases as $purchase) {
            $todays_purchases += $purchase['product']['buying_price'] * $purchase->quantity;
        }
        $data['todays_purchases'] = $todays_purchases;

        //estimate
        $estimates = Estimate::where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->get();

        $todays_estimate = 0;
        foreach ($estimates as $estimate) {
            $todays_estimate += $estimate['product']['selling_price'] * $estimate->quantity - $estimate->discount;

        }
        $data['todays_estimate'] = $todays_estimate;
        $data['estimate_count'] = Estimate::select('estimate_no')->where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->groupBy('estimate_no')->get()->count();

        //return
        $returns = Returns::where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->get();

        $todays_returns_cash = 0;
        $todays_returns_bank = 0;
        $todays_returns_discounts = 0;
        foreach ($returns as $return) {
            if($return->payment_method == 'cash')
            {
                $todays_returns_cash += $return->price * $return->quantity-$return->discount;
            }
            if($return->payment_method == 'transfer')
            {
                $todays_returns_bank += $return->price * $return->quantity-$return->discount;
            }
            $todays_returns_discounts += $return->discount;

        }
        $data['todays_returns_cash'] = $todays_returns_cash;
        $data['todays_returns_bank'] = $todays_returns_bank;
        $data['todays_returns_discounts'] = $todays_returns_discounts;
        $data['returns_count'] = Returns::select('return_no')->where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->groupBy('return_no')->get()->count();
        $expenses = Expense::where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->get();
       
        $todays_expense_cash = 0;
        $todays_expense_bank = 0;

        foreach ($expenses as $expense) {
            if($expense->payment_method == 'cash')
            {
                $todays_expense_cash += $expense->amount;
            }
            if($expense->payment_method == 'transfer')
            {
                $todays_expense_bank += $expense->amount;
            }
        }
        $data['todays_expense_cash'] = $todays_expense_cash;
        $data['todays_expense_bank'] = $todays_expense_bank;
        
        $lows = 0;
        $total_stock = 0;
        $stocks = Stock::where('branch_id', $branch_id)->get();
        foreach ($stocks as $stock) {

            if ($stock->quantity <= $stock->critical_level) {
                $lows++;
            }
            $total_stock++;
        }
        $data['lows'] = $lows;
        $data['total_stock'] = $total_stock;
        return view('admin', $data);

    }

}
