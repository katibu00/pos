<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ExpenseCategory;
use App\Models\ExpenseRecord;
use Illuminate\Http\Request;

class ExpenseRecordController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $branches = $user->usertype === 'cashier' ? [$user->branch] : Branch::all();
        $categories = ExpenseCategory::all();
    
        $query = ExpenseRecord::with('branch', 'category', 'user');
    
        if ($user->usertype === 'cashier') {
            $query->where('user_id', $user->id);
        }
    
        $recentExpenses = $query->latest()->paginate(10);
    
        $availableBalance = $user->branch->expense_balance;
        $todayExpenses = $query->whereDate('created_at', today())->sum('amount');
        $last30DaysExpenses = $query->whereDate('created_at', '>=', now()->subDays(30))->sum('amount');
    
        return view('expense.records', compact('branches', 'categories', 'recentExpenses', 'availableBalance', 'todayExpenses', 'last30DaysExpenses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $branch = Branch::find($validated['branch_id']);

        if ($branch->expense_balance < $validated['amount']) {
            return response()->json(['success' => false, 'message' => 'Insufficient funds in the branch.'], 422);
        }

        $expense = ExpenseRecord::create([
            'branch_id' => $validated['branch_id'],
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'note' => $validated['note'],
            'user_id' => auth()->id(),
        ]);

        $branch->decrement('expense_balance', $validated['amount']);

        return response()->json(['success' => true, 'expense' => $expense]);
    }
}
