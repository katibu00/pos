<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\FundTransfer;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FundTransferController extends Controller
{
    public function index()
    {
        $fundTransfers = FundTransfer::where('branch_id', auth()->user()->branch_id)->latest()->paginate(10);

        return view('fund_transfer.index', ['fundTransfers' => $fundTransfers]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'from_account' => [
                'required',
                'in:cash,transfer,pos',
                Rule::notIn([$request->input('to_account')]),
            ],
            'to_account' => 'required|in:cash,transfer,pos',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            // Return a JSON response with validation errors
            return response()->json([
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        if ($request->input('amount') > $this->getBalances($request->input('from_account'))) {
            return response()->json([
                'errors' => ['Insufficient balance in the selected account.'],
            ], 422);
        }

        $fundTransfer = new FundTransfer();
        $fundTransfer->description = $request->input('description');
        $fundTransfer->from_account = $request->input('from_account');
        $fundTransfer->to_account = $request->input('to_account');
        $fundTransfer->amount = $request->input('amount');
        $fundTransfer->cashier_id = auth()->user()->id;
        $fundTransfer->branch_id = auth()->user()->branch_id;
        $fundTransfer->save();

        return response()->json([
            'success' => true,
            'message' => 'Funds transfer created successfully!',
        ]);
    }

    private function getBalances($paymentMethod)
    {
        $branch_id = auth()->user()->branch_id;
        $cashier_id = auth()->user()->id;

        $todaySales = Sale::where('user_id', $cashier_id)
            ->where('branch_id', $branch_id)
            ->whereNotIn('stock_id', [1093, 1012])
            ->whereDate('created_at', today())
            ->get();

        $todayReturns = Returns::where('cashier_id', $cashier_id)
            ->where('branch_id', $branch_id)
            ->whereNull('channel')
            ->whereDate('created_at', today())
            ->get();

        $todayExpenses = Expense::where('payer_id', $cashier_id)
            ->where('branch_id', $branch_id)
            ->whereDate('created_at', today())
            ->get();

        $creditPayments = Payment::where('user_id', $cashier_id)
            ->where('branch_id', $branch_id)
            ->whereDate('created_at', today())
            ->get();

        $transfers = FundTransfer::where('cashier_id', $cashier_id)
            ->whereDate('created_at', Carbon::today())
            ->where('branch_id', $branch_id)
            ->get();

        $balances = $this->calculateBalances($todaySales, $todayReturns, $todayExpenses, $creditPayments, $transfers, $paymentMethod);

        return $balances;
    }

    private function calculateBalances($sales, $returns, $expenses, $creditPayments, $transfers, $paymentMethod)
    {
        $salesAmount = $sales->where('payment_method', $paymentMethod)->sum(function ($sale) {
            return $sale->price * $sale->quantity;
        });

        $returnsAmount = $returns->where('payment_method', $paymentMethod)->sum(function ($return) {
            return $return->price * $return->quantity;
        });

        $expensesAmount = $expenses->where('payment_method', $paymentMethod)->sum('amount');

        $creditPaymentsAmount = $creditPayments->where('payment_method', $paymentMethod)->sum('payment_amount');

        $transfersFromAmount = $transfers->where('from_account', $paymentMethod)->sum('amount');

        $transfersToAmount = $transfers->where('to_account', $paymentMethod)->sum('amount');

        $balance = $salesAmount - ($returnsAmount + $expensesAmount) + $creditPaymentsAmount + $transfersToAmount - $transfersFromAmount;

        return $balance;
    }

}
