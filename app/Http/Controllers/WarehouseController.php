<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Models\WarehouseTransaction;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $warehouseItems = WarehouseItem::with('warehouse', 'stock')
            ->paginate(10);
    
        if ($request->ajax()) {
            return view('warehouse.partials.items_table', compact('warehouseItems'))->render();
        }
    
        return view('warehouse.index', compact('warehouseItems'));
    }
    

    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array',
            'items.*.stock_id' => 'required|exists:stocks,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        DB::beginTransaction();
    
        try {
            $batchNumber = 'TW-' . Str::upper(Str::random(10)); // TW for Transfer to Warehouse
    
            foreach ($request->items as $item) {
                $stock = Stock::findOrFail($item['stock_id']);
    
                if ($stock->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient quantity for {$stock->name}");
                }
    
                $stock->quantity -= $item['quantity'];
                $stock->save();
    
                $warehouseItem = WarehouseItem::firstOrNew([
                    'warehouse_id' => $request->warehouse_id,
                    'stock_id' => $item['stock_id'],
                ]);
    
                $warehouseItem->quantity += $item['quantity'];
                $warehouseItem->save();
    
                WarehouseTransaction::create([
                    'warehouse_id' => $request->warehouse_id,
                    'stock_id' => $item['stock_id'],
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'source' => 'transfer',
                    'branch_id' => $stock->branch_id,
                    'batch_number' => $batchNumber,
                ]);
            }
    
            DB::commit();
            return response()->json(['message' => 'Transfer completed successfully', 'batch_number' => $batchNumber]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function transferToStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warehouse_item_id' => 'required|exists:warehouse_items,id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        DB::beginTransaction();
    
        try {
            $warehouseItem = WarehouseItem::findOrFail($request->warehouse_item_id);
            $stock = $warehouseItem->stock;
    
            if ($warehouseItem->quantity < $request->quantity) {
                throw new \Exception("Insufficient quantity in warehouse");
            }
    
            $batchNumber = 'TS-' . Str::upper(Str::random(10)); // TS for Transfer to Store
    
            $warehouseItem->quantity -= $request->quantity;
            $warehouseItem->save();
    
            $stock->quantity += $request->quantity;
            $stock->save();
    
            WarehouseTransaction::create([
                'warehouse_id' => $warehouseItem->warehouse_id,
                'stock_id' => $stock->id,
                'type' => 'out',
                'quantity' => $request->quantity,
                'source' => 'transfer_to_store',
                'branch_id' => $stock->branch_id,
                'batch_number' => $batchNumber,
            ]);
    
            DB::commit();
            return response()->json([
                'message' => 'Transfer completed successfully',
                'new_quantity' => $warehouseItem->quantity,
                'batch_number' => $batchNumber
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function transactions(Request $request)
    {
        $query = WarehouseTransaction::with(['warehouse', 'stock', 'branch'])
            ->select('batch_number', DB::raw('SUM(quantity) as total_quantity'), 'type', 'source', 'created_at')
            ->groupBy('batch_number', 'type', 'source', 'created_at');
    
        if ($request->filled('warehouse')) {
            $query->where('warehouse_id', $request->warehouse);
        }
    
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
    
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
    
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
    
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
    
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
    
        $transactions = $query->paginate(10);
    
        if ($request->ajax()) {
            return view('warehouse.partials.transactions_table', compact('transactions'))->render();
        }
    
        $warehouses = Warehouse::all();
        $sources = WarehouseTransaction::distinct('source')->pluck('source');
    
        return view('warehouse.transactions', compact('transactions', 'warehouses', 'sources'));
    }
    
    public function transactionDetails($batchNumber)
    {
        $transactions = WarehouseTransaction::with(['warehouse', 'stock', 'branch'])
            ->where('batch_number', $batchNumber)
            ->get();
    
        return response()->json($transactions);
    }

    public function transferForm()
    {
        $branches = Branch::all(); // Assuming you have a Branch model
        $warehouses = Warehouse::all();
        return view('warehouse.transfer', compact('branches', 'warehouses'));
    }
    
    public function searchStocks(Request $request)
    {
        $query = $request->get('query');
        $branch_id = $request->get('branch_id');
    
        $stocks = Stock::where('branch_id', $branch_id)
            ->where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'quantity']);
    
        return response()->json($stocks);
    }
    


    public function moveIn(Request $request)
    {
        // Implement move-in logic here
        // Create a new WarehouseTransaction
        // Update WarehouseItem
        // Update Stock quantity if necessary

        return redirect()->back()->with('success', 'Items moved in successfully');
    }

    public function moveOut(Request $request)
    {
        // Implement move-out logic here
        // Create a new WarehouseTransaction
        // Update WarehouseItem
        // Update Stock quantity

        return redirect()->back()->with('success', 'Items moved out successfully');
    }
}