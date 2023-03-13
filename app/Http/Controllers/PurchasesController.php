<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Purchase;
use App\Models\Stock;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class PurchasesController extends Controller
{
    function index(){
        $data['branches'] = Branch::all();

        $data['purchases'] = Purchase::select('date')->where('branch_id',0)->groupBy('date')->paginate(15);
        return view('purchases.index',$data);
    }

    function create(){
        $data['branches'] = Branch::all();
        $data['products'] = Stock::where('branch_id', auth()->user()->branch_id)->orderBy('name')->get();
        return view('purchases.create',$data);
    }
    function shopping_list(){

        $data['branches'] = Branch::all();
        $data['lows'] = [];
        return view('purchases.shopping_list',$data);
    }



    function store(Request $request){
        // dd($request->all());
        $productCount = count($request->product_id);
        if($productCount != NULL){
            for ($i=0; $i < $productCount; $i++){
                $data = Stock::find($request->product_id[$i]);
                $data->quantity += $request->quantity[$i];
                if($request->buying_price[$i] != '')
                {
                    $data->buying_price = $request->buying_price[$i];  
                }
                if($request->selling_price[$i] != '')
                {
                    $data->selling_price = $request->selling_price[$i];  
                }
                $data->update();

                $data = new Purchase();
                $data->branch_id = auth()->user()->branch_id;
                $data->stock_id = $request->product_id[$i];
                $data->quantity = $request->quantity[$i];
                $data->date = $request->date;
                $data->save();
            }
        }
      
        return response()->json([
            'status' => 201,
            'message' => 'Purchases has been added sucessfully',
            ]);

    }

    function details($date){

        $data['purchases'] = Purchase::whereDate('date', $date)->get();
        return view('purchases.details',$data);
    }

    
    public function fetchStocks(Request $request)
    {
        $stocks = Stock::where('branch_id', $request->branch_id)->get();
        return response()->json([
        'status' => 200,
        'stocks' => $stocks,
        ]);
      
    }

    public function fetchShopList(Request $request)
    {
        $lows = [];
        $stocks = Stock::where('branch_id', $request->branch_id)->get();
        foreach($stocks as $stock){

            if($stock->quantity <= $stock->critical_level){
                array_push($lows, $stock);
            }
        }
        $data['lows'] = $lows;
        return view('purchases.shopping_list_table', $data)->render();
    }
    public function fetchPurchases(Request $request)
    {
        $data['purchases'] = Purchase::select('date')->where('branch_id', $request->branch_id)->groupBy('date')->orderBy('created_at','desc')->paginate(15);
        return view('purchases.table', $data)->render();
    }


}
