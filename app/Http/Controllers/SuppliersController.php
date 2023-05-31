<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuppliersController extends Controller
{
    public function index()
    {
        $data['users'] = User::where('usertype', 'supplier')->get();
        return view('users.suppliers.index', $data);
    }

   
   
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'phone' => 'required|unique:users,phone',
        ]);

        $user = new User();
        $user->first_name = $request->first_name;
        $user->phone = $request->phone;
        $user->usertype = 'supplier';
        $user->password = Hash::make($request->phone);
        $user->save();
        Toastr::success('supplier has been created sucessfully', 'Done');
        return redirect()->route('suppliers.index');
    }

    public function delete(Request $request)
    {
        $user = User::find($request->id);
        $user->delete();
        Toastr::success('supplier has been deleted sucessfully', 'Done');
        return redirect()->route('suppliers.index');
    }

    public function edit($id)
    {
        $data['branches'] = Branch::all();
        $data['user'] = User::find($id);
        return view('users.suppliers.edit', $data);
    }


    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->first_name = $request->first_name;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->phone);
        $user->update();
        Toastr::success('supplier has been updated sucessfully', 'Done');
        return redirect()->route('suppliers.index');
    }
}
