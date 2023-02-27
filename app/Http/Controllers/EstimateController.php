<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\Sale;
use App\Models\Stock;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EstimateController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data['products'] = Stock::where('branch_id', $user->branch_id)->orderBy('name')->get();
        $data['recents'] = Estimate::select('product_id','estimate_no')->whereDate('created_at', Carbon::today())->where('cashier_id',auth()->user()->id)->groupBy('estimate_no')->orderBy('created_at','desc')->take(4)->get();
        return view('estimate.index', $data); 
    }
    public function allIndex()
    {
 
        $user = auth()->user();
        if($user->usertype == 'admin')
        {
            $data['estimates'] = Estimate::select('product_id','estimate_no')->groupBy('estimate_no')->orderBy('created_at','desc')->paginate(10);
        }else
        {
            $data['estimates'] = Estimate::select('product_id','estimate_no')->where('cashier_id',auth()->user()->id)->groupBy('estimate_no')->orderBy('created_at','desc')->paginate(5);
        }
        return view('estimate.all_index', $data); 
    }

    public function store(Request $request)
    {
        $year = date('Y');
        $month = Carbon::now()->format('m');
        $day = Carbon::now()->format('d');
        $last = Estimate::whereDate('created_at', '=', date('Y-m-d'))->latest()->first();
        if ($last == null) {
            $last_record = '1/0';
        } else {
            $last_record = $last->estimate_no;
        }
        $exploded = explode("/", $last_record);
        $number = $exploded[1] + 1;
        $padded = sprintf("%04d", $number);
        $stored = $year . $month . $day . '/' . $padded;

        $productCount = count($request->product_id);
        if ($productCount != null) {
            for ($i = 0; $i < $productCount; $i++) {

                $data = new Estimate();
                $data->branch_id = auth()->user()->branch_id;
                $data->estimate_no = $stored;
                $data->product_id = $request->product_id[$i];
                $data->price = $request->price[$i];
                $data->quantity = $request->quantity[$i];
                if($request->discount[$i] == null){
                    $data->discount = 0;

                }else{
                    $data->discount = $request->discount[$i];
                }
                $data->cashier_id = auth()->user()->id;
                $data->customer = $request->customer_name;
                $data->note = $request->note;
                $data->save();
            }
        }

        
        return response()->json([
            'status' => 201,
            'message' => 'Estimate has been Saved sucessfully',
        ]);

    }

    public function refresh()
    {
        $data['recents'] = Estimate::select('product_id','estimate_no')->whereDate('created_at', Carbon::today())->where('cashier_id',auth()->user()->id)->groupBy('estimate_no')->orderBy('created_at','desc')->take(4)->get();
        return view('estimate.recents_table', $data)->render();
    }
    public function loadReceipt(Request $request)
    {
        $items = Estimate::with('product')->where('estimate_no', $request->estimate_no)->get();
        return response()->json([
            'status' => 200,
            'items' => $items,
        ]);
    }

    public function allStore(Request $request)
    {
        $estimates = Estimate::where('estimate_no', $request->estimate_no)->get();

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

        foreach($estimates as $estimate)
        {
            $data = new Sale();
            $data->branch_id = auth()->user()->branch_id;
            $data->receipt_no = $stored;
            $data->stock_id = $estimate->product_id;
            $data->price = $estimate->price;
            $data->quantity = $estimate->quantity;
            if($estimate->discount == null){
                $data->discount = 0;

            }else{
                $data->discount = $estimate->discount;
            }
            $data->payment_method = $request->payment_method;
            $data->payment_amount = 0;
            $data->user_id = auth()->user()->id;
            $data->customer_name = $estimate->customer_name;
            $data->note = null;

            $data->save();

            $data = Stock::find($estimate->product_id);
            $data->quantity -= $estimate->quantity;
            $data->update();

            $estimate->delete();
        }

      
        Toastr::success('Estimate has been Marked as Sold sucessfully', 'Done');
        return redirect()->route('estimate.all.index'); 
    }


}
