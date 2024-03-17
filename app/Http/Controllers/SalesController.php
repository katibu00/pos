<?php

namespace App\Http\Controllers;

use App\Models\CashCredit;
use App\Models\CashCreditPayment;
use App\Models\Estimate;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\SalesNotification;


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

            if (!$this->checkBalance($request->payment_method, $total_price)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Low Balance in the Payment Channel.',
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

    private function checkBalance($paymentMethod, $totalPrice)
    {
        $branch_id = auth()->user()->branch_id;

        $todaySales = Sale::where('branch_id', $branch_id)
            ->where('payment_method', $paymentMethod)
            ->whereNotIn('stock_id', [1093, 1012])
            ->whereDate('created_at', today())
            ->get();

        $todayReturns = Returns::where('branch_id', $branch_id)
            ->where('payment_method', $paymentMethod)
            ->whereDate('created_at', today())
            ->get();

        $expenses = Expense::where('branch_id', $branch_id)
            ->where('payment_method', $paymentMethod)
            ->whereDate('created_at', today())
            ->sum('amount');

        $creditRepayments = Payment::where('branch_id', $branch_id)
            ->where('payment_method', $paymentMethod)
            ->where('payment_type', 'credit')
            ->whereDate('created_at', today())
            ->sum('payment_amount');

        $deposits = Payment::where('branch_id', $branch_id)
            ->where('payment_method', $paymentMethod)
            ->where('payment_type', 'deposit')
            ->whereDate('created_at', today())
            ->sum('payment_amount');

        $cashCreditPayment = CashCreditPayment::where('branch_id', $branch_id)
            ->whereDate('created_at', today())
            ->where('payment_method', $paymentMethod)
            ->sum('amount_paid');

        $totalSales = $todaySales->sum(function ($sale) {
            return ($sale->price * $sale->quantity) - $sale->discount;
        });

        $totalReturns = $todayReturns->sum(function ($return) {
            return ($return->price * $return->quantity) - $return->discount;
        });

        $netAmount = $totalSales + $deposits + $creditRepayments + $cashCreditPayment - ($totalReturns + $expenses);

        if ($paymentMethod === 'cash') {
            $cashCredit = CashCredit::where('branch_id', $branch_id)
                ->whereDate('created_at', today())
                ->sum('amount');
            $netAmount -= $cashCredit;
        }

        return ($totalPrice <= $netAmount);
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

        if ($transactionType === 'Sales') {
            $items = Sale::with('product')
                ->where('receipt_no', $transactionNo)
                ->get();
        } elseif ($transactionType === 'Returns') {
            $items = Returns::with('product')
                ->where('return_no', $transactionNo)
                ->get();
        } elseif ($transactionType === 'Estimates') {
            $items = Estimate::with('product')
                ->where('estimate_no', $transactionNo)
                ->get();
        }
        if(!$transactionType)
        {
            $items = Sale::with('product')
                ->where('receipt_no', $request->receipt_no)
                ->get();
        }
        return response()->json([
            'status' => 200,
            'items' => $items,
        ]);

    }

    public function allIndex()
    {
        $data['sales'] = Sale::select('stock_id', 'receipt_no')->where('branch_id', auth()->user()->branch_id)->groupBy('receipt_no')->orderBy('created_at', 'desc')->paginate(10);
        $data['staffs'] = User::whereIn('usertype', ['admin', 'cashier'])->where('branch_id', auth()->user()->branch_id)->get();

        return view('sales.all_index', $data);
    }


    public function allSearch(Request $request)
    {
        $searchQuery = $request->input('query');

        $data['sales'] = Sale::select('sales.stock_id', 'sales.receipt_no')
            ->join('users', 'sales.customer', '=', 'users.id')
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

        if ($transactionType && $transactionType != 'all') {
            if ($transactionType === 'awaiting_pickup') {
                $query->where('collected', 0);
            } else {
                $query->where('payment_method', $transactionType);
            }
        }

        $data['sales'] = $query->groupBy('receipt_no')
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();

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

        $sales = Sale::where('receipt_no', $receiptNo)
            ->where('collected', 0)
            ->get();

        foreach ($sales as $sale) {
            $sale->collected = 1;
            $sale->update();

            $stock = Stock::find($sale->stock_id);
            $stock->pending_pickups -= $sale->quantity;
            $stock->update();
        }

        return response()->json([
            'status' => 200,
            'message' => 'Sales marked as delivered successfully',
        ]);
    }

}
