<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function index()
    {
        $data['expense_cats'] = ExpenseCategory::all();
        if(auth()->user()->usertype == 'admin')
        {
            $data['dates'] = Expense::select('date')->where('branch_id', auth()->user()->branch_id)->distinct('date')->orderBy('date','desc')->limit(15)->get();
        }
        else
        {
            $data['dates'] = Expense::select('date')->where('branch_id', auth()->user()->branch_id)->where('payer_id',auth()->user()->id)->distinct('date')->orderBy('date','desc')->limit(15)->get();
        }
        return view('expense.index',$data);
    }

    public function store(Request $request)
    {
        // return $request->all();

        $dataCount = count($request->expense_category_id);
        if($dataCount != NULL){
            for ($i=0; $i < $dataCount; $i++){
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
        }

        Toastr::success('Expenses Recorded sucessfully', 'Done');
        return redirect()->route('expense.index');
    }
}
