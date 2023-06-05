<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvance;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Carbon\Carbon;


class SalaryAdvanceController extends Controller
{
    public function cashierIndex()
    {
        $data['advances'] = SalaryAdvance::where('cashier_id', auth()->user()->id)
        ->where('created_at', '>=', Carbon::now()->subDays(40))
        ->get();
        $data['staffs'] = User::whereNotIn('usertype', ['admin', 'customer'])
                                    ->where('branch_id', auth()->user()->branch_id)
                                    ->orderBy('first_name')
                                    ->get();
        return view('users.salary_advance.cashier_index', $data);
    }
    public function cashierStore(Request $request)
    {
        $new = new SalaryAdvance();
        $new->cashier_id = auth()->user()->id;
        $new->staff_id = $request->staff_id;
        $new->amount = $request->amount;
        $new->save();

        Toastr::success('Salary Advance Applied Successfully');

        if(auth()->user()->usertype == 'admin')
        {
            return redirect()->route('admin.salary_advance.index');
        }else
        {
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
            'message' => 'Request Approved Successfully'
        ]);
    }
    public function reject(Request $request)
    {
        $salary = SalaryAdvance::find($request->id);
        $salary->status = 'rejected';
        $salary->update();

        return response()->json([
            'status' => 200,
            'message' => 'Request Rejected Successfully'
        ]);
    }
    public function delete(Request $request)
    {
        $salary = SalaryAdvance::find($request->id);
       $salary->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Request Deleted Successfully'
        ]);
    }
}
