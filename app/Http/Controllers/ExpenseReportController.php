<?php

namespace App\Http\Controllers;

use App\Models\ExpenseAccount;
use App\Models\ExpenseDeposit;
use App\Models\ExpenseRecord;
use App\Models\Branch;
use Illuminate\Http\Request;

class ExpenseReportController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::all();
        $selectedBranch = $request->input('branch_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = ExpenseAccount::query();

        if ($selectedBranch) {
            $query->where('branch_id', $selectedBranch);
        }

        $accounts = $query->with(['branch', 'deposits', 'expenseRecords'])->get();

        $depositQuery = ExpenseDeposit::query();
        $expenseQuery = ExpenseRecord::query();

        if ($selectedBranch) {
            $depositQuery->whereHas('expenseAccount', function ($q) use ($selectedBranch) {
                $q->where('branch_id', $selectedBranch);
            });
            $expenseQuery->whereHas('expenseAccount', function ($q) use ($selectedBranch) {
                $q->where('branch_id', $selectedBranch);
            });
        }

        if ($startDate) {
            $depositQuery->whereDate('created_at', '>=', $startDate);
            $expenseQuery->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $depositQuery->whereDate('created_at', '<=', $endDate);
            $expenseQuery->whereDate('created_at', '<=', $endDate);
        }

        $deposits = $depositQuery->with('admin')->get();
        $expenses = $expenseQuery->with('user')->get();

        return view('expense-reports.index', compact('branches', 'selectedBranch', 'startDate', 'endDate', 'accounts', 'deposits', 'expenses'));
    }
}