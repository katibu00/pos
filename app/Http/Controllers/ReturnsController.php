<?php

namespace App\Http\Controllers;

use App\Models\Returns;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Http\Request;

class ReturnsController extends Controller
{
    public function index($id)
    {
        $receipt =  Sale::where('id', $id)->first();
        $data['sales'] = Sale::where('receipt_no', $receipt->receipt_no)->get();
        return view('returns.index',$data);
    }

    public function record(Request $request)
    {
        // return $request->all();

        $sale = Sale::find($request->sale_id);

        if($request->returned_qty > $sale->quantity)
        {
            return response()->json([
                'status' => 200,
                'type' => 'error',
                'message' => 'Returned Quantity cannot be greater than sold quantity',
            ]);
        }


        $stock = Stock::find($sale->stock_id);
        $stock->quantity = $stock->quantity + $request->returned_qty;
        $stock->update();

        $return = new Returns();
        $return->branch_id = $sale->branch_id;
        $return->receipt_no = $sale->receipt_no;
        $return->stock_id = $sale->stock_id;
        $return->sold_qty = $sale->quantity;
        $return->returned_qty = $request->returned_qty;
        $return->money_returned = $stock->selling_price * $request->returned_qty;
        $return->save();

        $sale->quantity = $sale->quantity - $request->returned_qty;
        $sale->update();

        return response()->json([
            'status' => 200,
            'type' => 'success',
            'message' => 'Return Recorded Successfully',
        ]);
    }

}
