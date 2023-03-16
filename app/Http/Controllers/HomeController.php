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

        $todaySales = Sale::where('branch_id', $branch_id)->where('stock_id','!=',1000)->whereDate('created_at', today())->get();

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

        $data['uniqueSalesCount'] = @$todaySales->unique('receipt_no')->count();
        $data['totalItemsSold'] = $todaySales->sum('quantity');
        // dd( $data['transferSales']);
        // dd($uniqueSales->count());

        $stocks = Stock::where('branch_id', $branch_id)
               ->where('quantity', '<=', 'critical_level')
               ->get();
        $data['lows'] = count($stocks);
        $data['total_stock'] = Stock::select('id')->where('branch_id', $branch_id)->count();
       
       
    

        return view('admin', $data);

    }

}
