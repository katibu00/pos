<?php

namespace App\Http\Controllers;

use App\Models\CashCredit;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FundTransfer;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\Sale;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpensesController extends Controller
{
    public function index()
    {
        $data['expense_cats'] = ExpenseCategory::all();
        if (auth()->user()->usertype == 'admin') {
            $data['dates'] = Expense::select('date')->where('branch_id', auth()->user()->branch_id)->distinct('date')->orderBy('date', 'desc')->paginate(15);
        } else {
            $data['dates'] = Expense::select('date')->where('branch_id', auth()->user()->branch_id)->where('payer_id', auth()->user()->id)->distinct('date')->orderBy('date', 'desc')->paginate(15);
        }
        return view('expense.index', $data);
    }

    public function store(Request $request)
    {
        $dataCount = count($request->expense_category_id);
        if ($dataCount != null) {
            // Calculate total expenses
            $totalExpenses = array_sum($request->amount);

            // Get unique payment methods
            $uniquePaymentMethods = array_unique($request->payment_method);

            // Calculate total balance across all payment methods
            $totalBalance = 0;
            $minimumBalance = 0;
            foreach ($uniquePaymentMethods as $paymentMethod) {
                $balanceInfo = $this->getTodayBalance($paymentMethod);
                $totalBalance += $balanceInfo['current_balance'];
                $minimumBalance += $balanceInfo['minimum_balance'];
            }

            // Check if total expenses exceed total balance or minimum balance
            if ($totalBalance <= 0 || ($totalBalance - $totalExpenses) < $minimumBalance) {
                Toastr::error('Insufficient funds. Total expenses exceed available balance.');
                return redirect()->route('expense.index');
            }

            // If balance is sufficient, process individual expenses
            for ($i = 0; $i < $dataCount; $i++) {
                $data = new Expense();
                $data->expense_category_id = $request->expense_category_id[$i];
                $data->amount = $request->amount[$i];
                $data->description = $request->description[$i];
                $data->payment_method = $request->payment_method[$i];
                $data->payer_id = auth()->user()->id;
                $data->branch_id = auth()->user()->branch_id;
                $data->date = $request->date;
                $data->save();
            }

            Toastr::success('Expenses Recorded successfully', 'Done');
            return redirect()->route('expense.index');
        }

        Toastr::error('No expenses to record');
        return redirect()->route('expense.index');
    }

    private function getTodayBalance($paymentMethod)
    {
        $branch_id = auth()->user()->branch_id;
        $today = Carbon::today();

        // Get sales for today
        $sales = Sale::where('branch_id', $branch_id)
            ->whereDate('created_at', $today)
            ->where(function ($query) use ($paymentMethod) {
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

        $minimumBalance = 500; // Set the minimum balance

        return [
            'current_balance' => $balance,
            'minimum_balance' => $minimumBalance,
        ];
    }
}
