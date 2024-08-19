<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Restock;
use App\Models\RestockItem;
use App\Models\Stock;
use App\Models\User;
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
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'stock_id' => 'required|array',
            'stock_id.*' => 'exists:stocks,id',
            'restock_quantity' => 'required|array',
            'restock_quantity.*' => 'required|integer|min:1',
            'new_buying_price' => 'nullable|array',
            'new_buying_price.*' => 'nullable|numeric|min:0',
            'new_selling_price' => 'nullable|array',
            'new_selling_price.*' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $prefix = 'DR'; // For Direct Restock
            $date = now()->format('ymd'); // YYMMDD format
            $sequence = str_pad(Restock::whereDate('created_at', now()->toDateString())->count() + 1, 3, '0', STR_PAD_LEFT); // 001, 002, 003, ...
            $restockNumber = $prefix . '-' . $date . '-' . $sequence;

            $restock = Restock::create([
                'restock_number' => $restockNumber,
                'type' => 'direct',
                'status' => 'completed',
                'total_cost' => 0,
            ]);

            $totalCost = 0;

            foreach ($request->stock_id as $index => $stockId) {
                $stock = Stock::findOrFail($stockId); // Ensure the stock record exists
                $quantity = $request->restock_quantity[$index];
                $newBuyingPrice = $request->new_buying_price[$index] ?? $stock->buying_price;
                $newSellingPrice = $request->new_selling_price[$index] ?? $stock->selling_price;

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
                    'old_quantity' => $stock->quantity, // Store the old quantity before the restock
                ]);

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
        return view('restock.complete_restock', compact('restock', 'branches'));
    }

    public function completeRestock(Request $request, Restock $restock)
    {
        $validatedData = $request->validate([
            'storage_location' => 'required|in:shop,warehouse',
            'branch_id' => 'required|exists:branches,id',
            'received_quantity' => 'required|array',
            'new_buying_price' => 'required|array',
            'new_selling_price' => 'required|array',
            'out_of_stock' => 'array',
        ]);

        DB::transaction(function () use ($restock, $validatedData) {
            foreach ($restock->items as $item) {
                $receivedQuantity = $validatedData['received_quantity'][$item->id] ?? 0;
                $newBuyingPrice = $validatedData['new_buying_price'][$item->id];
                $newSellingPrice = $validatedData['new_selling_price'][$item->id];
                $outOfStock = isset($validatedData['out_of_stock'][$item->id]);

                $item->update([
                    'received_quantity' => $receivedQuantity,
                    'new_buying_price' => $newBuyingPrice,
                    'new_selling_price' => $newSellingPrice,
                    'price_changed' => ($newBuyingPrice != $item->old_buying_price || $newSellingPrice != $item->old_selling_price),
                ]);

                if ($validatedData['storage_location'] === 'shop') {
                    $this->updateShopStock($item->stock, $receivedQuantity, $newBuyingPrice, $newSellingPrice, $validatedData['branch_id']);
                } else {
                    $this->updateWarehouseStock($item->stock, $receivedQuantity, $newBuyingPrice, $newSellingPrice);
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

    private function updateShopStock($stock, $quantity, $buyingPrice, $sellingPrice, $branchId)
    {
        $stock->update([
            'branch_id' => $branchId,
            'quantity' => $stock->quantity + $quantity,
            'buying_price' => $buyingPrice,
            'selling_price' => $sellingPrice,
        ]);
    }

    private function updateWarehouseStock($stock, $quantity, $buyingPrice, $sellingPrice)
    {
        // Implement warehouse stock update logic when warehouse functionality is added
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



}
