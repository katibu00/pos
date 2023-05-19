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
use Brian2694\Toastr\Facades\Toastr;
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
                $data['returns_profit'] += @$return->quantity * (@$return->price-@$return->product->buying_price);
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
            $stocks = Stock::where('branch_id', $request->branch_id)->where('quantity', '>', 0)->whereNotIn('id', [1093, 1012])->get();
            $data['stock_value'] = $stocks->sum(function ($stock) {
                return @$stock->quantity * @$stock->buying_price;
            });

            $data['gross_sales_profit'] = 0;
            foreach ($sales->get() as $sale) {
                $data['gross_sales_profit'] += @$sale->quantity * (@$sale->price-@$sale->product->buying_price);
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

        if ($request->report == 'inventory') {

            $branchId = $request->input('branch_id');
            $date = $request->input('date');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $inventoryIds = $request->input('inventory_id');

            if (empty($inventoryIds)) {
                Toastr::error('Please select at least one inventory item.');
                return redirect()->route('report.index');
            }

            $query = DB::table('stocks')->where('branch_id', $branchId);

            if (!empty($inventoryIds)) {
                $query->whereIn('id', $inventoryIds);
            }

            $inventoryItems = $query->get();

            foreach ($inventoryItems as $item) {
                $totalQuantitySold = DB::table('sales')
                    ->where('branch_id', $branchId)
                    ->where('stock_id', $item->id)
                    ->when($date === 'today', function ($query) {
                        return $query->whereDate('created_at', today());
                    })
                    ->when($date === 'week', function ($query) {
                        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    })
                    ->when($date === 'month', function ($query) {
                        return $query->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month);
                    })
                    ->when($date === 'range', function ($query) use ($startDate, $endDate) {
                        return $query->whereBetween('created_at', [$startDate, $endDate]);
                    })
                    ->sum('quantity');

                $salesRevenue = DB::table('sales')
                    ->where('branch_id', $branchId)
                    ->where('stock_id', $item->id)
                    ->when($date === 'today', function ($query) {
                        return $query->whereDate('created_at', today());
                    })
                    ->when($date === 'week', function ($query) {
                        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    })
                    ->when($date === 'month', function ($query) {
                        return $query->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month);
                    })
                    ->when($date === 'range', function ($query) use ($startDate, $endDate) {
                        return $query->whereBetween('created_at', [$startDate, $endDate]);
                    })
                    ->sum(DB::raw('price * quantity'));

                $costOfGoodsSold = $item->buying_price * $totalQuantitySold;
                $grossProfit = $salesRevenue - $costOfGoodsSold;

                $profitMargin = $salesRevenue != 0 ? ($grossProfit / $salesRevenue) * 100 : 0;

                $item->total_quantity_sold = $totalQuantitySold;
                $item->sales_revenue = $salesRevenue;
                $item->gross_profit = $grossProfit;
                $item->profit_margin = $profitMargin;
                $data['inventoryItems'] = $inventoryItems;
                if ($date == 'range') {
                    $data['start_date'] = $startDate;
                    $data['end_date'] = $endDate;
                }

            }

            $datas = [];
            foreach ($inventoryIds as $inventoryId) {
                $inventoryData = DB::table('sales')
                    ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(quantity) as total_quantity_sold'))
                    ->where('stock_id', $inventoryId)
                    ->whereYear('created_at', now()->year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total_quantity_sold', 'month')
                    ->all();

                // Fetch the inventory name
                $inventoryName = DB::table('stocks')->where('id', $inventoryId)->value('name');

                $datas[] = [
                    'inventoryName' => $inventoryName,
                    'inventoryData' => $inventoryData,
                ];

            }

            $data['datas'] = $datas;
            $data['year'] = date('Y');

        }

        if ($request->report == 'worst_selling') {

            $branchId = $request->input('branch_id');
            $date = $request->input('date');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $amount = $request->input('amount');

            $query = Sale::select('stock_id', DB::raw('SUM(quantity) as total_quantity'))
                ->where('branch_id', $branchId)
                ->whereNotIn('stock_id', [1093, 1012])
                ->groupBy('stock_id')
                ->orderBy('total_quantity');

            // Apply date range if selected
            if ($date == 'range') {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            // Apply amount limit if selected
            if ($amount) {
                $query->take($amount);
            }

            $worstSellingItems = $query->get();

            // Calculate total sales
            $totalSales = Sale::where('branch_id', $branchId)
                ->whereNotIn('stock_id', [1093, 1012])
                ->when($date == 'range', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->sum('quantity');

            // Retrieve stock information
            $worstSellingItems->load('product');

            // Calculate percentage of total sales
            foreach ($worstSellingItems as $item) {
                $item->percentage_of_total_sales = ($item->total_quantity / $totalSales) * 100;
            }

            $data['worstSellingItems'] = $worstSellingItems;
            $data['amount'] = $amount;

            $data['itemNames'] = $worstSellingItems->pluck('product.name');
            $data['quantitiesSold'] = $worstSellingItems->pluck('total_quantity');
        }

        if ($request->report == 'compare_branches') {

            // Retrieve all branches
            $branches = Branch::all();

            // Get the date 30 days ago from today
            $startDate = Carbon::now()->subDays($request->duration)->startOfDay();

            // Initialize arrays to store comparison data
            $grossSales = [];
            $expenses = [];
            $returns = [];
            $creditsOwed = [];
            $discounts = [];
            $netProfit = [];
            $avgTransactionValue = [];
            $inventoryTurnover = [];

            // Calculate and store data for each branch
            foreach ($branches as $branch) {
                $branchId = $branch->id;

                // Calculate gross sales
                $grossSales[$branchId] = Sale::where('branch_id', $branchId)
                    ->whereNotIn('stock_id', [1093, 1012])
                    ->whereBetween('created_at', [$startDate, Carbon::now()])
                    ->sum(DB::raw('price * quantity'));

                // Calculate expenses
                $expenses[$branchId] = Expense::where('branch_id', $branchId)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), Carbon::now()->format('Y-m-d')])
                    ->sum('amount');

                // Calculate returns
                $returns[$branchId] = Returns::where('branch_id', $branchId)
                    ->whereBetween('created_at', [$startDate, Carbon::now()])
                    ->sum(DB::raw('price * quantity'));

                // Retrieve all users in a specific branch
                $users = User::where('branch_id', $branchId)->get();

                // Sum the balances of all users
                $creditsOwed[$branchId] = $users->sum('balance');

                // Calculate discounts
                $discounts[$branchId] = Sale::where('branch_id', $branchId)
                    ->whereBetween('created_at', [$startDate, Carbon::now()])
                    ->sum('discount');

                // Calculate net profit
                $netProfit[$branchId] = Sale::with('product')
                    ->where('branch_id', $branchId)
                    ->whereNotIn('stock_id', [1093, 1012])
                    ->whereBetween('created_at', [$startDate, Carbon::now()])
                    ->get()
                    ->sum(function ($sale) {
                        return @$sale->quantity * (@$sale->price-@$sale->product->buying_price)-@$sale->discount;
                    });

                // Calculate average transaction value
                $avgTransactionValue[$branchId] = Sale::where('branch_id', $branchId)
                    ->whereNotIn('stock_id', [1093, 1012])
                    ->whereBetween('created_at', [$startDate, Carbon::now()])
                    ->avg('price');

                // Calculate inventory turnover
                $inventoryTurnover[$branchId] = Sale::where('branch_id', $branchId)
                    ->whereBetween('created_at', [$startDate, Carbon::now()])
                    ->sum('quantity');
            }

            $data = [
                'branches' => $branches,
                'grossSales' => $grossSales,
                'expenses' => $expenses,
                'returns' => $returns,
                'creditsOwed' => $creditsOwed,
                'discounts' => $discounts,
                'netProfit' => $netProfit,
                'avgTransactionValue' => $avgTransactionValue,
                'inventoryTurnover' => $inventoryTurnover,
                'duration' => $request->duration,
            ];

        }

        if ($request->report == 'compare_graphs') {

            // Retrieve the metrics for each branch
            $branches = Branch::all();
            $startDate = Carbon::now()->subDays($request->duration);

            $metrics = [];

            foreach ($branches as $branch) {
                $branchMetrics = [
                    'branch' => $branch,
                    'grossSales' => Sale::join('stocks', 'sales.stock_id', '=', 'stocks.id')
                        ->where('sales.branch_id', $branch->id)
                        ->whereNotIn('stock_id', [1093, 1012])
                        ->whereBetween('sales.created_at', [$startDate, Carbon::now()])
                        ->sum(DB::raw('sales.price * sales.quantity')),
                    'netProfit' => Sale::join('stocks', 'sales.stock_id', '=', 'stocks.id')
                        ->where('sales.branch_id', $branch->id)
                        ->whereNotIn('stock_id', [1093, 1012])
                        ->whereBetween('sales.created_at', [$startDate, Carbon::now()])
                        ->sum(DB::raw('sales.quantity * (sales.price - stocks.buying_price) - sales.discount')),
                    'expenses' => Expense::where('branch_id', $branch->id)
                        ->whereBetween('date', [$startDate, Carbon::now()])
                        ->sum('amount'),
                    'creditsOwed' => User::where('branch_id', $branch->id)->sum('balance'),
                    'discounts' => Sale::where('branch_id', $branch->id)
                        ->whereNotIn('stock_id', [1093, 1012])
                        ->whereBetween('created_at', [$startDate, Carbon::now()])
                        ->sum('discount'),
                    'stockValueLeft' => Stock::where('branch_id', $branch->id)->whereNotIn('id', [1093, 1012])->sum(DB::raw('quantity * buying_price')),

                ];

                $metrics[] = $branchMetrics;
            }

            $data = [
                'metrics' => $metrics,
                'duration' => $request->duration,
            ];

        }

        if ($request->report == 'best_customers') {

            $branchId = $request->input('branch_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $customers = User::where('branch_id', $branchId)->get();

            $rankedCustomers = [];
            foreach ($customers as $customer) {
                $totalPurchases = DB::table('sales')
                    ->where('customer_name', $customer->id)
                    ->where('branch_id', $branchId)
                    ->sum(DB::raw('price * quantity'));

                $totalPayments = DB::table('payments')
                    ->where('customer_id', $customer->id)
                    ->where('branch_id', $branchId)
                    ->sum('payment_amount');

                $totalDiscounts = DB::table('sales')
                    ->where('customer_name', $customer->id)
                    ->where('branch_id', $branchId)
                    ->sum('discount');

                $balance = $customer->balance;

                $rankedCustomers[] = [
                    'customer' => $customer,
                    'total_purchases' => $totalPurchases,
                    'total_payments' => $totalPayments,
                    'total_discounts' => $totalDiscounts,
                    'balance' => $balance,
                ];
            }

            usort($rankedCustomers, function ($a, $b) {
                return $b['total_purchases'] <=> $a['total_purchases'];
            });

            $rankedCustomers = array_slice($rankedCustomers, 0, 20);

            $data['rankedCustomers'] = $rankedCustomers;

        }


        if ($request->report == 'best_debtors') {
            $branchId = $request->input('branch_id');
            $debtors = User::where('branch_id', $branchId)
            ->orderBy('balance', 'desc')
            ->take(20) 
            ->get();

            $data['debtors'] = $debtors;

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

    public function fetchStocks(Request $request)
    {
        $branchId = $request->input('branch_id');
        $stocks = Stock::where('branch_id', $branchId)->groupBy('name')->get();

        return response()->json($stocks);
    }

    private function applySalesFilters($query, $date, $branchId, $startDate, $endDate)
    {
        $today = Carbon::now()->startOfDay();

        if ($date === 'today') {
            $query->whereDate('created_at', $today);
        } elseif ($date === 'this_week') {
            $query->whereBetween('created_at', [$today->startOfWeek(), $today->endOfWeek()]);
        } elseif ($date === 'this_month') {
            $query->whereYear('created_at', $today->year)
                ->whereMonth('created_at', $today->month);
        } elseif ($date === 'range') {
            $query->whereBetween('created_at', [Carbon::parse($startDate), Carbon::parse($endDate)]);
        }

        if (!is_null($branchId)) {
            $query->whereHas('sales', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            });
        }
    }

}
