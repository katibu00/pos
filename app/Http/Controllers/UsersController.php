<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        $data['users'] = User::where('usertype','!=','customer')->get();
        $data['branches'] = Branch::all();
        return view('users.index', $data);
    }

    public function customersIndex()
    {
        $data['customers'] = User::where('usertype','customer')->where('branch_id', auth()->user()->branch_id)->get();
        return view('users.customers.index', $data);
    }

    public function customerStore(Request $request)
    {
        $user = new User();
        $user->branch_id = auth()->user()->branch_id;
        $user->first_name = $request->first_name;
        $user->phone = $request->phone;
        $user->balance = $request->balance;
        $user->usertype = 'customer';
        $user->password = Hash::make(12345678);
        $user->save();
        Toastr::success('Customer has been created sucessfully', 'Done');
        return redirect()->route('customers.index');
    }

    public function store(Request $request)
    {
        $user = new User();
        $user->branch_id = $request->branch_id;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->usertype = $request->position;
        $user->password = Hash::make($request->password);
        $user->save();
        Toastr::success('User has been created sucessfully', 'Done');
        return redirect()->route('users.index');
    }

    public function delete(Request $request)
    {
        $user = User::find($request->id);
        if($user->id == Auth::user()->id){
            Toastr::error('You cannot delete yourself', 'Warning');
            return redirect()->route('users.index');
        }
        $user->delete();
        Toastr::success('User has been deleted sucessfully', 'Done');
        return redirect()->route('users.index');
    }

    function edit($id){
        $data['branches'] = Branch::all();
        $data['user'] = User::find($id);
        return view('users.edit',$data);
    }

    function customerProfile($id){
        $data['user'] = User::select('id','first_name','balance')->where('id',$id)->first();
        $data['dates'] = Sale::select('stock_id','receipt_no','created_at')->where('customer_name', $id)->groupBy('receipt_no')->orderBy('created_at','desc')->paginate(200);
        $data['payments'] = Payment::select('payment_amount','payment_type','created_at')->where('customer_id', $id)->orderBy('created_at','desc')->take(10)->get();
        return view('users.customers.profile',$data);
    }

    
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->branch_id = $request->branch_id;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->usertype = $request->position;
        $user->password = Hash::make($request->password);
        $user->update();
        Toastr::success('User has been updated sucessfully', 'Done');
        return redirect()->route('users.index');
    }

    public function savePayment(Request $request)
    {

        dd($request->all());

    }

}
