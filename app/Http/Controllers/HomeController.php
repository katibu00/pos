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
            $todaySales = Sale::where('branch_id', $branch_id)->where('stock_id','!=',1000)->whereDate('created_at', $request->date)->get();
            $todayReturns = Returns::where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $todayExpenses = Expense::where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $creditPayments = Payment::where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $estimates = Estimate::where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $purchases = Purchase::select('stock_id', 'quantity')->where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $data['date'] = $request->date;
        }else
        {
            $todaySales = Sale::where('branch_id', $branch_id)->where('stock_id','!=',1000)->whereDate('created_at', today())->get();
            $todayReturns = Returns::where('branch_id', $branch_id)->whereDate('created_at', today())->get();
            $todayExpenses = Expense::where('branch_id', $branch_id)->whereDate('created_at', today())->get();
            $creditPayments = Payment::where('branch_id', $branch_id)->whereDate('created_at', today())->get();
            $estimates = Estimate::where('branch_id', $branch_id)->whereDate('created_at', today())->get();
            $purchases = Purchase::select('stock_id', 'quantity')->where('branch_id', $branch_id)->whereDate('created_at', today())->get();
        }
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
            return (($sale->price - $sale->product->buying_price) * $sale->quantity) - $sale->discount;
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


    public function adminaaa(Request $request)
    {
        $data['branches'] = Branch::all();
        $branch_id = auth()->user()->branch_id;
        $todays = Sale::where('branch_id', $branch_id)->where('stock_id','!=',1000)->whereDate('created_at', Carbon::today())->get();

        $todays_gross = 0;
        $todays_cash = 0;
        $todays_pos = 0;
        $todays_credit = 0;
        $todays_transfer = 0;
        $todays_discount = 0;
        $todays_net_sales = 0;
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
            $todays_net_sales += ($today->price - $today->product->buying_price)*$today->quantity - $today->discount;
        }
        $data['todays_gross'] = $todays_gross;
        $data['discounts'] = $todays_discount;
        $data['todays_net_sales'] = $todays_net_sales;

        $data['todays_pos'] = $todays_pos;
        $data['todays_cash'] = $todays_cash;
        $data['todays_credit'] = $todays_credit;
        $data['todays_transfer'] = $todays_transfer;

        $data['sales_count'] = Sale::select('receipt_no')->where('stock_id','!=',1000)->where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->groupBy('receipt_no')->get()->count();
        $data['items_sold'] = Sale::select('receipt_no')->where('stock_id','!=',1000)->where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->get()->count();
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
        $todays_returns_pos = 0;
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
            if($return->payment_method == 'pos')
            {
                $todays_returns_pos += $return->price * $return->quantity-$return->discount;
            }
            $todays_returns_discounts += $return->discount;

        }
        $data['todays_returns_cash'] = $todays_returns_cash;
        $data['todays_returns_bank'] = $todays_returns_bank;
        $data['todays_returns_pos'] = $todays_returns_pos;
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
    
        $data['credit_pos'] = 0;
        $data['credit_transfer'] = 0;
        $data['credit_cash'] = 0;
        $data['credit_all'] = 0;
    
        $credit_payments = Payment::select(['payment_amount','payment_method'])->where('branch_id', $branch_id)->whereDate('created_at', Carbon::today())->get();
        
        foreach ($credit_payments as $payment) {
            if($payment->payment_method == 'cash')
            {
                $data['credit_cash'] += $payment->payment_amount;
            }
            if($payment->payment_method == 'transfer')
            {
                $data['credit_transfer'] += $payment->payment_amount;
            }
            if($payment->payment_method == 'pos')
            {
                $data['credit_pos'] += $payment->payment_amount;
            }
            $data['credit_all'] += $payment->payment_amount;
        }

        return view('admin', $data);

    }

}
