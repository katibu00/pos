<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EstimateController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data['products'] = Stock::where('branch_id', $user->branch_id)->orderBy('name')->get();
        $data['recents'] = Estimate::select('product_id', 'estimate_no', 'customer')->whereDate('created_at', Carbon::today())->where('cashier_id', auth()->user()->id)->groupBy('estimate_no')->orderBy('created_at', 'desc')->take(4)->get();
        return view('estimate.index', $data);
    }
    public function allIndex()
    {
        $user = auth()->user();
        $data['estimates'] = Estimate::select('product_id', 'estimate_no')->where('branch_id', auth()->user()->branch_id)->groupBy('estimate_no')->orderBy('created_at', 'desc')->paginate(10);
        $data['customers'] = User::select('id', 'first_name')->where('branch_id', $user->branch_id)->where('usertype', 'customer')->orderBy('first_name')->get();
        $data['staffs'] = User::whereIn('usertype', ['admin', 'cashier'])->where('branch_id', auth()->user()->branch_id)->get();

        return view('estimate.all_index', $data);
    }

    public function store(Request $request)
    {
        $transaction_id = Str::uuid();

        $productCount = count($request->product_id);
        if ($productCount != null) {
            for ($i = 0; $i < $productCount; $i++) {

                $data = new Estimate();
                $data->branch_id = auth()->user()->branch_id;
                $data->estimate_no = $transaction_id;
                $data->product_id = $request->product_id[$i];
                $data->price = $request->price[$i];
                $data->quantity = $request->quantity[$i];
                if ($request->discount[$i] == null) {
                    $data->discount = 0;

                } else {
                    $data->discount = $request->discount[$i];
                }
                $data->cashier_id = auth()->user()->id;
                $data->customer = $request->customer;
                $data->note = $request->note;
                if ($request->input('toggleLabor')) {
                    $data->labor_cost = $request->input('labor_cost');
                }
                $data->save();
            }
        }

        return response()->json([
            'status' => 201,
            'message' => 'Estimate has been Saved sucessfully',
        ]);

    }

    public function refresh()
    {
        $data['recents'] = Estimate::select('product_id', 'estimate_no')->whereDate('created_at', Carbon::today())->where('cashier_id', auth()->user()->id)->groupBy('estimate_no')->orderBy('created_at', 'desc')->take(4)->get();
        return view('estimate.recents_table', $data)->render();
    }
   
    public function loadReceipt(Request $request)
    {
        $items = Estimate::with('product')->where('estimate_no', $request->estimate_no)->get();
        $transactionDate = $items->first()->created_at->format('F j, Y h:i A');
        $accountDetails = [
            'account_number' => '8033174228',
            'account_name' => 'Umar Katibu',
            'bank_name' => 'Opay Bank'
        ];
    
        return response()->json([
            'status' => 200,
            'items' => $items,
            'transaction_date' => $transactionDate,
            'account_details' => $accountDetails,
        ]);
    }
    

    public function allStore(Request $request)
    {
        $estimates = Estimate::where('estimate_no', $request->estimate_no)->get();

        $transaction_id = Str::uuid();

        $total_amount = 0;

        if ($request->payment_method == 'credit') {
            foreach ($estimates as $estimate) {
                $total_amount += $estimate->price * $estimate->quantity - $estimate->discount;
            }
        }

        foreach ($estimates as $estimate) {

            $product = Stock::select('id', 'quantity', 'selling_price','buying_price')->where('id', $estimate->product_id)->first();

            if ($product->quantity >= $estimate->quantity) {
                $data = new Sale();
                $data->branch_id = auth()->user()->branch_id;
                $data->receipt_no = $transaction_id;
                $data->stock_id = $estimate->product_id;
                $data->price = $product->selling_price;
                $data->buying_price = $product->buying_price;
                $data->quantity = $estimate->quantity;
                $data->discount = $estimate->discount ?? 0;
                $data->payment_method = $request->payment_method;
                $data->payment_amount = 0;
                $data->user_id = auth()->user()->id;
                if ($request->payment_method == 'credit') {
                    $data->customer = $request->customer;
                } else {
                    $data->customer = $estimate->customer;
                }
                $data->note = null;
                $data->save();

                $product->quantity -= $estimate->quantity;
                $product->update();
                $estimate->delete();
            } else {
                Toastr::error('Out of Stock occured in one or more items');
            }
        }

        if ($request->payment_method == 'credit') {
            $user = User::select('id', 'balance')->where('id', $request->customer)->first();
            $user->balance += $total_amount;
            $user->update();
        }

        Toastr::success('Estimate has been Marked as Sold sucessfully', 'Done');
        return redirect()->route('estimate.all.index');
    }


    public function allSearch(Request $request)
    {
        $query = $request->input('query');
        $user = auth()->user();

        $data['estimates'] = Estimate::select('product_id', 'estimate_no')
            ->where('branch_id', $user->branch_id)
            ->where(function ($queryBuilder) use ($query) {
                $queryBuilder->whereHas('buyer', function ($queryBuilder) use ($query) {
                    $queryBuilder->where('first_name', 'LIKE', '%' . $query . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $query . '%');
                })
                ->orWhere('note', 'LIKE', '%' . $query . '%');
            })
            ->groupBy('estimate_no')
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();

        $data['customers'] = User::select('id', 'first_name')->where('branch_id', $user->branch_id)->where('usertype', 'customer')->orderBy('first_name')->get();
        $data['staffs'] = User::whereIn('usertype', ['admin', 'cashier'])->where('branch_id', $user->branch_id)->get();

        return view('estimate.all_table', $data)->render();
    }


    public function filterSales(Request $request)
    {
        $cashierId = $request->input('cashier_id');
        $user = auth()->user();
        $query = Estimate::select('product_id', 'estimate_no')
            ->where('branch_id', auth()->user()->branch_id);

        if ($cashierId && $cashierId != 'all') {
            $query->where('cashier_id', $cashierId);
        }

        $data['estimates'] = $query->groupBy('estimate_no')
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();

        $data['customers'] = User::select('id', 'first_name')->where('branch_id', $user->branch_id)->where('usertype', 'customer')->orderBy('first_name')->get();
        $data['staffs'] = User::whereIn('usertype', ['admin', 'cashier'])->where('branch_id', auth()->user()->branch_id)->get();

        return view('estimate.all_table', $data)->render();
    }

    public function edit(Request $request)
    {
        $request->validate([
            'encoded_estimate_no' => 'required',
        ]);

        $estimate_no = urldecode($request->input('encoded_estimate_no'));

        $estimates = Estimate::with('product')->where('estimate_no', $estimate_no)->get();

        if ($estimates->isEmpty()) {
            return response()->json(['error' => 'Estimates not found'], 404);
        }

        // Check for price changes
        $priceChanges = [];

        foreach ($estimates as $estimate) {
            $currentPrice = $estimate->price;
            $newSellingPrice = Stock::find($estimate->product_id)->selling_price;

            if ($currentPrice != $newSellingPrice) {
                $priceChanges[$estimate->id] = $newSellingPrice;
            }
        }
        $products = Stock::where('branch_id', auth()->user()->branch_id)->get();


        return response()->json(['estimates' => $estimates, 'price_changes' => $priceChanges,'products' => $products]);
    }

    public function update(Request $request)
    {
        // Validate the request data as needed
        $request->validate([
            'product.*' => 'required|exists:stocks,id',
            'price.*' => 'required|numeric|min:0',
            'quantity.*' => 'required|integer|min:1',
            'discount.*' => 'nullable|numeric', // Added discount validation
            'estimate_no' => 'required',
            'labor_cost' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $branch_id = auth()->user()->branch_id;

        $estimateNo = $request->input('estimate_no');

        $estimates = Estimate::where('estimate_no', $estimateNo)
            ->where('branch_id', $branch_id)
            ->get();

        if ($estimates->isEmpty()) {
            return response()->json(['error' => 'Estimates not found'], 404);
        }

        $updatedEstimateIds = [];

        foreach ($request->input('product') as $key => $productId) {
            // Check if the estimate already exists
            $estimate = $estimates->where('product_id', $productId)->first();

            // If the estimate exists, update it; otherwise, create a new one
            if ($estimate) {
                $estimate->update([
                    'price' => $request->input('price.' . $key),
                    'quantity' => $request->input('quantity.' . $key),
                    'discount' => $request->input('discount.' . $key) ?? 0,
                    'labor_cost' => $request->input('labor_cost'),
                    'note' => $request->input('note'),
                ]);

                $updatedEstimateIds[] = $estimate->id;
            } else {
                // Create a new estimate
                $newEstimate = Estimate::create([
                    'branch_id' => $branch_id,
                    'cashier_id' => auth()->user()->id,
                    'estimate_no' => $estimateNo,
                    'product_id' => $productId,
                    'price' => $request->input('price.' . $key),
                    'quantity' => $request->input('quantity.' . $key),
                    'discount' => $request->input('discount.' . $key) ?? 0,
                    'labor_cost' => $request->input('labor_cost'),
                    'note' => $request->input('note'),
                ]);

                $updatedEstimateIds[] = $newEstimate->id;
            }
        }

        // Delete estimates that were not updated or created
        $estimatesToDelete = $estimates->whereNotIn('id', $updatedEstimateIds);

        foreach ($estimatesToDelete as $estimateToDelete) {
            $estimateToDelete->delete();
        }

        // Return a success response or any additional data if needed
        return response()->json(['message' => 'Estimates updated successfully']);
    }


}
