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
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function cashier()
    {
        return redirect()->route('sales.index');

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
            $todaySales = Sale::where('branch_id', $branch_id)->whereNotIn('stock_id', [1093, 1012])->whereDate('created_at', $request->date)->get();
            $todayReturns = Returns::where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $todayExpenses = Expense::where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $creditPayments = Payment::where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $estimates = Estimate::where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $purchases = Purchase::select('stock_id', 'quantity')->where('branch_id', $branch_id)->whereDate('created_at', $request->date)->get();
            $data['date'] = $request->date;
        }else
        {
            $todaySales = Sale::where('branch_id', $branch_id)->whereNotIn('stock_id', [1093, 1012])->whereDate('created_at', today())->get();
            $todayReturns = Returns::where('branch_id', $branch_id)->whereDate('created_at', today())->get();
            $todayExpenses = Expense::where('branch_id', $branch_id)->whereDate('created_at', today())->get();
            $creditPayments = Payment::where('branch_id', $branch_id)->whereDate('created_at', today())->get();
            $estimates = Estimate::where('branch_id', $branch_id)->whereDate('created_at', today())->get();
            $purchases = Purchase::select('stock_id', 'quantity')->where('branch_id', $branch_id)->whereDate('created_at', today())->get();
        }
        $data['deposits'] = Payment::select('payment_amount')->where('branch_id', $branch_id)->where('payment_type', 'deposit')->sum('payment_amount');

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

        $data['returnProfit'] = $todayReturns->sum(function($return) {
            return (($return->price - $return->product->buying_price) * $return->quantity);
        });
       
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




        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(6);
        
        $salesData = Sale::where('branch_id', $branch_id)
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->selectRaw('date(created_at) as date, sum(price * quantity - discount) as revenue')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();
                        
        $data['dates'] = $salesData->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->shortDayName;
        });
        
        $data['revenues'] = $salesData->pluck('revenue');

        $salesData = Sale::where('branch_id', $branch_id)
                    ->whereNotIn('stock_id', [1093, 1012])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->select('stock_id', DB::raw('SUM(quantity) as total_quantity'))
                    ->groupBy('stock_id')
                    ->orderBy('total_quantity', 'DESC')
                    ->take(10)
                    ->get();
                   
        $data['labels'] = $salesData->pluck('product.name');
        $data['values'] = $salesData->pluck('total_quantity');



        $salesByTime = DB::table('sales')
        ->select(DB::raw('HOUR(created_at) AS hour'), DB::raw('SUM(price*quantity - discount) AS amount'))
        ->whereDate('created_at', Carbon::today())
        ->where('branch_id', $branch_id)
        ->groupBy(DB::raw('HOUR(created_at)'))
        ->orderBy(DB::raw('HOUR(created_at)'))
        ->get();

    $chartData = [
        'labels' => [],
        'data' => [],
    ];

    // Prepare chart data
    foreach ($salesByTime as $sale) {
        $hour = Carbon::createFromFormat('H', $sale->hour)->format('ga');
        $chartData['labels'][] = $hour;
        $chartData['data'][] = $sale->amount;
    }

    $data['chartData'] = $chartData;


    //////////////


    $yesterday = Carbon::yesterday();

    $salesByBranch = DB::table('sales')
        ->join('branches', 'sales.branch_id', '=', 'branches.id')
        ->select('branches.name', DB::raw('SUM(price * quantity - discount) AS revenue'))
        ->whereDate('sales.created_at', $yesterday)
        ->groupBy('sales.branch_id')
        ->get();

    $pieChartData = [
        'labels' => [],
        'data' => [],
        'backgroundColor' => [],
    ];

    // Prepare chart data
    foreach ($salesByBranch as $sale) {
        $pieChartData['labels'][] = $sale->name;
        $pieChartData['data'][] = $sale->revenue;
        $pieChartData['backgroundColor'][] = '#' . substr(md5(rand()), 0, 6); // Generate random color for each branch
    }


    $data['pieChartData'] = $pieChartData;


        return view('admin', $data);

    }


}
