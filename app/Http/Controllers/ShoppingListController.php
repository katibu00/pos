<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Reorder;
use App\Models\ReorderExpense;
use App\Models\Stock;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShoppingListController extends Controller
{

    public function index()
    {

        if (in_array(auth()->user()->id, [4, 443])) {
            $data['branches'] = Branch::all();
        } else {
            $data['branches'] = Branch::where('id', auth()->user()->branch_id)->get();
        }   
        $data['suppliers'] = User::where('usertype', 'supplier')->get();
        $data['stocks'] = [];

        return view('purchases.reorder.index', $data);
    }


    public function allIndex()
    {
        if (in_array(auth()->user()->id, [4, 443])) {
            $data['branches'] = Branch::all();
        } else {
            $data['branches'] = Branch::where('id', auth()->user()->branch_id)->get();
        }   
        $data['suppliers'] = User::where('usertype', 'supplier')->get();
    
        $reorders = Reorder::select('reorder_no', DB::raw('MIN(created_at) as date'), 'supplier_id', 'status')
            ->groupBy('reorder_no', 'supplier_id', 'status')
            ->orderBy('date', 'desc')
            ->limit(20)
            ->get();
    
            $reorderGroups = [];

            foreach ($reorders as $reorder) {
                $reorderItems = Reorder::where('reorder_no', $reorder->reorder_no)->get();
            
                $total = $reorderItems->sum(function ($item) {
                    return $item->quantity * $item->buying_price;
                });
            
                $supplier = $reorderItems->first()->supplier;
                $supplierName = $supplier ? $supplier->first_name . ' ' . $supplier->last_name : 'Unknown Supplier';
            
                $reorderGroup = [
                    'reorder_no' => $reorder->reorder_no,
                    'date' => date('l, d F', strtotime($reorder->date)),
                    'supplier' => $supplierName,
                    'total' => $total,
                    'status' => $reorder->status,
                ];
            
                $reorderGroups[] = $reorderGroup;
            }
            
            $data['reorderGroups'] = $reorderGroups;
            
    
        return view('purchases.reorder.all_reorders', $data);
    }
    


    public function store(Request $request)
    {

        $year = date('Y');
        $month = Carbon::now()->format('m');
        $day = Carbon::now()->format('d');
        $last = Reorder::whereDate('created_at', '=', date('Y-m-d'))->latest()->first();
        if ($last == null) {
            $last_record = '1/0';
        } else {
            $last_record = $last->reorder_no;
        }
        $exploded = explode("/", $last_record);
        $number = $exploded[1] + 1;
        $padded = sprintf("%04d", $number);
        $stored = $year . $month . $day . '/' . $padded;

        $productCount = count($request->product_id);
        if ($productCount != null) {
            for ($i = 0; $i < $productCount; $i++) {
                $data = new Reorder();
                $data->branch_id = $request->bind_branch_id;
                $data->reorder_no = $stored;
                $data->product_id = $request->product_id[$i];
                $data->buying_price = $request->buying_price[$i];
                $data->quantity = $request->product_quantity[$i];
                $data->supplier_id = $request->supplier;
                $data->save();
            }
        }

        return response()->json([
            'status' => 201,
            'message' => 'Reorder has been placed sucessfully',
        ]);

    }

    public function filterProducts(Request $request)
    {
        $branchId = $request->input('branch_id');
        $productType = $request->input('product_type');

        $query = Stock::query();

        // Filter by branch
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // Filter by product type
        switch ($productType) {
            case 'critical':
                $query->whereColumn('quantity', '<=', 'critical_level');
                break;
            case 'out-of-stock':
                $query->where('quantity', 0);
                break;
            case 'low':
                $query->whereColumn('quantity', '<=', DB::raw('2 * critical_level'));
                break;
            case 'well-stocked':
                $query->whereColumn('quantity', '>', DB::raw('2 * critical_level'));
                break;
            case 'all':
                // No additional filtering required
                break;
        }

        $products = $query->get();

        return response()->json([
            'products' => $products,
        ]);
    }

    public function fetchReorders(Request $request)
    {
        $branchId = $request->input('branch_id');
        $reorderType = $request->input('reorder_type');

        $reorders = Reorder::query()
            ->select('reorder_no', DB::raw('MIN(created_at) as date'), 'supplier_id', 'status')
            ->when($branchId, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($reorderType !== 'all', function ($query) use ($reorderType) {
                if (is_numeric($reorderType)) {
                    $query->where('supplier_id', $reorderType);
                } else {
                    $query->where('status', $reorderType);
                }
            })
            ->groupBy('reorder_no', 'supplier_id', 'status')
            ->latest()
            ->get();

        $reorderGroups = [];

        foreach ($reorders as $reorder) {
            $reorderItems = Reorder::where('reorder_no', $reorder->reorder_no)->get();

            $total = 0;

            foreach ($reorderItems as $item) {
                $total += $item->quantity * $item->product->buying_price;
            }

            $supplier = $reorderItems->first()->supplier;
            $supplierName = $supplier ? $supplier->first_name . ' ' . $supplier->last_name : 'Unknown Supplier';

            $firstReorder = $reorderItems->first();
            $firstDate = $firstReorder->created_at->format('l, d F');
            $firstStatus = $firstReorder->status;

            $reorderGroup = [
                'reorder_no' => $reorder->reorder_no,
                'date' => $firstDate,
                'supplier' => $supplierName,
                'status' => $firstStatus,
                'total' => $total,
            ];

            $reorderGroups[] = $reorderGroup;
        }

        return response()->json($reorderGroups);
    }

    public function complete($reorderNo)
    {

        $data['records'] = Reorder::where('reorder_no', $reorderNo)->get();
        $data['reorderNo'] = $reorderNo;
        return view('purchases.reorder.complete', $data);
    }

    public function completeSubmit(Request $request)
    {
        $data = $request->all();

        foreach ($data['product_id'] as $key => $productId) {
            $afterBuyingPrice = $data['after_buying_price'][$key];
            $afterSellingPrice = $data['after_selling_price'][$key];

            // Update buying and selling prices if they are not null
            if ($afterBuyingPrice !== null && $afterSellingPrice !== null) {
                $suppliedQuantity = $data['supplied_quantity'][$key];

                $stock = Stock::where('id', $productId)->first();

                // Update the buying and selling prices
                $stock->quantity += $suppliedQuantity;
                $stock->buying_price = $afterBuyingPrice;
                $stock->selling_price = $afterSellingPrice;
                $stock->save();
            }
        }

        // Find and update the reorders with the given reorder_no
        $reorders = Reorder::where('reorder_no', $data['reorder_no'])->get();
        foreach ($reorders as $reorder) {
            $reorder->status = 'completed';
            $reorder->save();
        }

        Toastr::success('Reorder completed successfully.');
        return redirect()->route('reorder.all.index');
    }

    public function downloadPDF(Request $request)
    {
        $reorderNo = $request->input('reorder_no');

        $records = Reorder::where('reorder_no', $reorderNo)->get();

        $pdf = Pdf::loadView('pdf.reorder', compact('records'));

        $filename = 'reorder_' . $reorderNo . '.pdf';

        return $pdf->download($filename);
    }

    public function destroyReorders(Request $request)
    {
        $reorderNo = $request->input('reorder_no');

        if (empty($reorderNo)) {
            return response()->json(['message' => 'No reorder number provided'], 400);
        }

        $deletedRows = Reorder::where('reorder_no', $reorderNo)->delete();

        if ($deletedRows === 0) {
            return response()->json(['message' => 'No reorders found'], 404);
        }

        return response()->json(['message' => 'Reorders deleted successfully']);
    }

    public function updateSupplier(Request $request)
    {
        $reorderNo = $request->input('reorder_no');
        $supplierId = $request->input('supplier_id');

        Reorder::where('reorder_no', $reorderNo)
            ->update(['supplier_id' => $supplierId]);

        return response()->json(['message' => 'Supplier updated successfully. Refresh the page to see changes.']);
    }

    public function details(Request $request)
    {
        $reorderNo = $request->input('reorder_no');

        $data = Reorder::with('product')->where('reorder_no', $reorderNo)->get();

        return response()->json($data);
    }

    public function saveExpenses(Request $request)
    {

        $validatedData = $request->validate([
            'reorderNumber' => 'required',
            'category' => 'required',
            'amount' => 'required|array',
            'description' => 'nullable|array',
        ]);

        $reorderNo = $validatedData['reorderNumber'];
        $categories = $validatedData['category'];
        $amounts = $validatedData['amount'];
        $descriptions = $validatedData['description'];

        for ($i = 0; $i < count($categories); $i++) {
            $expense = new ReorderExpense();
            $expense->reorder_no = $reorderNo;
            $expense->category = $categories[$i];
            $expense->amount = $amounts[$i];
            $expense->description = isset($descriptions[$i]) ? $descriptions[$i] : null;
            $expense->save();
        }

        return response()->json(['message' => 'Expenses saved successfully.']);
    }


    public function profitabilityForecast(Request $request)
    {
        $reorderNo = $request->input('reorderNo'); 
    
        // Fetch reorder items for the given reorder number
        $reorderItems = Reorder::with('product')->where('reorder_no', $reorderNo)->get();
    
        // Calculate the total inventory cost
        $totalInventoryCost = $reorderItems->sum(function ($item) {
            return $item->quantity * $item->buying_price;
        });
    
        // Fetch reorder expenses for the given reorder number
        $reorderExpenses = ReorderExpense::where('reorder_no', $reorderNo)->get();
    
        // Calculate the total expenses
        $totalExpenses = $reorderExpenses->sum('amount');
    
      
    
      
        // Prepare the response data
        $responseData = [
            'reorderItems' => $reorderItems,
            'reorderExpenses' => $reorderExpenses,
            'totalInventoryCost' => $totalInventoryCost,
            'totalExpenses' => $totalExpenses,
        ];
    
        return response()->json($responseData);
    }
    


}
