<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\Sale;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function index()
    {
        $data['expense_cats'] = ExpenseCategory::all();
        if(auth()->user()->usertype == 'admin')
        {
            $data['dates'] = Expense::select('date')->where('branch_id', auth()->user()->branch_id)->distinct('date')->orderBy('date','desc')->paginate(15);
        }
        else
        {
            $data['dates'] = Expense::select('date')->where('branch_id', auth()->user()->branch_id)->where('payer_id',auth()->user()->id)->distinct('date')->orderBy('date','desc')->paginate(15);
        }
        return view('expense.index',$data);
    }

    public function store(Request $request)
    {

        $dataCount = count($request->expense_category_id);
        if($dataCount != NULL){
            for ($i=0; $i < $dataCount; $i++){

                $todaySales = Sale::where('branch_id', auth()->user()->branch_id)->where('payment_method', $request->payment_method[$i])->where('stock_id','!=',1012)->whereDate('created_at', today())->get();
                $todayReturns = Returns::where('branch_id', auth()->user()->branch_id)->where('payment_method', $request->payment_method[$i])->whereDate('created_at', today())->get();
                $expenses = Expense::where('branch_id', auth()->user()->branch_id)->where('payment_method', $request->payment_method[$i])->whereDate('created_at', today())->sum('amount');
                $payments = Payment::where('branch_id', auth()->user()->branch_id)->where('payment_method', $request->payment_method[$i])->whereDate('created_at', today())->sum('payment_amount');
        
                $sales =  $todaySales->reduce(function ($total, $sale) {
                    $total += ($sale->price * $sale->quantity) - $sale->discount;
                    return $total;
                }, 0);
                $returns =  $todayReturns->reduce(function ($total, $return) {
                    $total += ($return->price * $return->quantity) - $return->discount;
                    return $total;
                }, 0);
        
                $net_amount = ((float)$sales+(float)$payments) - (float)$returns - (float)$expenses;
               
                if((float)$request->amount[$i] > (float)$net_amount)
                {
                    Toastr::error('Low Balance in the Payment Channel.');
                    return redirect()->route('expense.index');
                }

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
