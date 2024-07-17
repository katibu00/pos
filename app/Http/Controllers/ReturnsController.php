<?php

namespace App\Http\Controllers;

use App\Models\CashCredit;
use Illuminate\Support\Str;
use App\Models\Expense;
use App\Models\FundTransfer;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnsController extends Controller
{

    public function allIndex()
    {
        $data['returns'] = Returns::select('product_id', 'return_no')->where('branch_id', auth()->user()->branch_id)->groupBy('return_no')->orderBy('created_at', 'desc')->paginate(10);
        return view('returns.all.index', $data);
    }

    public function loadReceipt(Request $request)
    {
        $items = Returns::with('product')->where('return_no', $request->return_no)->get();
        return response()->json([
            'status' => 200,
            'items' => $items,
        ]);
    }




}
