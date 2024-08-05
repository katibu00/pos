<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ExpenseAccount;
use App\Models\ExpenseDeposit;
use Illuminate\Http\Request;

class ExpenseAccountController extends Controller
{
    public function index()
    {
        $accounts = ExpenseAccount::with('branch')->paginate(10);
        return view('expense-accounts.index', compact('accounts'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('expense-accounts.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'balance' => 'required|numeric|min:0',
            'daily_limit' => 'nullable|numeric|min:0',
        ]);

        ExpenseAccount::create($validatedData);

        return redirect()->route('expense-accounts.index')->with('success', 'Expense account created successfully.');
    }

    public function deposit(Request $request, ExpenseAccount $account)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $deposit = new ExpenseDeposit([
            'admin_id' => auth()->id(),
            'amount' => $validatedData['amount'],
        ]);

        $account->deposits()->save($deposit);
        $account->increment('balance', $validatedData['amount']);

        return redirect()->route('expense-accounts.show', $account)->with('success', 'Deposit recorded successfully.');
    }

    public function setLimit(Request $request, ExpenseAccount $account)
    {
        $validatedData = $request->validate([
            'daily_limit' => 'nullable|numeric|min:0',
        ]);

        $account->update($validatedData);

        return redirect()->route('expense-accounts.show', $account)->with('success', 'Daily limit updated successfully.');
    }

    // Implement other CRUD methods (show, edit, update, destroy) as needed
}