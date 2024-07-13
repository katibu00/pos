<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashCredit;
use App\Models\Estimate;
use App\Models\Expense;
use App\Models\FundTransfer;
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

    public function __construct()
    {
        set_time_limit(0);
    }

    public function index()
    {
        if (in_array(auth()->user()->id, [4, 443])) {
            $data['branches'] = Branch::all();
        } else {
            $data['branches'] = Branch::where('id', auth()->user()->branch_id)->get();
        }   
        return view('reports.index', $data);
    }

    public function generate(Request $request)
    {
        $branch_id = $request->branch_id;

        if ($request->report == 'general') {
            // Fetch sales data

            if ($request->date == 'today') {

                $todaySales = Sale::where('branch_id', $branch_id)
                    ->whereNotIn('stock_id', [1093, 1012])
                    ->whereDate('created_at', today())
                    ->get();

                $todayReturns = Returns::where('branch_id', $branch_id)
                    ->whereNull('channel')
                    ->whereDate('created_at', today())
                    ->get();

                $todayExpenses = Expense::where('branch_id', $branch_id)
                    ->whereDate('created_at', today())
                    ->get();

                $creditPayments = Payment::where('branch_id', $branch_id)
                    ->whereDate('created_at', today())
                    ->get();

                $estimates = Estimate::where('branch_id', $branch_id)
                    ->whereDate('created_at', today())
                    ->get();

                $purchases = Purchase::select('stock_id', 'quantity')
                    ->where('branch_id', $branch_id)
                    ->whereDate('created_at', today())
                    ->get();

                $data['cashCreditToday'] = CashCredit::where('branch_id', $branch_id)
                    ->whereDate('created_at', today())
                    ->sum(DB::raw('amount'));

                

               

            } else if ($request->date == 'week') {

                $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();
                
                $todaySales = Sale::where('branch_id', $branch_id)
                    ->whereNotIn('stock_id', [1093, 1012])
                    ->whereDate('created_at', '>=', $sevenDaysAgo)
                    ->get();
                
                $todayReturns = Returns::where('branch_id', $branch_id)
                    ->whereNull('channel')
                    ->whereDate('created_at', '>=', $sevenDaysAgo)
                    ->get();
                
                $todayExpenses = Expense::where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $sevenDaysAgo)
                    ->get();
                
                $creditPayments = Payment::where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $sevenDaysAgo)
                    ->get();
                
                $estimates = Estimate::where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $sevenDaysAgo)
                    ->get();
                
                $purchases = Purchase::select('stock_id', 'quantity')
                    ->where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $sevenDaysAgo)
                    ->get();
                
                $data['cashCreditToday'] = CashCredit::where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $sevenDaysAgo)
                    ->sum(DB::raw('amount'));
                

            } else if ($request->date == 'month'){

                $thirtyDaysAgo = Carbon::now()->subDays(7)->toDateString();
                
                $todaySales = Sale::where('branch_id', $branch_id)
                    ->whereNotIn('stock_id', [1093, 1012])
                    ->whereDate('created_at', '>=', $thirtyDaysAgo)
                    ->get();
                
                $todayReturns = Returns::where('branch_id', $branch_id)
                    ->whereNull('channel')
                    ->whereDate('created_at', '>=', $thirtyDaysAgo)
                    ->get();
                
                $todayExpenses = Expense::where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $thirtyDaysAgo)
                    ->get();
                
                $creditPayments = Payment::where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $thirtyDaysAgo)
                    ->get();
                
                $estimates = Estimate::where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $thirtyDaysAgo)
                    ->get();
                
                $purchases = Purchase::select('stock_id', 'quantity')
                    ->where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $thirtyDaysAgo)
                    ->get();
                
                $data['cashCreditToday'] = CashCredit::where('branch_id', $branch_id)
                    ->whereDate('created_at', '>=', $thirtyDaysAgo)
                    ->sum(DB::raw('amount'));

            } else if ($request->date == 'range' && $request->has('start_date') && $request->has('end_date')) {

                // Parse start and end dates from the request
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();

                $todaySales = Sale::where('branch_id', $branch_id)
                    ->whereNotIn('stock_id', [1093, 1012])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                $todayReturns = Returns::where('branch_id', $branch_id)
                    ->whereNull('channel')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                $todayExpenses = Expense::where('branch_id', $branch_id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                $creditPayments = Payment::where('branch_id', $branch_id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                $estimates = Estimate::where('branch_id', $branch_id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                $purchases = Purchase::select('stock_id', 'quantity')
                    ->where('branch_id', $branch_id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                $data['cashCreditToday'] = CashCredit::where('branch_id', $branch_id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('amount');

            }

            $paymentSums = [
                'cash' => 0,
                'transfer' => 0,
                'pos' => 0,
            ];

            $payments = DB::table('cash_credit_payments')
            ->select('payment_method', DB::raw('SUM(amount_paid) as total_amount_paid'))
            ->whereDate('created_at', today())
            ->groupBy('payment_method')
            ->get();

            foreach ($payments as $payment) {
                if (array_key_exists($payment->payment_method, $paymentSums)) {
                    $paymentSums[$payment->payment_method] += $payment->total_amount_paid;
                }
            }

            $data['CreditPaymentSummary'] = [
                'cash' => $paymentSums['cash'],
                'transfer' => $paymentSums['transfer'],
                'pos' => $paymentSums['pos'],
            ];

            $data['start_date'] = $request->start_date;
            $data['end_date'] = $request->end_date;

            //////////////////////////////////////////////

            $data['TotalcashCredit'] = CashCredit::where('branch_id', $branch_id)
                ->whereRaw('amount > amount_paid')
                ->sum(DB::raw('amount - amount_paid'));

            $data['deposits'] = User::where('branch_id', $branch_id)->sum('deposit');

            $data['totalDiscounts'] = $todaySales->sum('discount');
            //sales
            $data['grossSales'] = $todaySales->sum(function ($sale) {
                return $sale->price * $sale->quantity;
            });
            $data['totalDiscount'] = $todaySales->sum('discount');

            $uniquePosReceipts = [];

            $data['posSales'] = $todaySales
                ->filter(function ($sale) {
                    return $sale->payment_method == 'pos' || $sale->payment_method == 'multiple';
                })
                ->reduce(function ($total, $sale) use (&$uniquePosReceipts) {
                    if ($sale->payment_method == 'multiple') {

                        if (!in_array($sale->receipt_no, $uniquePosReceipts)) {
                            $paymentAmount = Payment::where('receipt_nos', $sale->receipt_no)
                                ->where('payment_type', 'multiple')
                                ->where('payment_method', 'pos')
                                ->value('payment_amount');

                            $total += $paymentAmount ?? 0;

                            $uniquePosReceipts[] = $sale->receipt_no;
                        }
                    } else {
                        $total += ($sale->price * $sale->quantity) - $sale->discount;
                    }

                    return $total;
                }, 0);

            $uniqueReceipts = [];

            $data['cashSales'] = $todaySales
                ->filter(function ($sale) {
                    return $sale->payment_method == 'cash' || $sale->payment_method == 'multiple';
                })
                ->reduce(function ($total, $sale) use (&$uniqueReceipts) {
                    if ($sale->payment_method == 'multiple') {

                        if (!in_array($sale->receipt_no, $uniqueReceipts)) {
                            $paymentAmount = Payment::where('receipt_nos', $sale->receipt_no)
                                ->where('payment_type', 'multiple')
                                ->where('payment_method', 'cash')
                                ->value('payment_amount');

                            $total += $paymentAmount ?? 0;

                            $uniqueReceipts[] = $sale->receipt_no;
                        }
                    } else {
                        $total += ($sale->price * $sale->quantity) - $sale->discount;
                    }

                    return $total;
                }, 0);

            $uniqueTransferReceipts = [];

            $data['transferSales'] = $todaySales
                ->filter(function ($sale) {
                    return $sale->payment_method == 'transfer' || $sale->payment_method == 'multiple';
                })
                ->reduce(function ($total, $sale) use (&$uniqueTransferReceipts) {
                    if ($sale->payment_method == 'multiple') {

                        if (!in_array($sale->receipt_no, $uniqueTransferReceipts)) {
                            $paymentAmount = Payment::where('receipt_nos', $sale->receipt_no)
                                ->where('payment_type', 'multiple')
                                ->where('payment_method', 'transfer')
                                ->value('payment_amount');

                            $total += $paymentAmount ?? 0;

                            $uniqueTransferReceipts[] = $sale->receipt_no;
                        }
                    } else {
                        $total += ($sale->price * $sale->quantity) - $sale->discount;
                    }

                    return $total;
                }, 0);

            $data['creditSales'] = $todaySales->where('payment_method', 'credit')->reduce(function ($total, $sale) {
                $total += ($sale->price * $sale->quantity) - $sale->discount;
                return $total;
            }, 0);
            $data['depositSales'] = $todaySales->where('payment_method', 'deposit')->reduce(function ($total, $sale) {
                $total += ($sale->price * $sale->quantity) - $sale->discount;
                return $total;
            }, 0);

            $data['grossProfit'] = $todaySales->sum(function ($sale) {

                return ($sale->price - $sale->buying_price) * $sale->quantity;
            });

            $data['uniqueSalesCount'] = @$todaySales->unique('receipt_no')->count();
            $data['totalItemsSold'] = $todaySales->sum('quantity');
            //returns
            $data['totalReturn'] = $todayReturns->sum(function ($return) {
                return ($return->price * $return->quantity);
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

            $data['returnProfit'] = $todayReturns->sum(function ($return) {
                return (($return->price-@$return->product->buying_price) * $return->quantity);
            });

            //Expenses
            $data['totalExpenses'] = $todayExpenses->sum('amount');
            $data['cashExpenses'] = $todayExpenses->where('payment_method', 'cash')->sum('amount');
            $data['posExpenses'] = $todayExpenses->where('payment_method', 'pos')->sum('amount');
            $data['transferExpenses'] = $todayExpenses->where('payment_method', 'transfer')->sum('amount');
            //credit Payments
            $data['totalCreditPayments'] = $creditPayments->where('payment_type', 'credit')->sum('payment_amount');
            $data['cashCreditPayments'] = $creditPayments->where('payment_method', 'cash')->where('payment_type', 'credit')->sum('payment_amount');
            $data['posCreditPayments'] = $creditPayments->where('payment_method', 'POS')->where('payment_type', 'credit')->sum('payment_amount');
            $data['transferCreditPayments'] = $creditPayments->where('payment_method', 'transfer')->where('payment_type', 'credit')->sum('payment_amount');
            //deposits
            $data['totalDepositPayments'] = $creditPayments->where('payment_type', 'deposit')->sum('payment_amount');
            $data['cashDepositPayments'] = $creditPayments->where('payment_method', 'cash')->where('payment_type', 'deposit')->sum('payment_amount');
            $data['posDepositPayments'] = $creditPayments->where('payment_method', 'POS')->where('payment_type', 'deposit')->sum('payment_amount');
            $data['transferDepositPayments'] = $creditPayments->where('payment_method', 'transfer')->where('payment_type', 'deposit')->sum('payment_amount');
            //estimates
            $data['totalEstimate'] = $estimates->sum(function ($estimate) {
                return ($estimate->price * $estimate->quantity) - $estimate->discount;
            });
            //purchases
            $data['totalPurchases'] = $purchases->sum(function ($purchase) {
                return $purchase['product']['buying_price'] * $purchase->quantity;
            });
           
            
            $data['cashFundTransfer'] = $data['transferFundTransfer'] = $data['posFundTransfer'] = 0;

            // Get cash transfers created today
            $cashTransfers = FundTransfer::where(function ($query) {
                $query->where('from_account', 'cash')
                    ->orWhere('to_account', 'cash');
            })
                ->whereDate('created_at', Carbon::today())
                ->where('branch_id', $branch_id)
                ->get();

            // Get transfer transfers created today
            $transferTransfers = FundTransfer::where(function ($query) {
                $query->where('from_account', 'transfer')
                    ->orWhere('to_account', 'transfer');
            })
                ->whereDate('created_at', Carbon::today())
                ->where('branch_id', $branch_id)
                ->get();

            // Get pos transfers created today
            $posTransfers = FundTransfer::where(function ($query) {
                $query->where('from_account', 'pos')
                    ->orWhere('to_account', 'pos');
            })
                ->whereDate('created_at', Carbon::today())
                ->where('branch_id', $branch_id)
                ->get();

            // Adjust the account balances based on funds transfers
            $data['cashFundTransfer'] += $cashTransfers->sum(function ($transfer) {
                return $transfer->from_account === 'cash' ? -$transfer->amount : ($transfer->to_account === 'cash' ? $transfer->amount : 0);
            });

            $data['transferFundTransfer'] += $transferTransfers->sum(function ($transfer) {
                return $transfer->from_account === 'transfer' ? -$transfer->amount : ($transfer->to_account === 'transfer' ? $transfer->amount : 0);
            });

            $data['posFundTransfer'] += $posTransfers->sum(function ($transfer) {
                return $transfer->from_account === 'pos' ? -$transfer->amount : ($transfer->to_account === 'pos' ? $transfer->amount : 0);
            });

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
                    ->where('customer', $customer->id)
                    ->where('branch_id', $branchId)
                    ->sum(DB::raw('price * quantity'));

                $totalPayments = DB::table('payments')
                    ->where('customer_id', $customer->id)
                    ->where('branch_id', $branchId)
                    ->sum('payment_amount');

                $totalDiscounts = DB::table('sales')
                    ->where('customer', $customer->id)
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
