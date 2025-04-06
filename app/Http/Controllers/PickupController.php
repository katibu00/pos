<?php

namespace App\Http\Controllers;

use App\Models\AwaitingPickup;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PickupController extends Controller
{
    public function index()
    {
        $awaitingPickups = AwaitingPickup::where('status', 'awaiting')
            ->select('receipt_no')
            ->distinct()
            ->with(['sale', 'stock'])
            ->paginate(15);
            
        return view('awaiting-pickups.index', compact('awaitingPickups'));
    }

    public function getSaleDetails($receipt_no)
    {
        $sales = Sale::where('receipt_no', $receipt_no)
            ->with(['stock', 'user', 'buyer'])
            ->get();
            
        if ($sales->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'Sale not found',
            ]);
        }
        
        // Check if any items are already marked for pickup
        $existingPickups = AwaitingPickup::where('receipt_no', $receipt_no)
            ->where('status', 'awaiting')
            ->get();
            
        return response()->json([
            'status' => 200,
            'sales' => $sales,
            'existingPickups' => $existingPickups,
        ]);
    }
    
    public function getAwaitingPickup($receipt_no)
    {
        $awaitingPickups = AwaitingPickup::where('receipt_no', $receipt_no)
            ->where('status', 'awaiting')
            ->with(['stock', 'sale'])
            ->get();
            
        if ($awaitingPickups->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No awaiting pickups found for this receipt',
            ]);
        }
        
        return response()->json([
            'status' => 200,
            'awaitingPickups' => $awaitingPickups,
        ]);
    }

    public function markAsAwaitingPickup(Request $request)
    {
        $request->validate([
            'receipt_no' => 'required|string',
            'items' => 'required|array',
            'items.*.sale_id' => 'required|exists:sales,id',
            'items.*.stock_id' => 'required|exists:stocks,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                // Check if the quantity is valid
                $sale = Sale::find($item['sale_id']);
                
                if ($item['quantity'] > $sale->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 400,
                        'message' => 'Cannot mark more items than were sold',
                    ]);
                }
                
                // Create awaiting pickup record
                $pickup = new AwaitingPickup();
                $pickup->receipt_no = $request->receipt_no;
                $pickup->stock_id = $item['stock_id'];
                $pickup->sale_id = $item['sale_id'];
                $pickup->quantity = $item['quantity'];
                $pickup->price = $item['price'];
                $pickup->note = $request->note ?? null;
                $pickup->user_id = Auth::id();
                $pickup->save();
                
                // Update stock quantity (add back to inventory)
                $stock = Stock::find($item['stock_id']);
                $stock->quantity += $item['quantity'];
                $stock->save();
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 201,
                'message' => 'Items marked as awaiting pickup successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Error marking items as awaiting pickup: ' . $e->getMessage(),
            ]);
        }
    }

    public function deliverItems(Request $request)
    {
        $request->validate([
            'receipt_no' => 'required|string',
            'items' => 'required|array',
            'items.*.pickup_id' => 'required|exists:awaiting_pickups,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                $pickup = AwaitingPickup::find($item['pickup_id']);
                
                // Check if the quantity is valid
                if ($item['quantity'] > $pickup->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 400,
                        'message' => 'Cannot deliver more items than are awaiting pickup',
                    ]);
                }
                
                // Check if there's enough stock
                $stock = Stock::find($pickup->stock_id);
                if ($stock->quantity < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 400,
                        'message' => 'Not enough stock available for ' . $stock->name,
                    ]);
                }
                
                // If delivering part of the quantity
                if ($item['quantity'] < $pickup->quantity) {
                    // Create a new record for the delivered portion
                    $delivered = new AwaitingPickup();
                    $delivered->receipt_no = $pickup->receipt_no;
                    $delivered->stock_id = $pickup->stock_id;
                    $delivered->sale_id = $pickup->sale_id;
                    $delivered->quantity = $item['quantity'];
                    $delivered->price = $pickup->price;
                    $delivered->status = 'delivered';
                    $delivered->note = $pickup->note;
                    $delivered->user_id = $pickup->user_id;
                    $delivered->delivery_user_id = Auth::id();
                    $delivered->delivered_at = now();
                    $delivered->save();
                    
                    // Update the original record
                    $pickup->quantity -= $item['quantity'];
                    $pickup->save();
                } else {
                    // Mark the entire record as delivered
                    $pickup->status = 'delivered';
                    $pickup->delivery_user_id = Auth::id();
                    $pickup->delivered_at = now();
                    $pickup->save();
                }
                
                // Update stock quantity (deduct from inventory)
                $stock->quantity -= $item['quantity'];
                $stock->save();
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 200,
                'message' => 'Items delivered successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Error delivering items: ' . $e->getMessage(),
            ]);
        }
    }
}