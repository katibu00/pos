<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        $data['users'] = User::where('usertype', '!=', 'customer')->get();
        $data['branches'] = Branch::all();
        return view('users.index', $data);
    }

    public function customersIndex()
    {
        $data['customers'] = User::where('usertype', 'customer')->where('branch_id', auth()->user()->branch_id)->get();
        return view('users.customers.index', $data);
    }

    public function customerStore(Request $request)
    {
        $request->validate([
            'first_name'=>'required',
            'phone'=>'required|unique:users,phone',
        ]);
        $user = new User();
        $user->branch_id = auth()->user()->branch_id;
        $user->first_name = $request->first_name;
        $user->phone = $request->phone;
        $user->balance = 0;
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
        if ($user->id == Auth::user()->id) {
            Toastr::error('You cannot delete yourself', 'Warning');
            return redirect()->route('users.index');
        }
        $user->delete();
        Toastr::success('User has been deleted sucessfully', 'Done');
        return redirect()->route('users.index');
    }

    public function edit($id)
    {
        $data['branches'] = Branch::all();
        $data['user'] = User::find($id);
        return view('users.edit', $data);
    }

    public function customerProfile($id)
    {
        $data['user'] = User::select('id', 'first_name', 'balance')->where('id', $id)->first();
        $data['dates'] = Sale::select('stock_id', 'receipt_no', 'created_at','status')
                        ->where('payment_method', 'credit')
                        ->where(function ($query) use ($id) {
                            $query->where('status', '!=', 'paid')
                                ->orWhereNull('status');
                        })
                        ->where('customer_name', $id)
                        ->groupBy('receipt_no')
                        ->orderBy('created_at', 'desc')
                        ->get();
        // dd($data['dat÷es']);
        $data['payments'] = Payment::select('id','payment_amount','payment_method','created_at')->where('customer_id', $id)->orderBy('created_at', 'desc')->take(10)->get();
        return view('users.customers.profile', $data);
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

        // dd($request->all());
        $customer = User::find($request->customer_id);
        $receipt_nos = [];
        $total_amount_paid = 0;

        $rowCount = count($request->receipt_no);
        if ($rowCount != null) {
            for ($i = 0; $i < $rowCount; $i++) {

                if ($request->payment_option[$i] == "Full Payment") {
                    $receiptNo = $request->receipt_no[$i];
                    $sales = DB::table('sales')
                                    ->where('receipt_no', $receiptNo)
                                    ->get();
                    $total_amount = 0;
                    if($sales[0]->status)
                    {
                        foreach($sales as $sale)
                        {
                            $total_amount += $sale->price*$sale->quantity-$sale->discount;
                        }
                        DB::table('sales')
                        ->where('receipt_no', '=', $request->receipt_no[$i])
                        ->update(['status' => 'paid']);
                        $amount_paid = $total_amount - $sales[0]->payment_amount;

                        $customer->balance = $customer->balance - $amount_paid;
                        $customer->update();

                        array_push($receipt_nos, $receiptNo);
                        $total_amount_paid += $amount_paid;
                       
                    }else
                    {
                        DB::table('sales')
                            ->where('receipt_no', '=', $request->receipt_no[$i])
                            ->update(['status' => 'paid']);

                        $customer->balance = $customer->balance - $request->full_price[$i];
                        $customer->update();

                        array_push($receipt_nos, $request->receipt_no[$i]);
                        $total_amount_paid += $request->full_price[$i];
                    }
                   
                }
                if ($request->payment_option[$i] == "Partial Payment") {

                    try {
                        DB::beginTransaction();
                    
                        $receiptNo = $request->receipt_no[$i];
                        $partialAmount = $request->partial_amount[$i];
                    
                        $sale = DB::table('sales')
                                    ->where('receipt_no', $receiptNo)
                                    ->first();
                    
                        if (!$sale) {
                            throw new Exception("Sale not found for receipt no: $receiptNo");
                        }
                    
                        $newPaymentAmount = $sale->payment_amount + $partialAmount;
                    
                        DB::table('sales')
                            ->where('receipt_no', $receiptNo)
                            ->update([
                                'status' => 'partial',
                                'payment_amount' => $newPaymentAmount
                            ]);
                    
                        DB::commit();
                    
                        // Success message or redirect

                        $customer->balance = $customer->balance - $request->partial_amount[$i];

                        array_push($receipt_nos, $request->receipt_no[$i]);
                        $total_amount_paid += $request->partial_amount[$i];
                        
                    } catch (Exception $e) {
                        DB::rollback();
                    
                        // Log error message and return error response
                    }
                    


                    // $customer->balance = $customer->balance - $request->partial_amount[$i];

                    // array_push($receipt_nos, $request->receipt_no[$i]);
                    // $total_amount_paid += $request->partial_amount[$i];


                }

            }
        }
        $customer->update();

        if($total_amount_paid != 0)
        {
            $record = new Payment();
            $record->payment_method = $request->payment_method;
            $record->payment_amount += $total_amount_paid;
            $record->branch_id = auth()->user()->branch_id;
            $record->customer_id = $request->customer_id;
            $record->receipt_nos = implode(',', $receipt_nos);
            $record->user_id = auth()->user()->id;
            $record->save();

            Toastr::success('Sales has been Recorded sucessfully', 'Done');
            return redirect()->back();    

        }

       

        Toastr::warning('Sales amount is zero. Nothing Recorded', 'Not Recorded');
        return redirect()->back();

    }

}
