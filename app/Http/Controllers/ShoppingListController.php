<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ShoppingList;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    function index(){
        $data['stocks'] = ShoppingList::where('branch_id',0)->paginate(25);
        $data['branches'] = Branch::all();
        $data['suppliers'] = User::where('usertype', 'supplier')->get();
        return view('shopping_list.index',$data);
    }

    function store(Request $request){


        $productCount = count($request->name);
        if($productCount != NULL){
            for ($i=0; $i < $productCount; $i++){
                $data = new ShoppingList();
                $data->branch_id = $request->branch_id;
                $data->name = $request->name[$i];
                $data->supplier_id = $request->supplier_id[$i];
                $data->save();
            }
        }
        Toastr::success('Shopping Lists has been added sucessfully', 'Done');
        return redirect()->route('shopping_list.index');

    }

    function edit($id){
        $data['stock'] = ShoppingList::find($id);
        return view('stock.edit',$data);
    }

   
    function update(Request $request, $id){
       
        $data = ShoppingList::find($id);
        $data->name = $request->name;
        $data->buying_price = $request->buying_price;
        $data->selling_price = $request->selling_price;
        $data->quantity = $request->quantity;
        $data->critical_level = $request->critical_level;

        $data->update();
        Toastr::success('Inventory has been updated sucessfully', 'Done');
        return redirect()->route('stock.index');
    }

   

    function delete(Request $request){
       
        $data = ShoppingList::find($request->id);
        
        $data->delete();
       
        return response()->json([
            'status' => 200,
            'message' => 'Product Deleted Succesffully',
        ]);
    }

    public function fetchList(Request $request)
    {
        $data['stocks'] = ShoppingList::where('branch_id', $request->branch_id)->paginate(25);
        return view('shopping_list.table', $data)->render();
      
    }


    public function paginate(Request $request)
    {
        $data['stocks'] = ShoppingList::where('branch_id', $request->branch_id)->paginate(25);
        return view('shopping_list.table', $data)->render();
    }


    public function Search(Request $request)
    {

        
        $data['stocks'] = ShoppingList::where('branch_id', $request->branch_id)->where('name', 'like','%'.$request['query'].'%')->paginate(25);

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
