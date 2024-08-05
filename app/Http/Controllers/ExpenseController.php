<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('cashier_id', auth()->id())->paginate(10);
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $account = ExpenseAccount::where('branch_id', auth()->user()->branch_id)->first();
        return view('expenses.create', compact('account'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $account = ExpenseAccount::where('branch_id', auth()->user()->branch_id)->firstOrFail();

        if ($account->daily_limit) {
            $todayExpenses = $account->expenses()
                ->whereDate('created_at', today())
                ->sum('amount');

            if ($todayExpenses + $validatedData['amount'] > $account->daily_limit) {
                return back()->withErrors(['amount' => 'This expense would exceed the daily limit.']);
            }
        }

        if ($validatedData['amount'] > $account->balance) {
            return back()->withErrors(['amount' => 'Insufficient funds in the expense account.']);
        }

        DB::transaction(function () use ($account, $validatedData) {
            $expense = new Expense([
                'cashier_id' => auth()->id(),
                'description' => $validatedData['description'],
                'amount' => $validatedData['amount'],
            ]);

            $account->expenses()->save($expense);
            $account->decrement('balance', $validatedData['amount']);
        });

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }
}