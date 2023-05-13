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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $data['branches'] = Branch::all();
        return view('reports.index', $data);
    }

    public function generate(Request $request)
    {
        // dd($request->all());
        if ($request->report == 'general') {
            // Fetch sales data
            $sales = Sale::where('branch_id', $request->branch_id)->whereNotIn('stock_id', [1093, 1012]);

            if ($request->date == 'today') {
                $sales = $sales->whereDate('created_at', now()->format('Y-m-d'));
            } else if ($request->date == 'week') {
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
                $sales = $sales->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            } else if ($request->date == 'month') {
                $sales = $sales->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month);
            } else if ($request->date == 'range' && $request->has('start_date') && $request->has('end_date')) {
                $sales = $sales->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }
            $data['total_sales_value'] = $sales->sum(DB::raw('(price * quantity)'));

            $data['total_discount'] = $sales->sum('discount');

            $data['start_date'] = $request->start_date;
            $data['end_date'] = $request->end_date;

           // Fetch expenses data
            $expenses = Expense::where('branch_id', $request->branch_id);

            if ($request->date == 'today') {
                $expenses = $expenses->whereDate('created_at', now()->format('Y-m-d'));
            } else if ($request->date == 'week') {
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
                $expenses = $expenses->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            } else if ($request->date == 'month') {
                $expenses = $expenses->whereYear('created_at', now()->year)
                                    ->whereMonth('created_at', now()->month);
            } else if ($request->date == 'range' && $request->has('start_date') && $request->has('end_date')) {
                $expenses = $expenses->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            $data['total_expenses_value'] = $expenses->sum('amount');
            $data['total_expenses_count'] = $expenses->count();



            $returns = Returns::where('branch_id', $request->branch_id);

            if ($request->date == 'today') {
                $returns = $returns->whereDate('created_at', now()->format('Y-m-d'));
            } else if ($request->date == 'week') {
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
                $returns = $returns->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            } else if ($request->date == 'month') {
                $returns = $returns->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month);
            } else if ($request->date == 'range' && $request->has('start_date') && $request->has('end_date')) {
                $returns = $returns->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            $totalValue = $returns->sum(DB::raw('(price * quantity) -discount'));

            $data['total_returns_value'] = $totalValue;
            $data['total_returns_discount'] = $returns->sum('discount');

            $data['returns_profit'] = 0;
            foreach ($returns->get() as $return) {
                $data['returns_profit'] += @$return->quantity * (@$return->price - @$return->product->buying_price);
            }

           // Fetch payments data
            $payments = Payment::where('branch_id', $request->branch_id);

            if ($request->date == 'today') {
                $payments = $payments->whereDate('created_at', now()->format('Y-m-d'));
            } else if ($request->date == 'week') {
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
                $payments = $payments->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            } else if ($request->date == 'month') {
                $payments = $payments->whereYear('created_at', now()->year)
                                    ->whereMonth('created_at', now()->month);
            } else if ($request->date == 'range' && $request->has('start_date') && $request->has('end_date')) {
                $payments = $payments->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            $data['total_payments_value'] = $payments->sum('payment_amount');

            // Fetch stocks data
            $stocks = Stock::where('branch_id', $request->branch_id)->where('quantity', '>', 0)->where('id','!=',1012)->get();
            $data['stock_value'] = $stocks->sum(function ($stock) {
                return @$stock->quantity * @$stock->buying_price;
            });


            $data['gross_sales_profit'] = 0;
            foreach ($sales->get() as $sale) {
                $data['gross_sales_profit'] += @$sale->quantity * (@$sale->price - @$sale->product->buying_price);
            }

            $data['totalCreditsOwed'] = User::where('branch_id', $request->branch_id)->sum('balance');


        }


        if ($request->report == 'best_selling') {

            $branchId = $request->input('branch_id');
            $reportType = $request->input('report');
            $date = $request->input('date');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $amount = $request->input('amount');

            
            $query = Sale::select('stock_id', DB::raw('SUM(quantity) as total_quantity'))
            ->where('branch_id', $branchId)
            ->whereNotIn('stock_id', [1093, 1012])
            ->groupBy('stock_id')
            ->orderBy('total_quantity', 'desc');

        // Apply date range if selected
        if ($date == 'range') {
            $query->whereBetween('created_at', [$startDate, $endDate]);
            $data['start_date'] = $startDate;
            $data['end_date'] = $endDate;

        }

        // Apply amount limit if selected
        if ($amount) {
            $query->take($amount);
        }

        $bestSellingItems = $query->get();

        // Calculate total sales
        $totalSales = Sale::where('branch_id', $branchId)
                            ->whereNotIn('stock_id', [1093, 1012])

                            ->when($date == 'range', function ($query) use ($startDate, $endDate) {
                                $query->whereBetween('created_at', [$startDate, $endDate]);
                            })
                            ->sum('quantity');

        // Retrieve stock information
        $bestSellingItems->load('product');

        // Calculate percentage of total sales
        foreach ($bestSellingItems as $item) {
            $item->percentage_of_total_sales = ($item->total_quantity / $totalSales) * 100;
        }


        $data['bestSellingItems'] = $bestSellingItems;
        $data['amount'] = $amount;

        $data['itemNames'] = $bestSellingItems->pluck('product.name');
        $data['quantitiesSold'] = $bestSellingItems->pluck('total_quantity');



        }

        if ($request->report == 'gross' && $request->date == 'today') {
            $data['sales'] = Sale::select('receipt_no')->where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->groupBy('receipt_no')->latest()->get();
        }
        if ($request->report == 'gross' && $request->date == 'week') {
            $data['sales'] = Sale::select('receipt_no')->where('branch_id', $request->branch_id)->whereDate('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->groupBy('receipt_no')->latest()->get();
        }
        if ($request->report == 'gross' && $request->date == 'month') {
            $data['sales'] = Sale::select('receipt_no')->where('branch_id', $request->branch_id)->whereMonth('created_at', Carbon::now()->month)->groupBy('receipt_no')->latest()->get();
        }
        if ($request->report == 'gross' && $request->date == 'range') {
            $data['sales'] = Sale::select('receipt_no')->where('branch_id', $request->branch_id)->whereBetween('created_at', [$request->start, $request->end])->groupBy('receipt_no')->latest()->get();
        }

        if ($request->report == 'inventory' && $request->date == 'today') {
            $data['inventories'] = Sale::select('stock_id')->where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->groupBy('stock_id')->get();
            $data['frame'] = $request->date;
        }
        if ($request->report == 'inventory' && $request->date == 'week') {
            $data['inventories'] = Sale::select('stock_id')->where('branch_id', $request->branch_id)->whereDate('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->groupBy('stock_id')->get();
            $data['frame'] = $request->date;
        }
        if ($request->report == 'inventory' && $request->date == 'month') {
            $data['inventories'] = Sale::select('stock_id')->where('branch_id', $request->branch_id)->whereMonth('created_at', Carbon::now()->month)->groupBy('stock_id')->get();
            $data['frame'] = $request->date;
        }
        if ($request->report == 'inventory' && $request->date == 'range') {
            $data['inventories'] = Sale::select('stock_id')->where('branch_id', $request->branch_id)->whereBetween('created_at', [$request->start, $request->end])->groupBy('stock_id')->get();
            $data['frame'] = $request->date;
            $data['start'] = $request->start;
            $data['end'] = $request->end;
        }

        if ($request->report == 'returns' && $request->date == 'today') {
            $data['returns'] = Returns::where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->get();
            $data['frame'] = $request->date;
        }

        if ($request->report == 'returns' && $request->date == 'week') {
            $data['returns'] = Returns::where('branch_id', $request->branch_id)->whereDate('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
            $data['frame'] = $request->date;
        }
        if ($request->report == 'returns' && $request->date == 'month') {
            $data['returns'] = Returns::where('branch_id', $request->branch_id)->whereMonth('created_at', Carbon::now()->month)->get();
            $data['frame'] = $request->date;
        }

        if ($request->report == 'returns' && $request->date == 'range') {
            $data['returns'] = Returns::where('branch_id', $request->branch_id)->whereBetween('created_at', [$request->start, $request->end])->get();
            $data['frame'] = $request->date;
            $data['start'] = $request->start;
            $data['end'] = $request->end;
        }

        if ($request->report == 'today') {

            $todays = Sale::where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->get();

            $todays_total = 0;
            $todays_discount = 0;
            foreach ($todays as $today) {
                $sum1 = $today['product']['selling_price'] * $today->quantity;
                $todays_total += $sum1;
                $todays_discount += $today->discount;
            }
            $data['gross'] = $todays_total;
            $data['discount'] = $todays_discount;
            $data['sales_count'] = Sale::select('receipt_no')->where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->groupBy('receipt_no')->get()->count();
            $data['items_sold'] = Sale::select('receipt_no')->where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->get()->count();
            $purchases = Purchase::select('stock_id', 'quantity')->where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->get();

            $todays_purchases = 0;
            foreach ($purchases as $purchase) {
                $todays_purchases += $purchase['product']['buying_price'] * $purchase->quantity;
            }
            $data['todays_purchases'] = $todays_purchases;

            //estimate
            $estimates = Estimate::where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->get();

            $todays_estimate = 0;
            foreach ($estimates as $estimate) {
                $todays_estimate += $estimate['product']['selling_price'] * $estimate->quantity - $estimate->discount;

            }
            $data['todays_estimate'] = $todays_estimate;
            $data['estimate_count'] = Estimate::select('estimate_no')->where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->groupBy('estimate_no')->get()->count();

            //return
            $returns = Returns::where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->get();

            $todays_returns = 0;
            foreach ($returns as $return) {
                $todays_returns += $return->price * $return->quantity;

            }
            $data['todays_returns'] = $todays_returns;
            $data['returns_count'] = Returns::select('return_no')->where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->groupBy('return_no')->get()->count();

            $lows = 0;
            $total_stock = 0;
            $stocks = Stock::where('branch_id', $request->branch_id)->get();
            foreach ($stocks as $stock) {

                if ($stock->quantity <= $stock->critical_level) {
                    $lows++;
                }
                $total_stock++;
            }
            $data['lows'] = $lows;
            $data['total_stock'] = $total_stock;

        }

        $data['branches'] = Branch::all();
        $data['report'] = $request->report;
        $data['date'] = $request->date;
        $data['branch_id'] = $request->branch_id;
        return view('reports.index', $data);

    }
}
