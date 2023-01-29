<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class BranchesController extends Controller
{
    function index(){
        $data['branches'] = Branch::all();
        return view('branches.index',$data);
    }


    public function store(Request $request)
    {
        $data = new Branch();
        $data->name = $request->name;
        $data->code = $request->code;
        $data->save();
        Toastr::success('Branch has been created sucessfully', 'Done');
        return redirect()->route('branches.index');
    }
}
