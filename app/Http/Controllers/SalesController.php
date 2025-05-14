<?php

namespace App\Http\Controllers;

use App\Models\AwaitingPickup;
use App\Models\CashCredit;
use App\Models\CashCreditPayment;
use App\Models\Estimate;
use App\Models\Expense;
use App\Models\FundTransfer;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\SalesNotification;
use Carbon\Carbon;

class SalesController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $products = Stock::where('branch_id', $user->branch_id)->orderBy('name')->get();
        $customers = User::select('id', 'first_name')->where('usertype', 'customer')->where('branch_id', auth()->user()->branch_id)->orderBy('first_name')->get();

        $latestTransactions = DB::table('sales')
            ->select('receipt_no as transaction_no', 'created_at', DB::raw("'Sales' as type"))
            ->where('branch_id', $user->branch_id);

        $latestTransactions->union(
            DB::table('estimates')
                ->select('estimate_no as transaction_no', 'created_at', DB::raw("'Estimates' as type"))
                ->where('branch_id', $user->branch_id)
        );

        $latestTransactions->union(
            DB::table('returns')
                ->select('return_no as transaction_no', 'created_at', DB::raw("'Returns' as type"))
                ->where('branch_id', $user->branch_id)
        );

        $latestTransactions = $latestTransactions
            ->orderBy('created_at', 'desc')
            ->groupBy('transaction_no')
            ->take(3)
            ->get();

        $transactionData = [];

        foreach ($latestTransactions as $transaction) {
            $table = $transaction->type == 'Sales' ? 'sales' : ($transaction->type == 'Returns' ? 'returns' : 'estimates');

            $rows = DB::table($table)
                ->where('branch_id', $user->branch_id)
                ->where($transaction->type == 'Sales' ? 'receipt_no' : ($transaction->type == 'Returns' ? 'return_no' : 'estimate_no'), $transaction->transaction_no)
                ->get();

            $totalAmount = 0;
            foreach ($rows as $row) {
                $totalAmount += ($row->price * $row->quantity) - $row->discount;
            }

            // Fetch the customer information for this transaction
            $customer = null;
            if ($transaction->type == 'Sales') {
                $sale = DB::table('sales')->where('receipt_no', $transaction->transaction_no)->first();
                if (!is_null($sale) && is_numeric($sale->customer)) {
                    $customer = User::find($sale->customer);
                }
            }

            $transactionData[] = [
                'transaction_no' => $transaction->transaction_no,
                'type' => $transaction->type,
                'created_at' => $transaction->created_at,
                'totalAmount' => $totalAmount,
                'customer' => $customer,
            ];
        }

        return view('transactions.index', compact('transactionData', 'products', 'customers'));
    }

    public function getProductSuggestions(Request $request)
    {
        $query = $request->input('query');
        $suggestions = Stock::where('name', 'like', '%' . $query . '%')
            ->where('branch_id', auth()->user()->branch_id)
            ->limit(20)
            ->get();

        return response()->json($suggestions);
    }

    public function fetchBalanceOrDeposit(Request $request)
    {
        $userId = $request->input('user_id');
        $paymentMethod = $request->input('payment_method'); // Get the selected payment method

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $balanceOrDeposit = 0; // Initialize the variable to store the balance or deposit

        if ($paymentMethod === 'credit') {
            $balanceOrDeposit = $user->balance; // Fetch the user's balance
        } elseif ($paymentMethod === 'deposit') {
            $balanceOrDeposit = $user->deposit; // Fetch the user's deposit
        }

        return response()->json(['balance_or_deposit' => $balanceOrDeposit], 200);
    }

    public function fetchBalance(Request $request)
    {
        $user = User::select('balance', 'deposit')->where('id', $request->customer_id)->first();
        if ($user) {
            return response()->json([
                'status' => 200,
                'balance' => $user->balance,
                'deposits' => $user->deposit,
            ]);
        } else {
            return response()->json([
                'status' => 404,
            ]);
        }
    }

    public function store(Request $request)
    {

        $productIds = $request->input('product_id');
        $quantities = $request->input('quantity');
        $remainingQuantities = $request->input('remaining_quantity');
        $transaction_type = $request->input('transaction_type');

        foreach ($productIds as $key => $productId) {
            
            if (!isset($quantities[$key]) || $quantities[$key] < 0.01) {
                return response()->json([
                    'status' => 400,
                    'message' => "Row " . ($key + 1) . ": Quantity field is required.",
                ]);
            };

            if($transaction_type == 'sales')
            {
                if ($remainingQuantities[$key] < 1) {
                    return response()->json([
                        'status' => 400,
                        'message' => "Row " . ($key + 1) . ": The product has finished",
                    ]);
                }
                if ($quantities[$key] > $remainingQuantities[$key]) {
                    return response()->json([
                        'status' => 400,
                        'message' => "Row " . ($key + 1) . ":The entered quantity exceeds the remaining quantity.",
                    ]);
                }
            };
        }

        $transaction_id = Str::uuid();

        if ($transaction_type == "sales") {
           
            $paymentMethod = $request->input('payment_method');
            $status = null;
            $payment_amount = null;


            $totalPrice = 0;
            foreach ($request->product_id as $index => $productId) {
                $productTotal = ($request->price[$index] * $request->quantity[$index]) - ($request->discount[$index] ?? 0);
                $totalPrice += $productTotal;
            }
            if ($paymentMethod == 'deposit') {

                $deposits = Payment::select('payment_amount')->where('customer_id', $request->customer)->where('payment_type', 'deposit')->sum('payment_amount');
                if ($totalPrice > $deposits) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Deposit Balance is low. Reduce Quantity and Try again',
                    ]);
                }

                $user = User::find($request->customer);
                $user->deposit -= $totalPrice;
                $user->update();

            } elseif ($paymentMethod == 'credit') {

                if($request->paid_amount != null && $request->paid_amount > 0)
                {
                    if ($request->partial_payment_method == '') {
                        return response()->json([
                            'status' => 400,
                            'message' => 'Please choose Partial Amount Payment Channel',
                        ]);
                    }

                    $payment = new Payment();
                    $payment->payment_method = $request->partial_payment_method;
                    $payment->branch_id = auth()->user()->branch_id;
                    $payment->payment_amount = $request->paid_amount;
                    $payment->customer_id = $request->customer;
                    $payment->receipt_nos = $transaction_id;
                    $payment->user_id = auth()->user()->id;
                    $payment->payment_type = 'credit';
                    $payment->save();

                    $status = 'partial';
                    $payment_amount = $request->paid_amount;

                    $user = User::find($request->customer);
                    $user->balance += ($totalPrice - $payment_amount);
                    $user->update();
                }else
                {
                    $user = User::find($request->customer);
                    $user->balance += $totalPrice;
                    $user->update();
                }

            } 

            foreach ($productIds as $index => $productId) {
               
                $stock = Stock::find($productId);
                $stock->quantity -= $request->quantity[$index];

                $data = new Sale();
                $data->branch_id = auth()->user()->branch_id;
                $data->receipt_no = $transaction_id;
                $data->stock_id = $productId;
                $data->price = $request->price[$index];
                $data->buying_price = $stock->buying_price;
                $data->quantity = $request->quantity[$index];
                $data->discount = $request->discount[$index] ?? 0;
                $data->payment_method = $paymentMethod;
                $data->user_id = auth()->user()->id;
                $data->customer = $request->customer === '0' ? null : $request->customer;
                $data->note = $request->note;

                if ($request->input('toggleLabor')) {
                    $data->labor_cost = $request->input('labor_cost');
                }
                $data->payment_amount = $payment_amount;
                $data->status = $status;
                $data->save();
               
                $stock->save();
            }

            if($paymentMethod == 'multiple')
            {
                if($request->cashAmount != null)
                {
                    $payment = new Payment();
                    $payment->payment_type = 'multiple';
                    $payment->payment_method = 'cash';
                    $payment->payment_amount = $request->cashAmount;
                    $payment->user_id = auth()->user()->id;
                    $payment->customer_id = 0;
                    $payment->receipt_nos =  $transaction_id;
                    $payment->save();
                }
                if($request->posAmount != null)
                {
                    $payment = new Payment();
                    $payment->payment_type = 'multiple';
                    $payment->payment_method = 'pos';
                    $payment->payment_amount = $request->posAmount;
                    $payment->user_id = auth()->user()->id;
                    $payment->customer_id = 0;
                    $payment->receipt_nos =  $transaction_id;
                    $payment->save();
                }
                if($request->transferAmount != null)
                {
                    $payment = new Payment();
                    $payment->payment_type = 'multiple';
                    $payment->payment_method = 'transfer';
                    $payment->payment_amount = $request->transferAmount;
                    $payment->user_id = auth()->user()->id;
                    $payment->customer_id = 0;
                    $payment->receipt_nos =  $transaction_id;
                    $payment->save();
                }
            }

            $totalSalesAmount = 0;
            foreach ($request->product_id as $index => $productId) {
                $productTotal = ($request->price[$index] * $request->quantity[$index]) - ($request->discount[$index] ?? 0);
                $totalSalesAmount += $productTotal;
            }

            // Determine customer name
            $customerName = $request->customer === '0' ? 'Walk-in Customer' : User::find($request->customer)->first_name;

            $branchName = auth()->user()->branch->name;

            $notificationMessage = "New Sale: $customerName brought goods worth ₦" . number_format($totalSalesAmount, 0) . " in $branchName Branch and paid via $paymentMethod.";

            // Send notification to admin
            $admin = User::where('usertype', 'admin')->first();
            $admin->notify(new SalesNotification($notificationMessage));
           
            return response()->json([
                'status' => 201,
                'message' => 'Sale has been recorded successfully',
            ]);
        }

        if ($transaction_type == "estimate") {

            $productCount = count($request->product_id);
            if ($productCount != null) {
                for ($i = 0; $i < $productCount; $i++) {

                    $data = new Estimate();
                    $data->branch_id = auth()->user()->branch_id;
                    $data->estimate_no = $transaction_id;
                    $data->product_id = $request->product_id[$i];
                    $data->price = $request->price[$i];
                    $data->quantity = $request->quantity[$i];
                    $data->discount = $request->discount[$i] ?? 0;
                    $data->cashier_id = auth()->user()->id;
                    $data->customer = $request->customer;
                    $data->note = $request->note;
                    if ($request->input('toggleLabor')) {
                        $data->labor_cost = $request->input('labor_cost');
                    }
                    $data->save();
                }
            }

            $totalSalesAmount = 0;
            foreach ($request->product_id as $index => $productId) {
                $productTotal = ($request->price[$index] * $request->quantity[$index]) - ($request->discount[$index] ?? 0);
                $totalSalesAmount += $productTotal;
            }

            // Determine customer name
            $customerName = $request->customer === '0' ? 'Walk-in Customer' : User::find($request->customer)->first_name;

            $branchName = auth()->user()->branch->name;

            $notificationMessage = "New Estimate: $customerName was issued quotation totalled ₦" . number_format($totalSalesAmount, 0) . " in $branchName Branch. Note: $request->note";

            // Send notification to admin
            $admin = User::where('usertype', 'admin')->first();
            $admin->notify(new SalesNotification($notificationMessage));

            return response()->json([
                'status' => 201,
                'message' => 'Estimate has been Saved sucessfully',
            ]);
        }

        if ($transaction_type == "return") {
          
            $total_price = collect($request->quantity)
                ->map(function ($quantity, $index) use ($request) {
                    return ($quantity * $request->price[$index]) - $request->discount[$index];
                })
                ->sum();

                if($request->payment_method == 'credit' || $request->payment_method == 'deposit')
                {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Wrong Payment Channel Selected.',
                    ]);
                }

                $balanceInfo = $this->getTodayBalance($request->payment_method);
                $currentBalance = $balanceInfo['current_balance'];
                $minimumBalance = $balanceInfo['minimum_balance'];
                
                if ($currentBalance <= 0 || ($currentBalance - $total_price) < $minimumBalance) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Insufficient funds. Please ensure you maintain a minimum balance of ' . $minimumBalance . '.',
                    ]);
                }
        

            $productCount = count($request->product_id);
            if ($productCount != null) {
                for ($i = 0; $i < $productCount; $i++) {

                    $data = new Returns();
                    $data->branch_id = auth()->user()->branch_id;
                    $data->return_no = $transaction_id;
                    $data->product_id = $request->product_id[$i];
                    $data->price = $request->price[$i];
                    $data->quantity = $request->quantity[$i];
                    if ($request->discount[$i] == null) {
                        $data->discount = 0;

                    } else {
                        $data->discount = $request->discount[$i];
                    }
                    $data->cashier_id = auth()->user()->id;
                    $data->customer = $request->customer_name;
                    $data->note = $request->note;
                    $data->payment_method = $request->payment_method;
                    $data->save();

                    $data = Stock::find($request->product_id[$i]);
                    $data->quantity += $request->quantity[$i];
                    $data->update();

                }
            }

            $totalSalesAmount = 0;
            foreach ($request->product_id as $index => $productId) {
                $productTotal = ($request->price[$index] * $request->quantity[$index]) - ($request->discount[$index] ?? 0);
                $totalSalesAmount += $productTotal;
            }

            // Determine customer name
            $customerName = $request->customer === '0' ? 'Walk-in Customer' : User::find($request->customer)->first_name;

            $branchName = auth()->user()->branch->name;

            $notificationMessage = "New Return: $customerName returned goods worth ₦" . number_format($totalSalesAmount, 0) . " in $branchName Branch.";

            // Send notification to admin
            $admin = User::where('usertype', 'admin')->first();
            $admin->notify(new SalesNotification($notificationMessage));

            return response()->json([
                'status' => 201,
                'message' => 'Return has been saved sucessfully',
            ]);

        }

    }

    private function getTodayBalance($paymentMethod)
    {
        $branch_id = auth()->user()->branch_id;
        $today = Carbon::today();
    
        // Get sales for today
        $sales = Sale::where('branch_id', $branch_id)
            ->whereDate('created_at', $today)
            ->where(function($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod)
                    ->orWhere('payment_method', 'multiple');
            })
            ->get();
    
        // Calculate sales amount
        $salesAmount = $sales->reduce(function ($total, $sale) use ($paymentMethod) {
            if ($sale->payment_method == 'multiple') {
                $paymentAmount = Payment::where('receipt_nos', $sale->receipt_no)
                    ->where('payment_type', 'multiple')
                    ->where('payment_method', $paymentMethod)
                    ->value('payment_amount');
                $total += $paymentAmount ?? 0;
            } else {
                $total += ($sale->price * $sale->quantity) - $sale->discount;
            }
            return $total;
        }, 0);
    
        // Get returns for today
        $returns = Returns::where('branch_id', $branch_id)
            ->whereDate('created_at', $today)
            ->where('payment_method', $paymentMethod)
            ->sum(DB::raw('price * quantity - discount'));
    
        // Get expenses for today
        $expenses = Expense::where('branch_id', $branch_id)
            ->whereDate('created_at', $today)
            ->where('payment_method', $paymentMethod)
            ->sum('amount');
    
        // Get credit payments for today
        $creditPayments = Payment::where('branch_id', $branch_id)
            ->whereDate('created_at', $today)
            ->where('payment_method', $paymentMethod)
            ->where('payment_type', 'credit')
            ->sum('payment_amount');
    
        // Get deposit payments for today
        $depositPayments = Payment::where('branch_id', $branch_id)
            ->whereDate('created_at', $today)
            ->where('payment_method', $paymentMethod)
            ->where('payment_type', 'deposit')
            ->sum('payment_amount');
    
        // Get cash credit for today (only applicable for cash)
        $cashCredit = $paymentMethod == 'cash' 
            ? CashCredit::where('branch_id', $branch_id)
                ->whereDate('created_at', $today)
                ->sum('amount')
            : 0;
    
        // Get credit repayments for today
        $creditRepayments = DB::table('cash_credit_payments')
            ->whereDate('created_at', $today)
            ->where('payment_method', $paymentMethod)
            ->sum('amount_paid');
    
        // Get fund transfers for today
        $fundTransfers = FundTransfer::whereDate('created_at', $today)
            ->where('branch_id', $branch_id)
            ->where(function ($query) use ($paymentMethod) {
                $query->where('from_account', $paymentMethod)
                      ->orWhere('to_account', $paymentMethod);
            })
            ->get()
            ->reduce(function ($total, $transfer) use ($paymentMethod) {
                return $total + ($transfer->from_account === $paymentMethod ? -$transfer->amount : ($transfer->to_account === $paymentMethod ? $transfer->amount : 0));
            }, 0);
    
        // Calculate total balance
        $balance = $salesAmount - $returns - $expenses + $creditPayments + $depositPayments - $cashCredit + $creditRepayments + $fundTransfers;
    
        $minimumBalance = 50; // Set the minimum balance

        return [
            'current_balance' => $balance,
            'minimum_balance' => $minimumBalance
        ];
    }

    public function refresh(Request $request)
    {
        $user = auth()->user();

        $latestTransactions = DB::table('sales')
            ->select('receipt_no as transaction_no', 'created_at', DB::raw("'Sales' as type"))
            ->where('branch_id', $user->branch_id);

        $latestTransactions->union(
            DB::table('estimates')
                ->select('estimate_no as transaction_no', 'created_at', DB::raw("'Estimates' as type"))
                ->where('branch_id', $user->branch_id)
        );

        $latestTransactions->union(
            DB::table('returns')
                ->select('return_no as transaction_no', 'created_at', DB::raw("'Returns' as type"))
                ->where('branch_id', $user->branch_id)
        );

        $latestTransactions = $latestTransactions
            ->orderBy('created_at', 'desc')
            ->groupBy('transaction_no')
            ->take(3)
            ->get();

        $transactionData = [];

        foreach ($latestTransactions as $transaction) {
            $table = $transaction->type == 'Sales' ? 'sales' : ($transaction->type == 'Returns' ? 'returns' : 'estimates');

            $rows = DB::table($table)
                ->where('branch_id', $user->branch_id)
                ->where($transaction->type == 'Sales' ? 'receipt_no' : ($transaction->type == 'Returns' ? 'return_no' : 'estimate_no'), $transaction->transaction_no)
                ->get();

            $totalAmount = 0;
            foreach ($rows as $row) {
                $totalAmount += ($row->price * $row->quantity) - $row->discount;
            }

            // Fetch the customer information for this transaction
            $customer = null;
            if ($transaction->type == 'Sales') {
                $sale = DB::table('sales')->where('receipt_no', $transaction->transaction_no)->first();
                if (!is_null($sale) && is_numeric($sale->customer)) {
                    $customer = User::find($sale->customer);
                }
            }

            $transactionData[] = [
                'transaction_no' => $transaction->transaction_no,
                'type' => $transaction->type,
                'created_at' => $transaction->created_at,
                'totalAmount' => $totalAmount,
                'customer' => $customer,
            ];
        }

        return view('transactions.recent_sales_table', compact('transactionData'))->render();
    }

    public function loadReceipt(Request $request)
    {
        $transactionType = $request->transaction_type;
        $transactionNo = $request->receipt_no;
        $items = [];
        $paidAmount = 0;
        $transactionDate = null;

        switch ($transactionType) {
            case 'Sales':
                $items = Sale::with('product')->where('receipt_no', $transactionNo)->get();
                if ($items->isNotEmpty()) {
                    $transactionDate = $items[0]->created_at;
                    if ($items[0]->payment_method === 'credit' && $items[0]->status === 'partial') {
                        $paidAmount = Payment::where('receipt_nos', 'like', "%$transactionNo%")->sum('payment_amount');
                    }
                }
                break;
            case 'Returns':
                $items = Returns::with('product')->where('return_no', $transactionNo)->get();
                if ($items->isNotEmpty()) {
                    $transactionDate = $items[0]->created_at;
                }
                break;
            case 'Estimates':
                $items = Estimate::with('product')->where('estimate_no', $transactionNo)->get();
                if ($items->isNotEmpty()) {
                    $transactionDate = $items[0]->created_at;
                }
                break;
            default:
                $items = Sale::with('product')->where('receipt_no', $transactionNo)->get();
                if ($items->isNotEmpty()) {
                    $transactionDate = $items[0]->created_at;
                }
                break;
        }

        return response()->json([
            'status' => 200,
            'items' => $items,
            'paid_amount' => $paidAmount,
            'transaction_date' => $transactionDate ? $transactionDate->format('F j, Y h:i A') : null,
        ]);
    }

    // public function allIndex()
    // {
    //     $data['sales'] = Sale::select('stock_id', 'receipt_no')->where('branch_id', auth()->user()->branch_id)->groupBy('receipt_no')->orderBy('created_at', 'desc')->paginate(10);
    //     $data['staffs'] = User::whereIn('usertype', ['admin', 'cashier'])->where('branch_id', auth()->user()->branch_id)->get();

    //     return view('sales.all_index', $data);
    // }


    public function allIndex()
    {
        $data['sales'] = Sale::select('stock_id', 'receipt_no')
            ->where('branch_id', auth()->user()->branch_id)
            ->groupBy('receipt_no')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Get receipt numbers from paginated sales
        $receiptNumbers = $data['sales']->pluck('receipt_no')->toArray();
        
        // Get all awaiting pickups for these receipt numbers from the new table
        $awaitingPickups = AwaitingPickup::whereIn('receipt_no', $receiptNumbers)
            ->where('status', 'awaiting')
            ->selectRaw('receipt_no, SUM(quantity) as total_quantity')
            ->groupBy('receipt_no')
            ->pluck('total_quantity', 'receipt_no')
            ->toArray();
        
        $data['awaitingPickups'] = $awaitingPickups;
        $data['staffs'] = User::whereIn('usertype', ['admin', 'cashier'])
            ->where('branch_id', auth()->user()->branch_id)
            ->get();
        
        return view('sales.all_index', $data);
    }


    public function allSearch(Request $request)
    {
        $searchQuery = $request->input('query');
    
        $data['sales'] = Sale::select('sales.stock_id', 'sales.receipt_no')
            ->leftJoin('users', 'sales.customer', '=', 'users.id')
            ->where('sales.branch_id', auth()->user()->branch_id)
            ->where(function($query) use ($searchQuery) {
                $query->where('users.first_name', 'like', "%$searchQuery%")
                    ->orWhere('users.last_name', 'like', "%$searchQuery%")
                    ->orWhere('sales.note', 'like', "%$searchQuery%");
            })
            ->groupBy('sales.receipt_no')
            ->orderBy('sales.created_at', 'desc')
            ->take(100)
            ->get();
    
        // Get receipt numbers from the search results
        $receiptNumbers = $data['sales']->pluck('receipt_no')->toArray();
        
        // Get all awaiting pickups for these receipt numbers from the new table
        $awaitingPickups = AwaitingPickup::whereIn('receipt_no', $receiptNumbers)
            ->where('status', 'awaiting')
            ->selectRaw('receipt_no, SUM(quantity) as total_quantity')
            ->groupBy('receipt_no')
            ->pluck('total_quantity', 'receipt_no')
            ->toArray();
        
        $data['awaitingPickups'] = $awaitingPickups;
    
        return view('sales.all_table', $data)->render();
    }


    public function filterSales(Request $request)
    {
        $cashierId = $request->input('cashier_id');
        $transactionType = $request->input('transaction_type');
    
        $query = Sale::select('stock_id', 'receipt_no')
            ->where('branch_id', auth()->user()->branch_id);
    
        if ($cashierId && $cashierId != 'all') {
            $query->where('user_id', $cashierId);
        }
    
        // Handle the transaction type filter
        if ($transactionType && $transactionType != 'all') {
            if ($transactionType === 'awaiting_pickup_old') {
                // Old awaiting pickup filtering logic
                $query->where('collected', 0);
            } elseif ($transactionType === 'awaiting_pickup_new') {
                // New awaiting pickup filtering logic
                // Get receipt numbers with awaiting pickups from the new table
                $receiptNumbers = AwaitingPickup::where('status', 'awaiting')
                                    ->pluck('receipt_no')
                                    ->unique()
                                    ->toArray();
                
                $query->whereIn('receipt_no', $receiptNumbers);
            } else {
                // Standard payment method filtering
                $query->where('payment_method', $transactionType);
            }
        }
    
        $data['sales'] = $query->groupBy('receipt_no')
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();
        
        // Get awaiting pickups data for the view
        $receiptNumbers = $data['sales']->pluck('receipt_no')->toArray();
        $awaitingPickups = AwaitingPickup::whereIn('receipt_no', $receiptNumbers)
            ->where('status', 'awaiting')
            ->selectRaw('receipt_no, SUM(quantity) as total_quantity')
            ->groupBy('receipt_no')
            ->pluck('total_quantity', 'receipt_no')
            ->toArray();
        
        $data['awaitingPickups'] = $awaitingPickups;
    
        return view('sales.all_table', $data)->render();
    }

    public function markAwaitingPickup(Request $request)
    {
        $receiptNo = $request->receiptNo;
        
        $sales = Sale::where('receipt_no', $receiptNo)->get();
        
        foreach ($sales as $sale) {
            $sale->collected = 0;
            $sale->save();
            
            $stock = Stock::find($sale->stock_id);
            $stock->pending_pickups += $sale->quantity;
            $stock->save();
        }
        
        return response()->json([
            'status' => 200,
            'message' => 'Items marked as awaiting pickup successfully.',
        ]);
    }
    
    public function markDeliver(Request $request)
    {
        $receiptNo = $request->receiptNo;
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            $sales = Sale::where('receipt_no', $receiptNo)
                ->where('collected', 0)
                ->get();
            
            // First, check if there's enough inventory for all items
            foreach ($sales as $sale) {
                $stock = Stock::find($sale->stock_id);
                
                if ($stock->quantity < $sale->quantity) {
                    // Not enough inventory, rollback and return error
                    DB::rollBack();
                    return response()->json([
                        'status' => 400,
                        'message' => "Insufficient inventory for {$stock->name}. Available: {$stock->quantity}, Required: {$sale->quantity}",
                    ]);
                }
            }
            
            // If we get here, we have enough inventory for all items
            foreach ($sales as $sale) {
                $stock = Stock::find($sale->stock_id);
                
                // Update sale status
                $sale->collected = 1;
                $sale->save();
                
                // Reduce inventory and pending pickups
                $stock->quantity -= $sale->quantity;
                $stock->pending_pickups -= $sale->quantity;
                $stock->save();
            }
            
            // Commit the transaction
            DB::commit();
            
            return response()->json([
                'status' => 200,
                'message' => 'Sales marked as delivered successfully',
            ]);
        } catch (\Exception $e) {
            // Something went wrong, rollback
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

}
