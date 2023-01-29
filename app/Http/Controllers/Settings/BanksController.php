<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Stock;
use Illuminate\Http\Request;

class BanksController extends Controller
{
    function index(){
        $data['banks'] = Bank::all();
        return view('banks.index',$data);
    }

    function store(Request $request){


        $productCount = count($request->name);
        if($productCount != NULL){
            for ($i=0; $i < $productCount; $i++){
                $data = new Bank();
                $data->name = $request->name[$i];
                $data->save();
            }
        }
        // Toastr::success('Class has been Assigned sucessfully', 'success');
        return redirect()->route('banks.index');

    }
}
