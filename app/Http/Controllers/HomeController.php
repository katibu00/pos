<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Estimate;
use App\Models\Expense;
use App\Models\Payment;
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

        //quries
        if(isset($request->date))
        {
            $todaySales = Sale::where('branch_id', $branch_id)->where('stock_id','!=',1012)->whereDate('created_at', $request->date)->get();
            $todayReturns = Returns::where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $todayExpenses = Expense::where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $creditPayments = Payment::where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $estimates = Estimate::where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $purchases = Purchase::select('stock_id', 'quantity')->where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $data['date'] = $request->date;
        }else
        {
            $todaySales = Sale::where('branch_id', $branch_id)->where('stock_id','!=',1012)->whereDate('created_at', today())->get();
            $todayReturns = Returns::where('branch_id', $branch_id)->whereDate('created_at', today())->get();
            $todayExpenses = Expense::where('branch_id', $branch_id)->whereDate('created_at', today())->get();
            $creditPayments = Payment::where('branch_id', $branch_id)->whereDate('created_at', today())->get();
            $estimates = Estimate::where('branch_id', $branch_id)->whereDate('created_at', today())->get();
            $purchases = Purchase::select('stock_id', 'quantity')->where('branch_id', $branch_id)->whereDate('created_at', today())->get();
        }
        $data['totalDiscounts'] = $todaySales->sum('discount');
        //sales 
        $data['grossSales'] = $todaySales->sum(function($sale) {
            return $sale->price * $sale->quantity;
        });
        $data['totalDiscount'] = $todaySales->sum('discount');
        $data['posSales'] = $todaySales->where('payment_method', 'pos')->reduce(function ($total, $sale) {
            $total += ($sale->price * $sale->quantity) - $sale->discount;
            return $total;
        }, 0);
        $data['cashSales'] = $todaySales->where('payment_method', 'cash')->reduce(function ($total, $sale) {
            $total += ($sale->price * $sale->quantity) - $sale->discount;
            return $total;
        }, 0);
        $data['transferSales'] = $todaySales->where('payment_method', 'transfer')->reduce(function ($total, $sale) {
            $total += ($sale->price * $sale->quantity) - $sale->discount;
            return $total;
        }, 0);
        $data['creditSales'] = $todaySales->where('payment_method', 'credit')->reduce(function ($total, $sale) {
            $total += ($sale->price * $sale->quantity) - $sale->discount;
            return $total;
        }, 0);
        $data['grossProfit'] = $todaySales->sum(function($sale) {
            return (($sale->price - $sale->product->buying_price) * $sale->quantity);
        });
        $data['uniqueSalesCount'] = @$todaySales->unique('receipt_no')->count();
        $data['totalItemsSold'] = $todaySales->sum('quantity');
        //returns
        $data['totalReturn'] = $todayReturns->sum(function($return) {
            return ($return->price * $return->quantity) - $return->discount;
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
        //Expenses
        $data['totalExpenses'] = $todayExpenses->sum('amount');
        $data['cashExpenses'] = $todayExpenses->where('payment_method', 'cash')->sum('amount');
        $data['posExpenses'] = $todayExpenses->where('payment_method', 'pos')->sum('amount');
        $data['transferExpenses'] = $todayExpenses->where('payment_method', 'transfer')->sum('amount');
        //credit Payments
        $data['totalCreditPayments'] = $creditPayments->sum('payment_amount');
        $data['cashCreditPayments'] = $creditPayments->where('payment_method', 'cash')->sum('payment_amount');
        $data['posCreditPayments'] = $creditPayments->where('payment_method', 'POS')->sum('payment_amount');
        $data['transferCreditPayments'] = $creditPayments->where('payment_method', 'transfer')->sum('payment_amount');
        //estimates
        $data['totalEstimate'] = $estimates->sum(function($estimate) {
            return ($estimate->price * $estimate->quantity) - $estimate->discount;
        });
        //purchases
        $data['totalPurchases'] = $purchases->sum(function($purchase) {
            return $purchase['product']['buying_price'] * $purchase->quantity;
        });
        $stocks = Stock::where('branch_id', $branch_id)
               ->where('quantity', '<=', 'critical_level')
               ->get();
        $data['lows'] = count($stocks);
        $data['total_stock'] = Stock::select('id')->where('branch_id', $branch_id)->count();
        return view('admin', $data);

    }


}
