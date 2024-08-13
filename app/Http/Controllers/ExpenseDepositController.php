<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ExpenseDeposit;
use Illuminate\Http\Request;

class ExpenseDepositController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        $recentDeposits = ExpenseDeposit::with('branch', 'user')->latest()->paginate(10);
        if (request()->ajax()) {
            return view('expense.deposits', compact('branches', 'recentDeposits'));
        }
        return view('expense.deposits', compact('branches', 'recentDeposits'));
    }

    public function getBalanceCards()
    {
        $branches = Branch::all();
        return view('expense.balance_cards', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $deposit = ExpenseDeposit::create([
            'branch_id' => $validated['branch_id'],
            'amount' => $validated['amount'],
            'note' => $validated['note'],
            'user_id' => auth()->id(),
        ]);

        $branch = Branch::find($validated['branch_id']);
        $branch->increment('expense_balance', $validated['amount']);

        return response()->json(['success' => true, 'deposit' => $deposit]);
    }

}
