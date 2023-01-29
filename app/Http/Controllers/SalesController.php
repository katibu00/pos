<?php

namespace App\Http\Controllers;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data['products'] = Stock::where('branch_id', $user->branch_id)->orderBy('name')->get();
        $data['recents'] = Sale::select('stock_id','receipt_no')->groupBy('receipt_no')->orderBy('created_at','desc')->take(4)->get();
        $data['sold_items'] = [];
        return view('sales.index', $data);
       

    }

    public function store(Request $request)
    {
        $year = date('Y');
        $month = Carbon::now()->format('m');
        $day = Carbon::now()->format('d');
        $last = Sale::whereDate('created_at', '=', date('Y-m-d'))->latest()->first();
        if ($last == null) {
            $last_record = '1/0';
        } else {
            $last_record = $last->receipt_no;
        }
        $exploded = explode("/", $last_record);
        $number = $exploded[1] + 1;
        $padded = sprintf("%04d", $number);
        $stored = $year . $month . $day . '/' . $padded;

        $productCount = count($request->product_id);
        if ($productCount != null) {
            for ($i = 0; $i < $productCount; $i++) {

                $data = new Sale();
                $data->branch_id = auth()->user()->branch_id;
                $data->receipt_no = $stored;
                $data->stock_id = $request->product_id[$i];
                $data->price = $request->price[$i];
                $data->quantity = $request->quantity[$i];
                if($request->discount[$i] == null){
                    $data->discount = 0;

                }else{
                    $data->discount = $request->discount[$i];
                }
                $data->payment_method = $request->payment_method;
                $data->payment_amount = $request->paid_amount;
                $data->user_id = auth()->user()->id;
                $data->customer_name = $request->customer_name;
                $data->note = $request->note;
             

                $data->save();

                $data = Stock::find($request->product_id[$i]);
                $data->quantity -= $request->quantity[$i];
                $data->update();

            }
        }

        
        return response()->json([
            'status' => 201,
            'message' => 'Sale has been recorded sucessfully',
        ]);

    }

    public function refresh(Request $request)
    {
        $data['recents'] = Sale::select('stock_id','receipt_no')->groupBy('receipt_no')->orderBy('created_at','desc')->take(4)->get();
        return view('sales.recent_sales_table', $data)->render();
    }
    public function loadReceipt(Request $request)
    {
        $items = Sale::with('product')->where('receipt_no', $request->receipt_no)->get();
       
        // return view('sales.receipt', $data)->render();

        return response()->json([
            'status' => 200,
            'items' => $items,
        ]);
    }

    public function details(Request $request)
    {
        return Sale::with(['product', 'user'])->where('receipt_no', $request->receipt_no)->get();
    }

    public function fetchSales(Request $request)
    {
        $data['sales'] = Sale::select('id', 'receipt_no')->where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->groupBy('receipt_no')->latest()->get();

        $user = User::find(auth()->user()->id);
        $user->branch_id = $request->branch_id;
        $user->update();
        return view('sales.table', $data)->render();
    }

    public function adminSearch(Request $request)
    {

        $data['sales'] = Sale::select('id', 'receipt_no')->where('receipt_no', 'like', '%' . $request['query'] . '%')->groupBy('receipt_no')->latest()->get();

        if ($data['sales']->count() > 0) {
            return view('sales.table', $data)->render();
        } else {
            return response()->json([
                'status' => 404,
            ]);
        }

    }
    public function cashierSearch(Request $request)
    {

        $data['sales'] = Sale::select('id', 'receipt_no')->where('branch_id', auth()->user()->branch_id)->where('receipt_no', 'like', '%' . $request['query'] . '%')->groupBy('receipt_no')->latest()->get();

        if ($data['sales']->count() > 0) {
            return view('sales.cashier_table', $data)->render();
        } else {
            return response()->json([
                'status' => 404,
            ]);
        }

    }

}
