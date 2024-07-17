<?php

namespace App\Http\Controllers;

use App\Models\CashCredit;
use App\Models\Expense;
use App\Models\FundTransfer;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $balanceInfo = $this->getTodayBalance($request->input('from_account'));
        $currentBalance = $balanceInfo['current_balance'];
        $minimumBalance = $balanceInfo['minimum_balance'];
        
        if ($currentBalance <= 0 || ($currentBalance - $request->input('amount')) < $minimumBalance) {
            return response()->json([
                'errors' => ['Insufficient funds. Please ensure you maintain a minimum balance of ' . $minimumBalance . ' in the selected account.'],
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


}
