<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvance;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryAdvanceController extends Controller
{
    public function cashierIndex()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $data['advances'] = SalaryAdvance::where('cashier_id', auth()->user()->id)
            ->whereBetween('created_at', [$currentMonth, Carbon::now()])
            ->orderBy('created_at', 'desc')
            ->get();

        $data['staffs'] = User::whereNotIn('usertype', ['admin', 'customer'])
            ->where('branch_id', auth()->user()->branch_id)
            ->orderBy('first_name')
            ->get();

        return view('users.salary_advance.cashier_index', $data);
    }

    public function cashierStore(Request $request)
    {
        $collectedAmount = SalaryAdvance::where('staff_id', $request->staff_id)->sum('amount');

        $maxSalary = User::where('id', $request->staff_id)->value('max_salary');

        $requestedAmount = $request->amount;
        $totalAmountCollected = $collectedAmount + $requestedAmount;

        if ($totalAmountCollected > $maxSalary) {
            Toastr::warning('The requested amount exceeds the maximum salary limit.');
            return redirect()->route('cashier.salary_advance.index');
        }

        $new = new SalaryAdvance();
        $new->cashier_id = auth()->user()->id;
        $new->staff_id = $request->staff_id;
        $new->amount = $requestedAmount;
        $new->save();

        Toastr::success('Salary Advance Applied Successfully');

        if (auth()->user()->usertype == 'admin') {
            return redirect()->route('admin.salary_advance.index');
        } else {
            return redirect()->route('cashier.salary_advance.index');
        }
    }

    public function adminIndex()
    {
        $data['staffs'] = User::whereNotIn('usertype', ['admin', 'customer'])
            ->orderBy('first_name')
            ->get();
        return view('users.salary_advance.admin_index', $data);
    }

    public function approve(Request $request)
    {
        $salary = SalaryAdvance::find($request->id);
        $salary->status = 'approved';
        $salary->update();

        return response()->json([
            'status' => 200,
            'message' => 'Request Approved Successfully',
        ]);
    }
    public function reject(Request $request)
    {
        $salary = SalaryAdvance::find($request->id);
        $salary->status = 'rejected';
        $salary->update();

        return response()->json([
            'status' => 200,
            'message' => 'Request Rejected Successfully',
        ]);
    }
    public function delete(Request $request)
    {
        $salary = SalaryAdvance::find($request->id);
        $salary->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Request Deleted Successfully',
        ]);
    }

    public function fetchSalaryAdvances(Request $request)
    {
        $selectedMonth = $request->input('month');

        $selectedMonth = $selectedMonth ?? Carbon::now()->month;

        $data['advances'] = SalaryAdvance::where('cashier_id', auth()->user()->id)
            ->whereMonth('created_at', $selectedMonth)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('users.salary_advance.cashier_index_table', $data)->render();
        // return view('sales.all_table', $data)->render();

    }
}
