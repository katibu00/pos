<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvance;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class SalaryAdvanceController extends Controller
{
    public function cashierIndex()
    {
        $data['advances'] = SalaryAdvance::where('cashier_id', auth()->user()->id)->get();
        return view('users.salary_advance.cashier_index', $data);
    }
    public function cashierStore(Request $request)
    {
        $new = new SalaryAdvance();
        $new->cashier_id = auth()->user()->id;
        $new->amount = $request->amount;
        $new->save();

        Toastr::success('Salary Advance Applied Successfully');
        return redirect()->route('cashier.salary_advance.index');
    }

    public function adminIndex()
    {
        // $data['advances'] = SalaryAdvance::where('branch_i,,d', auth()->user()->branch_id)->get();
        $data['staffs'] = User::where('usertype', '!=', 'customer')->where('usertype', '!=', 'admin')->get();
        return view('users.salary_advance.admin_index', $data);
    }
}
