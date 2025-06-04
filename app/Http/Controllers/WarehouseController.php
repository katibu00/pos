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
use Carbon\Carbon;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $warehouseId = $request->get('warehouse_id', Warehouse::first()->id ?? null);
        $warehouse = Warehouse::find($warehouseId);
        
        if (!$warehouse) {
            return redirect()->back()->with('error', 'No warehouse found');
        }

        // Get warehouse stats
        $stats = $this->getWarehouseStats($warehouseId);
        
        // Get warehouse items with pagination
        $query = WarehouseItem::with(['stock.branch', 'warehouse'])
            ->where('warehouse_id', $warehouseId)
            ->where('quantity', '>', 0);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('stock', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $warehouseItems = $query->orderBy('updated_at', 'desc')->paginate(15);
        
        // Add last transactions data
        foreach ($warehouseItems as $item) {
            $item->last_move_in = WarehouseTransaction::where('warehouse_id', $warehouseId)
                ->where('stock_id', $item->stock_id)
                ->where('type', 'in')
                ->latest()
                ->first();
                
            $item->last_move_out = WarehouseTransaction::where('warehouse_id', $warehouseId)
                ->where('stock_id', $item->stock_id)
                ->where('type', 'out')
                ->latest()
                ->first();
        }

        $warehouses = Warehouse::all();

        if ($request->ajax()) {
            return view('warehouse.partials.items_table', compact('warehouseItems'))->render();
        }

        return view('warehouse.index', compact('warehouseItems', 'warehouses', 'warehouse', 'stats'));
    }

    private function getWarehouseStats($warehouseId)
    {
        $totalItems = WarehouseItem::where('warehouse_id', $warehouseId)->sum('quantity');
        
        $totalValue = WarehouseItem::join('stocks', 'warehouse_items.stock_id', '=', 'stocks.id')
            ->where('warehouse_id', $warehouseId)
            ->sum(DB::raw('warehouse_items.quantity * stocks.buying_price'));
            
        $uniqueProducts = WarehouseItem::where('warehouse_id', $warehouseId)
            ->where('quantity', '>', 0)
            ->count();
            
        $lastMoveIn = WarehouseTransaction::where('warehouse_id', $warehouseId)
            ->where('type', 'in')
            ->latest()
            ->first();
            
        $lastMoveOut = WarehouseTransaction::where('warehouse_id', $warehouseId)
            ->where('type', 'out')
            ->latest()
            ->first();
            
        $todayMovements = WarehouseTransaction::where('warehouse_id', $warehouseId)
            ->whereDate('created_at', Carbon::today())
            ->count();

        return [
            'total_items' => $totalItems,
            'total_value' => $totalValue,
            'unique_products' => $uniqueProducts,
            'last_move_in' => $lastMoveIn,
            'last_move_out' => $lastMoveOut,
            'today_movements' => $todayMovements
        ];
    }

    public function transferForm()
    {
        $branches = Branch::all(); 
        $warehouses = Warehouse::all();
        return view('warehouse.transfer', compact('branches', 'warehouses'));
    }

    public function searchItems(Request $request)
    {
        $query = $request->get('query');
        $source = $request->get('source'); // 'store' or 'warehouse'
        $sourceId = $request->get('source_id'); // branch_id or warehouse_id
        $page = $request->get('page', 1);
        $perPage = 10;

        if ($source === 'store') {
            $items = Stock::where('branch_id', $sourceId)
                ->where('quantity', '>', 0)
                ->when($query, function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            $items = WarehouseItem::with('stock')
                ->where('warehouse_id', $sourceId)
                ->where('quantity', '>', 0)
                ->when($query, function($q) use ($query) {
                    $q->whereHas('stock', function($stock) use ($query) {
                        $stock->where('name', 'like', "%{$query}%");
                    });
                })
                ->paginate($perPage, ['*'], 'page', $page);
        }

        if ($request->ajax()) {
            return response()->json([
                'items' => $items->items(),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total()
                ]
            ]);
        }

        return response()->json($items);
    }

    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_type' => 'required|in:store,warehouse',
            'source_id' => 'required|integer',
            'destination_type' => 'required|in:store,warehouse',
            'destination_id' => 'required|integer',
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $batchNumber = 'T-' . strtoupper(Str::random(8)) . '-' . time();
            
            foreach ($request->items as $item) {
                $this->processTransferItem(
                    $item,
                    $request->source_type,
                    $request->source_id,
                    $request->destination_type,
                    $request->destination_id,
                    $batchNumber
                );
            }

            DB::commit();
            return response()->json([
                'message' => 'Transfer completed successfully',
                'batch_number' => $batchNumber
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function processTransferItem($item, $sourceType, $sourceId, $destType, $destId, $batchNumber)
    {
        $quantity = $item['quantity'];
        
        if ($sourceType === 'store') {
            // Transfer from store to warehouse
            $stock = Stock::findOrFail($item['id']);
            
            if ($stock->quantity < $quantity) {
                throw new \Exception("Insufficient quantity for {$stock->name}. Available: {$stock->quantity}");
            }
            
            // Reduce from store
            $stock->quantity -= $quantity;
            $stock->save();
            
            // Add to warehouse
            $warehouseItem = WarehouseItem::firstOrNew([
                'warehouse_id' => $destId,
                'stock_id' => $stock->id,
            ]);
            $warehouseItem->quantity += $quantity;
            $warehouseItem->save();
            
            // Record transaction
            WarehouseTransaction::create([
                'warehouse_id' => $destId,
                'stock_id' => $stock->id,
                'type' => 'in',
                'quantity' => $quantity,
                'source' => 'transfer',
                'branch_id' => $stock->branch_id,
                'batch_number' => $batchNumber,
            ]);
            
        } else {
            // Transfer from warehouse to store
            $warehouseItem = WarehouseItem::with('stock')->findOrFail($item['id']);
            
            if ($warehouseItem->quantity < $quantity) {
                throw new \Exception("Insufficient quantity for {$warehouseItem->stock->name}. Available: {$warehouseItem->quantity}");
            }
            
            // Reduce from warehouse
            $warehouseItem->quantity -= $quantity;
            $warehouseItem->save();
            
            // Add to store
            $warehouseItem->stock->quantity += $quantity;
            $warehouseItem->stock->save();
            
            // Record transaction
            WarehouseTransaction::create([
                'warehouse_id' => $warehouseItem->warehouse_id,
                'stock_id' => $warehouseItem->stock_id,
                'type' => 'out',
                'quantity' => $quantity,
                'source' => 'transfer',
                'branch_id' => $warehouseItem->stock->branch_id,
                'batch_number' => $batchNumber,
            ]);
        }
    }

    // Keep existing methods for backward compatibility
    public function transferToStore(Request $request)
    {
        // This method can now redirect to the new transfer method
        return $this->transfer($request);
    }

    public function searchStocks(Request $request)
    {
        // Redirect to new searchItems method
        return $this->searchItems($request);
    }

    /////////



    // Add these methods to your WarehouseController

public function transactions(Request $request)
{
    $query = WarehouseTransaction::with(['warehouse', 'stock.branch', 'stock'])
        ->select('warehouse_transactions.*');

    // Apply filters
    if ($request->filled('warehouse_id')) {
        $query->where('warehouse_id', $request->warehouse_id);
    }

    if ($request->filled('branch_id')) {
        $query->where('branch_id', $request->branch_id);
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

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('batch_number', 'like', "%{$search}%")
              ->orWhereHas('stock', function($stockQuery) use ($search) {
                  $stockQuery->where('name', 'like', "%{$search}%");
              })
              ->orWhereHas('warehouse', function($warehouseQuery) use ($search) {
                  $warehouseQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    // Sorting
    $sortField = $request->input('sort', 'created_at');
    $sortDirection = $request->input('direction', 'desc');
    
    // Validate sort fields
    $allowedSortFields = ['created_at', 'type', 'quantity', 'batch_number'];
    if (!in_array($sortField, $allowedSortFields)) {
        $sortField = 'created_at';
    }

    $query->orderBy($sortField, $sortDirection);

    $transactions = $query->paginate(20);

    // Get filter options
    $warehouses = Warehouse::orderBy('name')->get();
    $branches = Branch::orderBy('name')->get();
    $sources = WarehouseTransaction::distinct('source')->whereNotNull('source')->pluck('source');
    
    // Get summary statistics
    $stats = $this->getTransactionStats($request);

    if ($request->ajax()) {
        return response()->json([
            'html' => view('warehouse.partials.transactions_table', compact('transactions'))->render(),
            'pagination' => $transactions->links()->render(),
            'stats' => $stats
        ]);
    }

    return view('warehouse.transactions', compact(
        'transactions', 
        'warehouses', 
        'branches', 
        'sources', 
        'stats'
    ));
}

public function transactionDetails($batchNumber)
{
    $transactions = WarehouseTransaction::with(['warehouse', 'stock.branch', 'stock'])
        ->where('batch_number', $batchNumber)
        ->orderBy('created_at', 'desc')
        ->get();

    if ($transactions->isEmpty()) {
        return response()->json(['error' => 'Batch not found'], 404);
    }

    // Calculate batch summary
    $summary = [
        'batch_number' => $batchNumber,
        'type' => $transactions->first()->type,
        'source' => $transactions->first()->source,
        'warehouse' => $transactions->first()->warehouse->name,
        'total_items' => $transactions->count(),
        'total_quantity' => $transactions->sum('quantity'),
        'created_at' => $transactions->first()->created_at,
        'branch' => $transactions->first()->stock->branch->name ?? 'N/A'
    ];

    return response()->json([
        'summary' => $summary,
        'transactions' => $transactions
    ]);
}

public function exportTransactions(Request $request)
{
    // You can implement CSV/Excel export here
    $query = WarehouseTransaction::with(['warehouse', 'stock.branch']);
    
    // Apply same filters as transactions method
    if ($request->filled('warehouse_id')) {
        $query->where('warehouse_id', $request->warehouse_id);
    }
    // ... apply other filters

    $transactions = $query->get();
    
    // For now, return JSON (you can implement actual CSV export later)
    return response()->json($transactions);
}

private function getTransactionStats($request)
{
    $baseQuery = WarehouseTransaction::query();
    
    // Apply same filters for consistent stats
    if ($request->filled('warehouse_id')) {
        $baseQuery->where('warehouse_id', $request->warehouse_id);
    }
    if ($request->filled('branch_id')) {
        $baseQuery->where('branch_id', $request->branch_id);
    }
    if ($request->filled('date_from')) {
        $baseQuery->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $baseQuery->whereDate('created_at', '<=', $request->date_to);
    }

    $totalTransactions = $baseQuery->count();
    $totalMoveIn = $baseQuery->where('type', 'in')->sum('quantity');
    $totalMoveOut = $baseQuery->where('type', 'out')->sum('quantity');
    $todayTransactions = $baseQuery->clone()->whereDate('created_at', today())->count();

    // Most active warehouse
    $mostActiveWarehouse = WarehouseTransaction::select('warehouse_id', DB::raw('COUNT(*) as transaction_count'))
        ->with('warehouse')
        ->groupBy('warehouse_id')
        ->orderBy('transaction_count', 'desc')
        ->first();

    // Recent activity (last 7 days)
    $recentActivity = WarehouseTransaction::where('created_at', '>=', now()->subDays(7))
        ->count();

    return [
        'total_transactions' => $totalTransactions,
        'total_move_in' => $totalMoveIn,
        'total_move_out' => $totalMoveOut,
        'today_transactions' => $todayTransactions,
        'most_active_warehouse' => $mostActiveWarehouse?->warehouse?->name ?? 'N/A',
        'recent_activity' => $recentActivity,
        'net_movement' => $totalMoveIn - $totalMoveOut
    ];
}


}