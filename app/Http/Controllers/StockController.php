<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Stock;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Sale;
use Carbon\Carbon;

class StockController extends Controller
{
    function index(){
        $data['stocks'] = Stock::where('branch_id',0)->paginate(25);
        if (in_array(auth()->user()->id, [4, 443])) {
            $data['branches'] = Branch::all();
        } else {
            $data['branches'] = Branch::where('id', auth()->user()->branch_id)->get();
        }   
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
        if (in_array(auth()->user()->id, [4, 443])) {
            $data['branches'] = Branch::all();
        } else {
            $data['branches'] = Branch::where('id', auth()->user()->branch_id)->get();
        }   
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



    function correctIndex(){
        if (in_array(auth()->user()->id, [4, 443])) {
            $data['branches'] = Branch::all();
        } else {
            $data['branches'] = Branch::where('id', auth()->user()->branch_id)->get();
        }   
        return view('correct_sales.index',$data);
    }


    public function fetchAllStocks(Request $request)
    {
        $stocks = Stock::where('branch_id', $request->branch_id)->get();
        return response()->json(['stocks' => $stocks]);
    }


    public function fetchSales(Request $request)
    {
        $sales = Sale::with('product')
        ->where('stock_id', $request->stock_id)
        ->whereDate('created_at', '>', Carbon::parse('2024-01-05'))
        ->get();       
         return response()->json(['sales' => $sales]);
    }


    public function updateBuyingPrice(Request $request)
    {
        $sale = Sale::find($request->id);
        if ($sale) {
            $sale->buying_price = $request->buying_price;
            $sale->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Sale not found']);
        }
    }



    
}
