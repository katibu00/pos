<?php

namespace App\Http\Controllers;

use App\Models\CashCredit;
use App\Models\CashCreditPayment;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FundTransfer;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\Sale;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
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

               
                if ($request->amount[$i] > $this->getBalances($request->payment_method[$i])) {
                    
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


    private function getBalances($paymentMethod)
    {
        $branch_id = auth()->user()->branch_id;
        $cashier_id = auth()->user()->id;

        $todaySales = Sale::where('user_id', $cashier_id)
            ->where('branch_id', $branch_id)
            ->whereNotIn('stock_id', [1093, 1012])
            ->whereDate('created_at', today())
            ->get();

        $todayReturns = Returns::where('cashier_id', $cashier_id)
            ->where('branch_id', $branch_id)
            ->whereNull('channel')
            ->whereDate('created_at', today())
            ->get();

        $todayExpenses = Expense::where('payer_id', $cashier_id)
            ->where('branch_id', $branch_id)
            ->whereDate('created_at', today())
            ->get();

        $creditPayments = Payment::where('user_id', $cashier_id)
            ->where('branch_id', $branch_id)
            ->whereDate('created_at', today())
            ->get();

        $transfers = FundTransfer::where('cashier_id', $cashier_id)
            ->whereDate('created_at', Carbon::today())
            ->where('branch_id', $branch_id)
            ->get();

        $balances = $this->calculateBalances($todaySales, $todayReturns, $todayExpenses, $creditPayments, $transfers, $paymentMethod);

        return $balances;
    }

    private function calculateBalances($sales, $returns, $expenses, $creditPayments, $transfers, $paymentMethod)
    {
        $salesAmount = $sales->where('payment_method', $paymentMethod)->sum(function ($sale) {
            return $sale->price * $sale->quantity;
        });

        $returnsAmount = $returns->where('payment_method', $paymentMethod)->sum(function ($return) {
            return $return->price * $return->quantity;
        });

        $expensesAmount = $expenses->where('payment_method', $paymentMethod)->sum('amount');

        $creditPaymentsAmount = $creditPayments->where('payment_method', $paymentMethod)->sum('payment_amount');

        $transfersFromAmount = $transfers->where('from_account', $paymentMethod)->sum('amount');

        $transfersToAmount = $transfers->where('to_account', $paymentMethod)->sum('amount');

        $balance = $salesAmount - ($returnsAmount + $expensesAmount) + $creditPaymentsAmount + $transfersToAmount - $transfersFromAmount;

        return $balance;
    }
}
