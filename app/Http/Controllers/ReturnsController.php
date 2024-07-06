<?php

namespace App\Http\Controllers;

use App\Models\CashCredit;
use Illuminate\Support\Str;
use App\Models\Expense;
use App\Models\FundTransfer;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnsController extends Controller
{
    // public function index()
    // {
    //     $user = auth()->user();
    //     $data['products'] = Stock::where('branch_id', $user->branch_id)->orderBy('name')->get();
    //     $data['recents'] = Returns::select('product_id', 'return_no')->whereDate('created_at', Carbon::today())->where('cashier_id', auth()->user()->id)->groupBy('return_no')->orderBy('created_at', 'desc')->take(4)->get();
    //     return view('returns.index', $data);
    // }
    public function allIndex()
    {
        $data['returns'] = Returns::select('product_id', 'return_no')->where('branch_id', auth()->user()->branch_id)->groupBy('return_no')->orderBy('created_at', 'desc')->paginate(10);
        return view('returns.all.index', $data);
    }

    // public function store(Request $request)
    // {

    //     dd($this->getTodayBalance('cash'));
    //     $total_price = collect($request->quantity)
    //         ->map(function ($quantity, $index) use ($request) {
    //             return ($quantity * $request->price[$index]) - $request->discount[$index];
    //         })
    //         ->sum();



    //     if ($total_price > $this->getTodayBalance($request->payment_method)) {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => 'Low Balance in the Payment Channel.',
    //         ]);
    //     }

    //     $transaction_id = Str::uuid();

    //     $productCount = count($request->product_id);
    //     if ($productCount != null) {
    //         for ($i = 0; $i < $productCount; $i++) {

    //             $data = new Returns();
    //             $data->branch_id = auth()->user()->branch_id;
    //             $data->return_no = $transaction_id;
    //             $data->product_id = $request->product_id[$i];
    //             $data->price = $request->price[$i];
    //             $data->quantity = $request->quantity[$i];
    //             if ($request->discount[$i] == null) {
    //                 $data->discount = 0;

    //             } else {
    //                 $data->discount = $request->discount[$i];
    //             }
    //             $data->cashier_id = auth()->user()->id;
    //             $data->customer = $request->customer_name;
    //             $data->note = $request->note;
    //             $data->payment_method = $request->payment_method;
    //             $data->save();

    //             $data = Stock::find($request->product_id[$i]);
    //             $data->quantity += $request->quantity[$i];
    //             $data->update();

    //         }
    //     }

    //     return response()->json([
    //         'status' => 201,
    //         'message' => 'Return has been saved sucessfully',
    //     ]);

    // }

    // public function refresh(Request $request)
    // {
    //     $data['recents'] = Returns::select('product_id', 'return_no')->whereDate('created_at', Carbon::today())->where('cashier_id', auth()->user()->id)->groupBy('return_no')->orderBy('created_at', 'desc')->take(4)->get();
    //     return view('returns.recent_sales_table', $data)->render();
    // }
    public function loadReceipt(Request $request)
    {
        $items = Returns::with('product')->where('return_no', $request->return_no)->get();
        return response()->json([
            'status' => 200,
            'items' => $items,
        ]);
    }

    // private function getBalances($paymentMethod)
    // {
    //     $branch_id = auth()->user()->branch_id;
    //     $cashier_id = auth()->user()->id;

    //     $todaySales = Sale::where('user_id', $cashier_id)
    //         ->where('branch_id', $branch_id)
    //         ->whereNotIn('stock_id', [1093, 1012])
    //         ->whereDate('created_at', today())
    //         ->get();

    //     $todayReturns = Returns::where('cashier_id', $cashier_id)
    //         ->where('branch_id', $branch_id)
    //         ->whereNull('channel')
    //         ->whereDate('created_at', today())
    //         ->get();

    //     $todayExpenses = Expense::where('payer_id', $cashier_id)
    //         ->where('branch_id', $branch_id)
    //         ->whereDate('created_at', today())
    //         ->get();

    //     $creditPayments = Payment::where('user_id', $cashier_id)
    //         ->where('branch_id', $branch_id)
    //         ->whereDate('created_at', today())
    //         ->get();

    //     $transfers = FundTransfer::where('cashier_id', $cashier_id)
    //         ->whereDate('created_at', Carbon::today())
    //         ->where('branch_id', $branch_id)
    //         ->get();

    //     $balances = $this->calculateBalances($todaySales, $todayReturns, $todayExpenses, $creditPayments, $transfers, $paymentMethod);

    //     return $balances;
    // }

    // private function calculateBalances($sales, $returns, $expenses, $creditPayments, $transfers, $paymentMethod)
    // {
    //     $salesAmount = $sales->where('payment_method', $paymentMethod)->sum(function ($sale) {
    //         return $sale->price * $sale->quantity;
    //     });

    //     $returnsAmount = $returns->where('payment_method', $paymentMethod)->sum(function ($return) {
    //         return $return->price * $return->quantity;
    //     });

    //     $expensesAmount = $expenses->where('payment_method', $paymentMethod)->sum('amount');

    //     $creditPaymentsAmount = $creditPayments->where('payment_method', $paymentMethod)->sum('payment_amount');

    //     $transfersFromAmount = $transfers->where('from_account', $paymentMethod)->sum('amount');

    //     $transfersToAmount = $transfers->where('to_account', $paymentMethod)->sum('amount');

    //     $balance = $salesAmount - ($returnsAmount + $expensesAmount) + $creditPaymentsAmount + $transfersToAmount - $transfersFromAmount;

    //     return $balance;
    // }



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

        return $balance;
    }
}
