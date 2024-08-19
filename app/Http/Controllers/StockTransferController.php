<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Stock;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StockTransferItem;
use Illuminate\Support\Str;


class StockTransferController extends Controller
{


   

    public function search(Request $request)
    {
        $fromBranchId = $request->input('from_branch_id');
        $toBranchId = $request->input('to_branch_id');
        $keyword = $request->input('keyword');

        $fromStocks = Stock::where('branch_id', $fromBranchId)
            ->where('name', 'like', "%{$keyword}%")
            ->take(10)
            ->get();

        $toStocks = Stock::where('branch_id', $toBranchId)
            ->where('name', 'like', "%{$keyword}%")
            ->take(10)
            ->get();

        return response()->json([
            'fromStocks' => $fromStocks,
            'toStocks' => $toStocks,
        ]);
    }





  
        public function index()
        {
            $transfers = StockTransfer::with(['fromBranch', 'toBranch'])
                ->latest()
                ->paginate(10);
            return view('restock.stock-transfers.index', compact('transfers'));
        }
    
        public function create()
        {
            $branches = Branch::all();
            return view('restock.stock-transfers.create', compact('branches'));
        }
    
        public function store(Request $request)
        {
            $request->validate([
                'from_branch_id' => 'required|exists:branches,id',
                'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
                'transfers' => 'required|array',
                'transfers.*.from_stock_id' => 'required|exists:stocks,id',
                'transfers.*.to_stock_id' => 'required|exists:stocks,id',
                'transfers.*.quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);
    
            DB::beginTransaction();
    
            try {
                $stockTransfer = StockTransfer::create([
                    'transfer_number' => 'TRF-' . Str::upper(Str::random(8)),
                    'from_branch_id' => $request->from_branch_id,
                    'to_branch_id' => $request->to_branch_id,
                    'transfer_date' => now(),
                    'notes' => $request->notes,
                ]);
    
                foreach ($request->transfers as $transfer) {
                    $fromStock = Stock::findOrFail($transfer['from_stock_id']);
                    $toStock = Stock::findOrFail($transfer['to_stock_id']);
    
                    if ($fromStock->quantity < $transfer['quantity']) {
                        throw new \Exception("Insufficient quantity for stock: {$fromStock->name}");
                    }
    
                    StockTransferItem::create([
                        'stock_transfer_id' => $stockTransfer->id,
                        'from_stock_id' => $fromStock->id,
                        'to_stock_id' => $toStock->id,
                        'quantity' => $transfer['quantity'],
                    ]);
    
                    $fromStock->decrement('quantity', $transfer['quantity']);
                    $toStock->increment('quantity', $transfer['quantity']);
                }
    
                DB::commit();
                return response()->json(['message' => 'Stock transfer completed successfully', 'transfer_id' => $stockTransfer->id], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 422);
            }
        }
    
        public function show($id)
        {
            $transfer = StockTransfer::with(['fromBranch', 'toBranch', 'items.fromStock', 'items.toStock'])->findOrFail($id);
            return view('restock.stock-transfers.show', compact('transfer'));
        }
    




}