<?php

namespace App\Http\Controllers;

use App\Models\Debtor;
use Illuminate\Http\Request;

class DebtorsController extends Controller
{
    function index(){
        $data['debtors'] = Debtor::all();
        return view('debtors.index',$data);
    }

    function store(Request $request){


        $productCount = count($request->name);
        if($productCount != NULL){
            for ($i=0; $i < $productCount; $i++){
                $data = new Debtor();
                $data->name = $request->name[$i];
                $data->save();
            }
        }
        // Toastr::success('Class has been Assigned sucessfully', 'success');
        return redirect()->route('debtors.index');

    }
}
