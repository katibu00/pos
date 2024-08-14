<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restock;
use App\Models\Branch;
use App\Models\RestockItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class RestockController extends Controller
{





    public function index(Request $request)
    {
        $restocks = Restock::with(['supplier', 'items', 'branchRestocks.branch'])
            ->when($request->branch_id, function($query, $branch_id) {
                return $query->whereHas('branchRestocks', function($q) use ($branch_id) {
                    $q->where('branch_id', $branch_id);
                });
            })
            ->when($request->type, function($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->status, function($query, $status) {
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

    public function createPlanned()
    {
        return view('restock.create_planned');
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
    
    
    

}
