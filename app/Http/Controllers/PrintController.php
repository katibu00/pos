<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function receipt($id)
    {


      $receipt =  Sale::where('id', $id)->first();

        $sales = Sale::where('receipt_no', $receipt->receipt_no)->get();

        return view('print.receipt', compact('sales'));

    }
}
