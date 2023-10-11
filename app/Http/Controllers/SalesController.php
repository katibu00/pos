<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data['products'] = Stock::where('branch_id', $user->branch_id)->orderBy('name')->get();
        $data['recents'] = Sale::select('stock_id', 'receipt_no')->whereDate('created_at', Carbon::today())->where('user_id', auth()->user()->id)->groupBy('receipt_no')->orderBy('created_at', 'desc')->take(3)->get();
        $data['sold_items'] = [];
        $data['customers'] = User::select('id', 'first_name')->where('usertype', 'customer')->where('branch_id', auth()->user()->branch_id)->orderBy('first_name')->get();
        return view('sales.index', $data);
    }

    public function getProductSuggestions(Request $request)
    {
        $query = $request->input('query');
        $suggestions = Stock::where('name', 'like', '%' . $query . '%')
            ->where('branch_id', auth()->user()->branch_id)
            ->limit(10) // Limit the number of suggestions
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
        if (in_array(null, $request->quantity, true)) {
            return response()->json([
                'status' => 400,
                'message' => 'Quantity cannot be empty for any product.',
            ]);
        }
        
        $paymentMethod = $request->input('payment_method');
    
        $year = date('Y');
        $month = Carbon::now()->format('m');
        $day = Carbon::now()->format('d');
        $last = Sale::whereDate('created_at', '=', date('Y-m-d'))->latest()->first();
        $lastRecord = $last ? $last->receipt_no : '1/0';
        [$prefix, $number] = explode("/", $lastRecord);
        $number = sprintf("%04d", $number + 1);
        $trxId = $year . $month . $day . '/' . $number;
     
    
        // Calculate the total price for the products
        $totalPrice = 0;
        foreach ($request->product_id as $index => $productId) {
            $productTotal = ($request->price[$index] * $request->quantity[$index]) - ($request->discount[$index] ?? 0);
            $totalPrice += $productTotal;
        }
        if ($paymentMethod == 'deposit') {
            // Check if deposit balance is sufficient
            $deposits = Payment::select('payment_amount')->where('customer_id', $request->customer)->where('payment_type', 'deposit')->sum('payment_amount');
            if ($totalPrice > $deposits) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Deposit Balance is low. Reduce Quantity and Try again',
                ]);
            }
    
            // Deduct the deposit amount from the customer's balance
            $user = User::find($request->customer);
            $user->deposit -= $totalPrice;
            $user->update();
    
        } elseif ($paymentMethod == 'credit') {
            // Add the total price to the customer's balance
            $user = User::find($request->customer);
            $user->balance += $totalPrice;
            $user->update();
    
        } else {
           
        }
    
        // Loop through the products and process each one
        foreach ($request->product_id as $index => $productId) {
            $data = new Sale();
            $data->branch_id = auth()->user()->branch_id;
            $data->receipt_no = $trxId;
            $data->stock_id = $productId;
            $data->price = $request->price[$index];
            $data->quantity = $request->quantity[$index];
            $data->discount = $request->discount[$index] ?? 0;
            $data->payment_method = $paymentMethod;
            $data->user_id = auth()->user()->id;
            $data->customer = $request->customer === '0' ? null : $request->customer;
            $data->note = $request->note;
            $data->payment_method = $paymentMethod;
    
            // Handle labor cost if necessary
            if ($request->input('toggleLabor')) {
                $data->labor_cost = $request->input('labor_cost');
            }
    
            $data->save();
    
            // Update stock quantity
            $stock = Stock::find($productId);
            $stock->quantity -= $request->quantity[$index];
            $stock->update();
        }
    
        return response()->json([
            'status' => 201,
            'message' => 'Sale has been recorded successfully',
        ]);
    }
    

    public function refresh(Request $request)
    {
        $data['recents'] = Sale::select('stock_id', 'receipt_no')->whereDate('created_at', Carbon::today())->where('user_id', auth()->user()->id)->groupBy('receipt_no')->orderBy('created_at', 'desc')->take(3)->get();
        return view('sales.recent_sales_table', $data)->render();
    }
    public function loadReceipt(Request $request)
    {
        $items = Sale::with('product')->where('receipt_no', $request->receipt_no)->get();
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
        $query = $request->input('query');

        // Perform the search query on the Sale model
        $data['sales'] = Sale::select('stock_id', 'receipt_no')
            ->where('branch_id', auth()->user()->branch_id)
            ->where('receipt_no', 'LIKE', '%' . $query . '%')
            ->groupBy('receipt_no')
            ->orderBy('created_at', 'desc')
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
