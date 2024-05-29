<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashCredit;
use App\Models\Payment;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Notifications\SalesNotification;
use Illuminate\Pagination\LengthAwarePaginator;


class UsersController extends Controller
{
    public function index()
    {
        $data['users'] = User::whereNotIn('usertype', ['customer', 'supplier'])->get();
        $data['branches'] = Branch::all();
        return view('users.index', $data);
    }


    public function customersIndex()
    {
        $branchId = auth()->user()->branch_id;
    
        // Fetch all customers of the branch
        $customers = User::where('usertype', 'customer')
            ->where('branch_id', $branchId)
            ->orderBy('first_name')
            ->get();
    
        $customerData = [];
    
        foreach ($customers as $customer) {
            $customerId = $customer->id;
    
            // Fetch credit sales transactions for the customer that are not fully paid
            $sales = Sale::where('customer', $customerId)
                ->where('branch_id', $branchId)
                ->where('payment_method', 'credit')
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhere('status', 'partial');
                })
                ->orderBy('receipt_no')
                ->get()
                ->groupBy('receipt_no');
    
            $totalCreditBalance = 0;
            $lastSalesDate = null;
    
            foreach ($sales as $receiptNo => $salesGroup) {
                $transactionOwed = 0;
                $transactionPaid = $salesGroup->first()->payment_amount ?? 0;
    
                foreach ($salesGroup as $sale) {
                    $transactionOwed += ($sale->price * ($sale->quantity)) - $sale->discount;
                    $lastSalesDate = $lastSalesDate ? max($lastSalesDate, $sale->created_at) : $sale->created_at;
                }
    
                // Fetch the return transactions matching the receipt_no
                $returns = Returns::where('branch_id', $branchId)
                    ->where('return_no', $receiptNo)
                    ->get();
    
                foreach ($returns as $return) {
                    $transactionOwed -= (($return->price * $return->quantity) - $return->discount);
                }
    
                // Calculate total credit balance for this transaction
                $totalCreditBalance += $transactionOwed - $transactionPaid;
            }
    
            // Fetch the last payment date and amount for the customer
            $lastPayment = Payment::where('customer_id', $customerId)
                ->where('branch_id', $branchId)
                ->orderBy('created_at', 'desc')
                ->first();
    
            $lastPaymentDate = $lastPayment ? $lastPayment->created_at : null;
            $lastPaymentAmount = $lastPayment ? $lastPayment->payment_amount : 'N/A';
    
            $daysSinceLastPayment = $lastPaymentDate ? now()->diffInDays($lastPaymentDate) : ($lastSalesDate ? now()->diffInDays($lastSalesDate) : 'N/A');
    
            $customerData[] = [
                'customer_id' => $customerId,
                'first_name' => $customer->first_name,
                'phone' => $customer->phone,
                'credit_balance' => $totalCreditBalance,
                'deposit' => $customer->deposit,
                'last_sales_date' => $lastSalesDate ? $lastSalesDate->format('Y-m-d') : 'N/A',
                'last_payment_date' => $lastPaymentDate ? $lastPaymentDate->format('Y-m-d') : 'N/A',
                'days_since_last_payment' => $daysSinceLastPayment !== 'N/A' ? $daysSinceLastPayment . ' days' : 'N/A',
                'last_payment_amount' => $lastPaymentAmount, // Include last payment amount
            ];
        }
    
        // Sort customers by days since last payment
        usort($customerData, function ($a, $b) {
            return $b['days_since_last_payment'] <=> $a['days_since_last_payment'];
        });
    
        // Paginate results
        $perPage = 2000; // Adjust the number of customers per page as needed
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $itemCollection = collect($customerData);
        $paginatedCustomers = $itemCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedCustomers = new LengthAwarePaginator($paginatedCustomers, count($itemCollection), $perPage);
    
        $paginatedCustomers->setPath(route('customers.index'));
        return view('users.customers.index', ['customers' => $paginatedCustomers]);
    }


    public function customerStore(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'phone' => 'required|unique:users,phone',
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
        return redirect()->route('customers.index')->with('success','Customer has been created sucessfully');
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

    public function editCustomer($id)
    {
        $data['user'] = User::find($id);
        return view('users.customers.edit', $data);
    }

    public function customerProfile($id)
    {
        $data['user'] = User::select('id', 'first_name', 'balance','deposit')->where('id', $id)->first();
        $data['dates'] = Sale::select('stock_id', 'receipt_no', 'created_at', 'status')
            ->where('payment_method', 'credit')
            ->where(function ($query) use ($id) {
                $query->where('status', '!=', 'paid')
                    ->orWhereNull('status');
            })
            ->where('customer', $id)
            ->groupBy('receipt_no')
            ->orderBy('created_at', 'desc')
            ->get();
        // dd($data['dat÷es']);
        $data['payments'] = Payment::select('id', 'payment_amount', 'payment_method', 'created_at')->where('payment_type', 'credit')->where('customer_id', $id)->orderBy('created_at', 'desc')->take(20)->get();

        $data['shoppingHistory'] = Sale::where('customer', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                // Group the sales by date
                return $item->created_at->toDateString();
            });


        $data['cashcredits'] = CashCredit::where('customer_id', $id)
            ->whereColumn('amount', '>', 'amount_paid')
            ->get();

        $data['totalCashCreditOwed'] = $data['cashcredits']->sum(function ($cashcredit) {
                return $cashcredit->amount - $cashcredit->amount_paid;
            });
        return view('users.customers.profile', $data);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
    
        $user->branch_id = $request->branch_id;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->max_salary = $request->max_salary;
        $user->usertype = $request->position;
    
        // Check if the password is provided in the request
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    
        $user->update();
    
        Toastr::success('User has been updated successfully', 'Done');
        return redirect()->route('users.index');
    }
    

    public function updateCustomer(Request $request, $id)
    {
        $user = User::find($id);
        $user->first_name = $request->first_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->balance = $request->balance;
        $user->deposit = $request->deposit;
      
        $user->update();
        Toastr::success('Customer has been updated sucessfully', 'Done');
        return redirect()->route('customers.index');
    }

    public function savePayment(Request $request)
    {
        $customer = User::find($request->customer_id);
        $receipt_nos = [];
        $total_amount_paid = 0;

        if ($request->receipt_no == null) {
            return response()->json(['error' => 'No transaction selected'], 400);
        }

        $rowCount = count($request->receipt_no);
        if ($rowCount != null) {
            for ($i = 0; $i < $rowCount; $i++) {

                if ($request->payment_option[$i] == "Full Payment") {
                  
                    if ($request->payment_method == 'deposit') 
                    {
                        if ($customer->deposit < $request->full_payment_payable[$i]) {
                            return response()->json(['error' => 'Customer has insufficient deposit balance'], 400);
                        }
                    }
                  

                    $receiptNo = $request->receipt_no[$i];
                    $sales = DB::table('sales')
                        ->where('receipt_no', $receiptNo)
                        ->get();
                    $total_amount = 0;
                    if ($sales[0]->status) {
                        foreach ($sales as $sale) {
                            $total_amount += $sale->price * $sale->quantity - $sale->discount;
                        }
                        DB::table('sales')
                            ->where('receipt_no', '=', $request->receipt_no[$i])
                            ->update(['status' => 'paid']);

                        if ($request->payment_method == 'deposit') {
                            $customer->deposit -= $request->full_payment_payable[$i];
                            $customer->balance -= $request->full_payment_payable[$i];
                            $customer->update();
                        } else {
                            $customer->balance -= $request->full_payment_payable[$i];
                            $customer->update();
                        }
                        array_push($receipt_nos, $receiptNo);
                        $total_amount_paid += $request->full_payment_payable[$i];

                    } else {
                        DB::table('sales')
                            ->where('receipt_no', '=', $request->receipt_no[$i])
                            ->update(['status' => 'paid']);
                       
                        array_push($receipt_nos, $request->receipt_no[$i]);
                        $total_amount_paid += $request->full_payment_payable[$i];

                        if ($request->payment_method == 'deposit') {
                            $customer->deposit -= $request->full_payment_payable[$i];
                            $customer->balance -= $request->full_payment_payable[$i];
                            $customer->update();
                        } else {
                            $customer->balance -= $request->full_payment_payable[$i];
                            $customer->update();
                        }
                    }

                }
                if ($request->payment_option[$i] == "Partial Payment") {

                    if ($request->payment_method == 'deposit') 
                    {
                        if ($customer->deposit < $request->partial_amount[$i]) {
                            return response()->json(['error' => 'Customer has insufficient deposit balance'], 400);
                        }
                    }

                    try {
                        DB::beginTransaction();

                        $receiptNo = $request->receipt_no[$i];
                        $partialAmount = $request->partial_amount[$i];

                        $sales = DB::table('sales')
                            ->where('receipt_no', $receiptNo)
                            ->get();

                        if ($sales->count() < 1) {
                            return response()->json(['error' => "Sale not found for receipt no: $receiptNo"], 400);
                        }

                        $total_amount = 0;
                        foreach ($sales as $sale) {
                            $total_amount += $sale->quantity * $sale->price - $sale->discount;
                        }

                        $newPaymentAmount = $sales[0]->payment_amount + $partialAmount;

                       
                        if ($newPaymentAmount > $total_amount) {
                            return response()->json(['error' => 'Amount exceeds total for the receipt'], 400);
                        }
                        

                        DB::table('sales')
                            ->where('receipt_no', $receiptNo)
                            ->update([
                                'status' => 'partial',
                                'payment_amount' => $newPaymentAmount,
                            ]);

                        DB::commit();

                        // Success message or redirect
                        if ($request->payment_method == 'deposit') {
                            $customer->deposit -= $request->partial_amount[$i];
                            $customer->balance -= $request->partial_amount[$i];
                            $customer->update();
                        } else {
                            $customer->balance -=  $request->partial_amount[$i];
                            $customer->update();
                        }

                        array_push($receipt_nos, $request->receipt_no[$i]);
                        $total_amount_paid += $request->partial_amount[$i];

                    } catch (Exception $e) {
                        DB::rollback();
                        return response()->json(['error' => 'An error occurred while processing the payment'], 500);

                    }

                }

            }
        }
        $customer->update();

        if ($total_amount_paid != 0) {
            $record = new Payment();
            $record->payment_method = $request->payment_method;
            $record->payment_amount += $total_amount_paid;
            $record->branch_id = auth()->user()->branch_id;
            $record->customer_id = $request->customer_id;
            $record->customer_balance = $customer->balance;
            $record->receipt_nos = implode(',', $receipt_nos);
            $record->user_id = auth()->user()->id;
            $record->save();



           
            // Determine customer name
            $customerName = $request->customer === '0' ? 'Walk-in Customer' : User::find($request->customer_id)->first_name;

            $branchName = auth()->user()->branch->name;

            $notificationMessage = "New Credit Repayment: $customerName paid credit balance of ₦" . number_format($total_amount_paid, 0) . " via $request->payment_method in $branchName Branch.";

            // Send notification to admin
            $admin = User::where('usertype', 'admin')->first();
            $admin->notify(new SalesNotification($notificationMessage));

            return response()->json(['success' => 'Payment recorded successfully'], 200);


        }

        return response()->json(['warning' => 'Sales amount is zero. Nothing recorded'], 200);


    }


    public function saveDeposit(Request $request)
    {
        $record = new Payment();
        $record->payment_method = $request->payment_method;
        $record->payment_amount = $request->amount;
        $record->branch_id = auth()->user()->branch_id;
        $record->customer_id = $request->customer_id;
        $record->user_id = auth()->user()->id;
        $record->payment_type = 'deposit';
        $record->save();

        $user = User::find($request->customer_id);
        $user->deposit += $request->amount;
        $user->save();

        Toastr::success('Deposit has been Recorded sucessfully', 'Done');
        return redirect()->back();

    }

    public function updateDeposit(Request $request)
    {
        // dd($request->all());
        $deposit = Payment::findOrFail($request->depositId);

        $validatedData = $request->validate([
            'payment_amount' => 'required|numeric',
        ]);

        $deposit->payment_amount = $validatedData['payment_amount'];
        $deposit->save();

        return response()->json(['message' => 'Deposit updated successfully']);
    }

    public function loadReceipt(Request $request)
    {
        $payment = Payment::find($request->payment_id);
        $receiptNos = explode(',', $payment->receipt_nos);
    
        $formattedDates = [];
    
        foreach ($receiptNos as $receiptNo) {
            $sale = Sale::where('receipt_no', $receiptNo)->first();
            if ($sale) {
                $formattedDate = date('d F Y', strtotime($sale->created_at));
                $formattedDates[] = $formattedDate;
            }
        }
    
        return response()->json([
            'status' => 200,
            'payment' => $payment,
            'dates' => $formattedDates,
            'balance' => $payment->customer_balance ?? 0,
        ]);
    }

    public function deleteCustomer(Request $request)
    {
        $user = User::find($request->id);
        $user->delete();
        Payment::where('customer_id', $request->id)->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Customer deleted succesfully',
        ]);
    }

    public function returnIndex(Request $request)
    {
        $id = $request->input('id');
        $sale = Sale::where('receipt_no',$id)->where('branch_id', auth()->user()->branch_id)->first();

        $data['sales'] = Sale::select('id', 'stock_id', 'price', 'quantity', 'discount', 'status', 'payment_amount', 'customer', 'returned_qty')
            ->where('receipt_no', $id)
            ->where('branch_id', auth()->user()->branch_id)
            ->where('customer', $sale->customer)
            ->get();

        return view('users.customers.return', $data);

    }
 
    public function returnStore(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $receiptNo = Sale::where('id', $request->sale_id[0])->value('receipt_no');
        $customer = Sale::where('id', $request->sale_id[0])->value('customer');
        
        // Retrieve sales for the given receipt number, customer, and branch
        $sales = Sale::where('receipt_no', $receiptNo)
            ->where('customer', $customer)
            ->where('branch_id', $branchId)
            ->get();
        
        $remainingBalance = $sales->sum(fn($sale) => ($sale->quantity - $sale->returned_qty) * $sale->price - $sale->discount) - ($sales[0]->payment_amount ?? 0);

        // dd($remainingBalance);

        $totalReturnedValue = 0;

        foreach ($request->sale_id as $index => $saleId) {
            $returnedQty = $request->returned_qty[$index];

            if ($returnedQty > 0) {
                $sale = Sale::findOrFail($saleId);
                $discountPerUnit = $sale->discount / $sale->quantity;
                $appliedDiscount = $discountPerUnit * $returnedQty;
                $returnedValue = ($sale->price * $returnedQty) - $appliedDiscount;

                $totalReturnedValue += $returnedValue;
            }
        }
        if ($totalReturnedValue > $remainingBalance) {
            return back()->withErrors(['error' => 'The value of the returned products exceeds the remaining balance.']);
        }
        foreach ($request->sale_id as $index => $saleId) {
            $returnedQty = $request->returned_qty[$index];

            if ($returnedQty > 0) {
                $sale = Sale::findOrFail($saleId);
                $sale->returned_qty += $returnedQty;
                $sale->save();

                $discountPerUnit = $sale->discount / $sale->quantity;
                $appliedDiscount = $discountPerUnit * $returnedQty;

                // Save return details
                Returns::create([
                    'branch_id' => $branchId,
                    'return_no' => $receiptNo,
                    'product_id' => $request->product_id[$index],
                    'price' => $request->price[$index],
                    'quantity' => $returnedQty,
                    'discount' => $appliedDiscount,
                    'cashier_id' => auth()->user()->id,
                    'customer' => $customer,
                    'channel' => 'credit',
                    'note' => $sale->note,
                    'return_channel' => 'profile',
                ]);

                // Update stock
                $product = Stock::findOrFail($request->product_id[$index]);
                $product->quantity += $returnedQty;
                $product->save();

                // Update customer balance
                $user = User::findOrFail($request->customer_id);
                $user->balance -= ($request->price[$index] * $returnedQty - $appliedDiscount);
                $user->save();
            }
        }

        Toastr::success('Credit Sales was Updated Successfully');
        return redirect()->route('customers.profile', $customer);
    }


    public function search(Request $request)
    {
        $searchQuery = $request->input('query');

        $data['customers'] = User::where('usertype', 'customer')
            ->where('branch_id', auth()->user()->branch_id)
            ->where(function ($query) use ($searchQuery) {
                $query->where('first_name', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $searchQuery . '%');
            })
            ->orderBy('first_name')
            ->get();

        return view('users.customers.table', $data)->render();

    }

}
