<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Sale;
use App\Models\Stock;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Exports\StocksExport;
use App\Models\Restock;
use App\Models\RestockItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{
    public function index()
    {
        $data['stocks'] = Stock::where('branch_id', 0)->paginate(25);
        if (in_array(auth()->user()->id, [4, 443])) {
            $data['branches'] = Branch::all();
        } else {
            $data['branches'] = Branch::where('id', auth()->user()->branch_id)->get();
        }
        return view('stock.index', $data);
    }

    public function store(Request $request)
    {

        $productCount = count($request->name);
        if ($productCount != null) {
            for ($i = 0; $i < $productCount; $i++) {
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

    public function edit($id)
    {
        $data['stock'] = Stock::find($id);
        return view('stock.edit', $data);
    }

    public function copyIndex($id)
    {
        $data['stock'] = Stock::find($id);
        if (in_array(auth()->user()->id, [4, 443])) {
            $data['branches'] = Branch::all();
        } else {
            $data['branches'] = Branch::where('id', auth()->user()->branch_id)->get();
        }
        return view('stock.copy', $data);
    }

    public function update(Request $request, $id)
    {

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

    public function copyStore(Request $request)
    {

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

    public function delete(Request $request)
    {

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

        $data['stocks'] = Stock::where('branch_id', $request->branch_id)->where('name', 'like', '%' . $request['query'] . '%')->paginate(25);

        if ($data['stocks']->count() > 0) {
            return view('stock.table', $data)->render();
        } else {
            return response()->json([
                'status' => 404,
            ]);
        }
    }

    public function correctIndex()
    {
        if (in_array(auth()->user()->id, [4, 443])) {
            $data['branches'] = Branch::all();
        } else {
            $data['branches'] = Branch::where('id', auth()->user()->branch_id)->get();
        }
        return view('correct_sales.index', $data);
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



    public function export(Request $request)
    {
        $branchId = $request->input('branch_id');
        $columns = $request->input('columns');

        return Excel::download(new StocksExport($branchId, $columns), 'stocks.xlsx');
    }

    public function exportView()
    {
        $branches = \App\Models\Branch::select('id', 'name')->get();
        return view('stock.export', compact('branches'));
    }




    public function updatePrices(Request $request, Stock $stock)
{
    $request->validate([
        'old_buying_price' => 'required|numeric',
        'old_selling_price' => 'required|numeric',
        'new_buying_price' => 'required|numeric',
        'new_selling_price' => 'required|numeric',
    ]);

    DB::beginTransaction();
    try {
        // Create a new direct restock
        $restock = Restock::create([
            'restock_number' => 'PR' . time(), // PR for Price Review
            'type' => 'direct',
            'status' => 'completed',
            'total_cost' => 0,
        ]);

        // Create restock item for price change record
        RestockItem::create([
            'restock_id' => $restock->id,
            'stock_id' => $stock->id,
            'ordered_quantity' => 0,
            'received_quantity' => 0,
            'old_quantity' => $stock->quantity,
            'old_buying_price' => $request->old_buying_price,
            'new_buying_price' => $request->new_buying_price,
            'old_selling_price' => $request->old_selling_price,
            'new_selling_price' => $request->new_selling_price,
            'price_changed' => true,
        ]);

        // Update stock prices
        $stock->update([
            'buying_price' => $request->new_buying_price,
            'selling_price' => $request->new_selling_price,
        ]);

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Prices updated successfully']);
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['success' => false, 'message' => 'Error updating prices'], 500);
    }
}
    

}
