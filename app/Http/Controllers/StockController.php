<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Stock;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class StockController extends Controller
{
    function index(){
        $data['stocks'] = Stock::where('branch_id',0)->paginate(25);
        $data['branches'] = Branch::all();
        return view('stock.index',$data);
    }

    function store(Request $request){


        $productCount = count($request->name);
        if($productCount != NULL){
            for ($i=0; $i < $productCount; $i++){
                $data = new Stock();
                $data->branch_id = $request->branch_id;
                $data->name = $request->name[$i];
                $data->buying_price = $request->buying_price[$i];
                $data->selling_price = $request->selling_price[$i];
                $data->quantity = $request->quantity[$i];
                $data->critical_level = $request->critical_level[$i];
                $data->save();
            }
        }
        Toastr::success('Inventories has been added sucessfully', 'Done');
        return redirect()->route('stock.index');

    }

    function edit($id){
        $data['stock'] = Stock::find($id);
        return view('stock.edit',$data);
    }

    function copyIndex($id){
        $data['stock'] = Stock::find($id);
        $data['branches'] = Branch::all();
        return view('stock.copy',$data);
    }

    function update(Request $request, $id){
       
        $data = Stock::find($id);
        $data->name = $request->name;
        $data->buying_price = $request->buying_price;
        $data->selling_price = $request->selling_price;
        $data->quantity = $request->quantity;
        $data->critical_level = $request->critical_level;

        $data->update();
        Toastr::success('Inventory has been updated sucessfully', 'Done');
        return redirect()->route('stock.index');
    }

    function copyStore(Request $request){
       
        $data = new Stock();
        $data->branch_id = $request->branch_id;
        $data->name = $request->name;
        $data->buying_price = $request->buying_price;
        $data->selling_price = $request->selling_price;
        $data->quantity = $request->quantity;
        $data->critical_level = $request->critical_level;

        $data->save();
        Toastr::success('Inventory has been Copied sucessfully', 'Done');
        return redirect()->route('stock.index');
    }

    function delete(Request $request){
       
        $data = Stock::find($request->id);
        
        $data->delete();
       
        return response()->json([
            'status' => 200,
            'message' => 'Product Deleted Succesffully',
        ]);
    }

    public function fetchStocks(Request $request)
    {
        $data['stocks'] = Stock::where('branch_id', $request->branch_id)->paginate(25);
        return view('stock.table', $data)->render();
      
    }


    public function paginate(Request $request)
    {
        $data['stocks'] = Stock::where('branch_id', $request->branch_id)->paginate(25);
        return view('stock.table', $data)->render();
    }


    public function Search(Request $request)
    {

        
        $data['stocks'] = Stock::where('branch_id', $request->branch_id)->where('name', 'like','%'.$request['query'].'%')->paginate(25);

        if( $data['stocks']->count() > 0 )
        {
            return view('stock.table', $data)->render();
        }else
        {
            return response()->json([
                'status' => 404,
            ]);
        }
    }
}
