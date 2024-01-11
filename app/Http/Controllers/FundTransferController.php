<?php

namespace App\Http\Controllers;

use App\Models\FundTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FundTransferController extends Controller
{
    public function index()
    {
        $fundTransfers = FundTransfer::where('branch_id', auth()->user()->branch_id)->latest()->paginate(10);

        return view('fund_transfer.index', ['fundTransfers' => $fundTransfers]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'from_account' => [
                'required',
                'in:cash,transfer,pos',
                Rule::notIn([$request->input('to_account')]),
            ],
            'to_account' => 'required|in:cash,transfer,pos',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            // Return a JSON response with validation errors
            return response()->json([
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        $fundTransfer = new FundTransfer();
        $fundTransfer->description = $request->input('description');
        $fundTransfer->from_account = $request->input('from_account');
        $fundTransfer->to_account = $request->input('to_account');
        $fundTransfer->amount = $request->input('amount');
        $fundTransfer->branch_id = auth()->user()->branch_id;
        $fundTransfer->save();

        return response()->json([
            'success' => true,
            'message' => 'Funds transfer created successfully!',
        ]);
    }

}
