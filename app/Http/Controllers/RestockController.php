<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Restock;
use App\Models\RestockItem;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use App\Models\WarehouseTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class RestockController extends Controller
{

    public function index(Request $request)
    {
        $restocks = Restock::with(['supplier', 'items', 'branchRestocks.branch'])
            ->when($request->branch_id, function ($query, $branch_id) {
                return $query->whereHas('branchRestocks', function ($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                });
            })
            ->when($request->type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy($request->sort_by ?? 'restocks.created_at', $request->sort_order ?? 'desc')
            ->paginate(15);

        $branches = Branch::all();
        $recentRestocks = Restock::with('branchRestocks.branch')->latest()->take(5)->get();

        $branchRestockValues = Restock::where('restocks.created_at', '>=', now()->subDays(30))
            ->join('branch_restocks', 'restocks.id', '=', 'branch_restocks.restock_id')
            ->join('branches', 'branch_restocks.branch_id', '=', 'branches.id')
            ->selectRaw('branches.id, branches.name, SUM(restocks.total_cost) as total_value')
            ->groupBy('branches.id', 'branches.name')
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'table' => view('restock.partials.restock_table', compact('restocks'))->render(),
                'recentRestocks' => view('restock.partials.recent_restocks', compact('recentRestocks'))->render(),
                'branchRestockValues' => view('restock.partials.branch_restock_values', compact('branchRestockValues'))->render(),
            ]);
        }

        return view('restock.index', compact('restocks', 'branches', 'recentRestocks', 'branchRestockValues'));
    }

    public function createDirect()
    {
        $branches = Branch::all();
        return view('restock.create_direct', compact('branches'));
    }

    public function searchStocks(Request $request)
    {
        $term = $request->input('term');
        $branchId = $request->input('branch_id');

        $stocks = Stock::where('branch_id', $branchId)
            ->where('name', 'LIKE', "%{$term}%")
            ->get();

        return response()->json($stocks);
    }

    public function storeDirect(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'stock_id' => 'required|array',
            'stock_id.*' => 'exists:stocks,id',
            'restock_quantity' => 'required|array',
            'restock_quantity.*' => 'required|integer|min:1',
            'new_buying_price' => 'required|array',
            'new_buying_price.*' => 'required|numeric|min:0',
            'new_selling_price' => 'required|array',
            'new_selling_price.*' => 'required|numeric|min:0',
        ]);
    
        DB::beginTransaction();
        try {
            // Generate restock number
            $prefix = 'DR';
            $date = now()->format('ymd');
            $sequence = str_pad(Restock::whereDate('created_at', now()->toDateString())->count() + 1, 3, '0', STR_PAD_LEFT);
            $restockNumber = $prefix . '-' . $date . '-' . $sequence;
    
            // Create restock entry
            $restock = Restock::create([
                'restock_number' => $restockNumber,
                'type' => 'direct',
                'status' => 'completed',
                'total_cost' => 0,
            ]);
    
            $totalCost = 0;
    
            // Process each stock item
            foreach ($validatedData['stock_id'] as $index => $stockId) {
                $stock = Stock::findOrFail($stockId);
                $quantity = $validatedData['restock_quantity'][$index];
                $newBuyingPrice = $validatedData['new_buying_price'][$index];
                $newSellingPrice = $validatedData['new_selling_price'][$index];
    
                // Create restock item
                RestockItem::create([
                    'restock_id' => $restock->id,
                    'stock_id' => $stockId,
                    'ordered_quantity' => $quantity,
                    'received_quantity' => $quantity,
                    'old_buying_price' => $stock->buying_price,
                    'new_buying_price' => $newBuyingPrice,
                    'old_selling_price' => $stock->selling_price,
                    'new_selling_price' => $newSellingPrice,
                    'price_changed' => ($newBuyingPrice != $stock->buying_price || $newSellingPrice != $stock->selling_price),
                    'old_quantity' => $stock->quantity,
                ]);
    
                // Update stock
                $stock->update([
                    'quantity' => $stock->quantity + $quantity,
                    'buying_price' => $newBuyingPrice,
                    'selling_price' => $newSellingPrice,
                ]);
    
                $totalCost += $quantity * $newBuyingPrice;
            }
    
            $restock->update(['total_cost' => $totalCost]);
            
            DB::commit();
            return redirect()->route('restock.index')->with('success', 'Direct restock created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while creating the restock. ' . $e->getMessage());
        }
    }


    ////




    public function storePlanned(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'supplier_id' => 'nullable|exists:users,id',
            'stocks' => 'required|array',
            'stocks.*.id' => 'required|exists:stocks,id',
            'stocks.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $prefix = 'PR'; // For Planned Restock
            $date = now()->format('ymd');
            $sequence = str_pad(Restock::whereDate('created_at', now()->toDateString())->count() + 1, 3, '0', STR_PAD_LEFT);
            $restockNumber = $prefix . '-' . $date . '-' . $sequence;

            $restock = Restock::create([
                'restock_number' => $restockNumber,
                'type' => 'planned',
                'supplier_id' => $request->supplier_id,
                'status' => 'pending',
                'total_cost' => 0,
            ]);

            $totalCost = 0;

            foreach ($request->stocks as $stockData) {
                $stock = Stock::findOrFail($stockData['id']);
                $quantity = $stockData['quantity'];

                RestockItem::create([
                    'restock_id' => $restock->id,
                    'stock_id' => $stock->id,
                    'ordered_quantity' => $quantity,
                    'old_buying_price' => $stock->buying_price,
                    'new_buying_price' => $stock->buying_price,
                    'old_selling_price' => $stock->selling_price,
                    'new_selling_price' => $stock->selling_price,
                    'old_quantity' => $stock->quantity,
                ]);

                $totalCost += $quantity * $stock->buying_price;
            }

            $restock->update(['total_cost' => $totalCost]);

            DB::commit();

            return redirect()->route('restock.index')->with('success', 'Planned restock created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while creating the restock. ' . $e->getMessage());
        }
    }






    public function createPlanned()
    {
        $branches = Branch::all();
        $firstBranch = $branches->first();
        $stocks = $this->getStocks($firstBranch->id);
        $suppliers = User::where('usertype', 'supplier')->get();
        
        return view('restock.create_planned', compact('branches', 'stocks', 'suppliers'));
    }

    public function fetchStocks(Request $request)
    {
        $stocks = $this->getStocks(
            $request->branch_id,
            $request->stock_level,
            $request->search
        );
    
        return response()->json([
            'stocks' => view('restock.partials.stocks_table', compact('stocks'))->render()
        ]);
    }
    
    private function getStocks($branchId, $stockLevel = 'all', $search = null)
    {
        $query = Stock::where('branch_id', $branchId);
    
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
    
        switch ($stockLevel) {
            case 'very_high':
                $query->whereRaw('quantity > critical_level * 2');
                break;
            case 'high':
                $query->whereRaw('quantity > critical_level * 1.5 AND quantity <= critical_level * 2');
                break;
            case 'medium':
                $query->whereRaw('quantity > critical_level AND quantity <= critical_level * 1.5');
                break;
            case 'low':
                $query->whereRaw('quantity > critical_level * 0.5 AND quantity <= critical_level');
                break;
            case 'very_low':
                $query->whereRaw('quantity > 0 AND quantity <= critical_level * 0.5');
                break;
            case 'out_of_stock':
                $query->where('quantity', 0);
                break;
        }
    
        return $query->paginate(10)->withQueryString();
    }




    // public function showCompleteForm(Restock $restock)
    // {
    //     // Check if the restock type is not 'planned'
    //     if ($restock->type !== 'planned') {
    //         return redirect()->back()->with('error', 'Only planned restocks can be completed.');
    //     }
    
    //     // Check if the restock is already completed
    //     if ($restock->status === 'completed') {
    //         return redirect()->back()->with('error', 'This restock has already been completed.');
    //     }
    
    //     // If checks pass, proceed with loading the form
    //     $restock->load('items.stock'); // Eager load the relationships
    //     $branches = Branch::all();
    //     return view('restock.complete_restock', compact('restock', 'branches'));
    // }

    // public function completeRestock(Request $request, Restock $restock)
    // {
    //     $validatedData = $request->validate([
    //         'storage_location' => 'required|in:shop,warehouse',
    //         'branch_id' => 'required|exists:branches,id',
    //         'received_quantity' => 'required|array',
    //         'new_buying_price' => 'required|array',
    //         'new_selling_price' => 'required|array',
    //         'out_of_stock' => 'array',
    //     ]);

    //     DB::transaction(function () use ($restock, $validatedData) {
    //         foreach ($restock->items as $item) {
    //             $receivedQuantity = $validatedData['received_quantity'][$item->id] ?? 0;
    //             $newBuyingPrice = $validatedData['new_buying_price'][$item->id];
    //             $newSellingPrice = $validatedData['new_selling_price'][$item->id];
    //             $outOfStock = isset($validatedData['out_of_stock'][$item->id]);

    //             $item->update([
    //                 'received_quantity' => $receivedQuantity,
    //                 'new_buying_price' => $newBuyingPrice,
    //                 'new_selling_price' => $newSellingPrice,
    //                 'price_changed' => ($newBuyingPrice != $item->old_buying_price || $newSellingPrice != $item->old_selling_price),
    //             ]);

    //             if ($validatedData['storage_location'] === 'shop') {
    //                 $this->updateShopStock($item->stock, $receivedQuantity, $newBuyingPrice, $newSellingPrice, $validatedData['branch_id']);
    //             } else {
    //                 $this->updateWarehouseStock($item->stock, $receivedQuantity, $newBuyingPrice, $newSellingPrice);
    //             }
    //         }

    //         $restock->update([
    //             'status' => 'completed',
    //             'total_cost' => $restock->items->sum(function ($item) {
    //                 return $item->received_quantity * $item->new_buying_price;
    //             }),
    //         ]);
    //     });

    //     return redirect()->route('restock.index')->with('success', 'Restock order completed successfully.');
    // }

    private function updateShopStock($stock, $quantity, $buyingPrice, $sellingPrice, $branchId)
    {
        $stock->update([
            'branch_id' => $branchId,
            'quantity' => $stock->quantity + $quantity,
            'buying_price' => $buyingPrice,
            'selling_price' => $sellingPrice,
        ]);
    }

   




    // Updated showCompleteForm method
    public function showCompleteForm(Restock $restock)
    {
        // Check if the restock type is not 'planned'
        if ($restock->type !== 'planned') {
            return redirect()->back()->with('error', 'Only planned restocks can be completed.');
        }

        // Check if the restock is already completed
        if ($restock->status === 'completed') {
            return redirect()->back()->with('error', 'This restock has already been completed.');
        }

        // If checks pass, proceed with loading the form
        $restock->load('items.stock'); // Eager load the relationships
        $branches = Branch::all();
        $warehouses = Warehouse::all(); // Add warehouses
        
        return view('restock.complete_restock', compact('restock', 'branches', 'warehouses'));
    }

    // Updated completeRestock method with proper validation
    public function completeRestock(Request $request, Restock $restock)
    {
        $validatedData = $request->validate([
            'storage_location' => 'required|in:shop,warehouse',

            'branch_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->storage_location === 'shop') {
                        if (!$value || !\App\Models\Branch::where('id', $value)->exists()) {
                            $fail('The selected branch is invalid.');
                        }
                    }
                },
            ],

            'warehouse_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->storage_location === 'warehouse') {
                        if (!$value || !\App\Models\Warehouse::where('id', $value)->exists()) {
                            $fail('The selected warehouse is invalid.');
                        }
                    }
                },
            ],

            'received_quantity' => 'required|array',
            'new_buying_price' => 'required|array',
            'new_selling_price' => 'required|array',
            'not_supplied' => 'nullable|array',
        ]);


        DB::transaction(function () use ($restock, $validatedData) {
            foreach ($restock->items as $item) {
                $receivedQuantity = $validatedData['received_quantity'][$item->id] ?? 0;
                $newBuyingPrice = $validatedData['new_buying_price'][$item->id];
                $newSellingPrice = $validatedData['new_selling_price'][$item->id];
                $notSupplied = isset($validatedData['not_supplied'][$item->id]);

                // If item is not supplied, set received quantity to 0
                if ($notSupplied) {
                    $receivedQuantity = 0;
                }

                $item->update([
                    'received_quantity' => $receivedQuantity,
                    'new_buying_price' => $newBuyingPrice,
                    'new_selling_price' => $newSellingPrice,
                    'price_changed' => ($newBuyingPrice != $item->old_buying_price || $newSellingPrice != $item->old_selling_price),
                ]);

                // Only update stock if quantity was received
                if ($receivedQuantity > 0) {
                    if ($validatedData['storage_location'] === 'shop') {
                        $this->updateShopStock($item->stock, $receivedQuantity, $newBuyingPrice, $newSellingPrice, $validatedData['branch_id']);
                    } else {
                        $this->updateWarehouseStock($item->stock, $receivedQuantity, $newBuyingPrice, $newSellingPrice, $validatedData['warehouse_id']);
                    }
                }
            }

            $restock->update([
                'status' => 'completed',
                'total_cost' => $restock->items->sum(function ($item) {
                    return $item->received_quantity * $item->new_buying_price;
                }),
            ]);
        });

        return redirect()->route('restock.index')->with('success', 'Restock order completed successfully.');
    }

    // Updated updateWarehouseStock method
    private function updateWarehouseStock($stock, $quantity, $buyingPrice, $sellingPrice, $warehouseId)
    {
        // Update the stock's pricing information
        $stock->update([
            'buying_price' => $buyingPrice,
            'selling_price' => $sellingPrice,
        ]);

        // Check if the item already exists in the warehouse
        $warehouseItem = WarehouseItem::where('warehouse_id', $warehouseId)
                                    ->where('stock_id', $stock->id)
                                    ->first();

        if ($warehouseItem) {
            // Update existing warehouse item
            $warehouseItem->increment('quantity', $quantity);
        } else {
            // Create new warehouse item
            WarehouseItem::create([
                'warehouse_id' => $warehouseId,
                'stock_id' => $stock->id,
                'quantity' => $quantity,
            ]);
        }

        // Record the warehouse transaction
        WarehouseTransaction::create([
            'warehouse_id' => $warehouseId,
            'stock_id' => $stock->id,
            'type' => 'in',
            'quantity' => $quantity,
            'source' => 'purchase',
            'batch_number' => null, // You can add batch tracking if needed
            'branch_id' => null,
        ]);
    }









    public function getDetails(Restock $restock)
    {
        $restockItems = $restock->items()->with('stock')->get();
        $expenses = $restock->expenses;
        $damages = $restock->damages()->with('stock')->get();

        return view('restock.partials.details', compact('restock', 'restockItems', 'expenses', 'damages'));
    }

    public function getExpenses(Restock $restock)
    {
        $expenses = $restock->expenses;
        $expenseCategories = ['loading', 'unloading', 'handling', 'packaging', 'labelling', 'transportation', 'other'];

        return view('restock.partials.expenses', compact('restock', 'expenses', 'expenseCategories'));
    }

    public function storeExpenses(Request $request, Restock $restock)
    {
        $validated = $request->validate([
            'expense_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $restock->expenses()->create($validated);

        return redirect()->back()->with('success', 'Expense added successfully.');
    }

    public function getDamages(Restock $restock)
    {
        $restockItems = $restock->items()->with('stock')->get();
        $damages = $restock->damages()->with('stock')->get();

        return view('restock.partials.damages', compact('restock', 'restockItems', 'damages'));
    }

    public function storeDamages(Request $request, Restock $restock)
    {
        $validated = $request->validate([
            'restock_item_id' => 'required|exists:restock_items,id',
            'quantity' => 'required|integer|min:1',
            'damage_level' => 'required|in:minor,moderate,severe',
            'notes' => 'nullable|string',
        ]);
    
        $restockItem = $restock->items()->findOrFail($validated['restock_item_id']);
    
        // Check if the damaged quantity exceeds the ordered quantity
        $existingDamageQuantity = $restock->damages()
            ->where('stock_id', $restockItem->stock_id)
            ->sum('quantity');
    
        $totalDamageQuantity = $existingDamageQuantity + $validated['quantity'];
    
        if ($totalDamageQuantity > $restockItem->ordered_quantity) {
            return redirect()->back()->with('error', 'Total damaged quantity cannot exceed the ordered quantity.');
        }
    
        $restock->damages()->create([
            'stock_id' => $restockItem->stock_id,
            'quantity' => $validated['quantity'],
            'damage_level' => $validated['damage_level'],
            'notes' => $validated['notes'],
        ]);
    
        return redirect()->back()->with('success', 'Damage recorded successfully.');
    }

    public function downloadPdf(Restock $restock)
    {
        $restockItems = $restock->items()->with('stock')->get();
        $expenses = $restock->expenses;
        $damages = $restock->damages()->with('stock')->get();

        $pdf = PDF::loadView('restock.pdf', compact('restock', 'restockItems', 'expenses', 'damages'));
        $pdf->setPaper('roll', 'portrait');

        return $pdf->download('restock_' . $restock->restock_number . '.pdf');
    }




    public function editPlanned(Restock $restock)
    {
        $suppliers = User::where('usertype', 'supplier')->get();
        $restockItems = $restock->items()->with('stock')->get();
    
        return view('restock.edit_planned', compact('restock', 'suppliers', 'restockItems'));
    }

    public function updatePlanned(Request $request, Restock $restock)
    {
        // Validate base request
        $request->validate([
            'supplier_id' => 'nullable|exists:users,id',
            'stocks' => 'required|array|min:1',
            'stocks.*.id' => 'required|exists:stocks,id',
            'stocks.*.quantity' => 'required|integer|min:1',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Update supplier if changed
            $restock->update([
                'supplier_id' => $request->supplier_id,
            ]);
    
            // Track current restock items
            $currentItemIds = $restock->items->pluck('stock_id')->toArray();
            $incomingStockIds = array_column($request->stocks, 'id');
    
            // Identify items to delete (in current items but not in incoming)
            $itemsToDelete = array_diff($currentItemIds, $incomingStockIds);
            
            // Remove items not in new selection
            if (!empty($itemsToDelete)) {
                RestockItem::where('restock_id', $restock->id)
                    ->whereIn('stock_id', $itemsToDelete)
                    ->delete();
            }
    
            $totalCost = 0;
    
            // Process incoming stocks
            foreach ($request->stocks as $stockData) {
                $stock = Stock::findOrFail($stockData['id']);
                $quantity = $stockData['quantity'];
    
                // Update or create restock item
                $restockItem = RestockItem::updateOrCreate(
                    [
                        'restock_id' => $restock->id,
                        'stock_id' => $stock->id
                    ],
                    [
                        'ordered_quantity' => $quantity,
                        'old_buying_price' => $stock->buying_price,
                        'new_buying_price' => $stock->buying_price,
                        'old_selling_price' => $stock->selling_price,
                        'new_selling_price' => $stock->selling_price,
                    ]
                );
    
                $totalCost += $quantity * $stock->buying_price;
            }
    
            // Update total cost
            $restock->update(['total_cost' => $totalCost]);
    
            DB::commit();
    
            return redirect()->route('restock.index')
                ->with('success', 'Planned restock updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Error updating restock: ' . $e->getMessage())
                ->withInput();
        }
    }



}
