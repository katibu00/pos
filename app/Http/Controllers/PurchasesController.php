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



    function store(Request $request){


        $productCount = count($request->product_id);
        if($productCount != NULL){
            for ($i=0; $i < $productCount; $i++){
                $data = Stock::find($request->product_id[$i]);
                $data->quantity += $request->quantity[$i];
                $data->update();

                $data = new Purchase();
                $data->branch_id = $request->branch;
                $data->stock_id = $request->product_id[$i];
                $data->quantity = $request->quantity[$i];
                $data->date = $request->date;
                $data->save();
            }
        }
        Toastr::success('Purchases has been added sucessfully', 'Done');
        return redirect()->route('purchase.index');

    }

    function details($date){

        $data['purchases'] = Purchase::whereDate('date', $date)->get();
        // dd( $data['purchases']);
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

    public function fetchPurchases(Request $request)
    {
        $data['purchases'] = Purchase::select('date')->where('branch_id', $request->branch_id)->groupBy('date')->paginate(15);
        return view('purchases.table', $data)->render();
    }


}
