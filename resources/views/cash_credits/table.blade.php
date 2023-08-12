<div class="table-responsive">
    <table class="table" style="width:100%">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Credit Balance</th>
                <th scope="col">Last Full Payment</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($debtors as $debtor)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $debtor->customer->first_name }}</td>
                    @php
                        $total_amount = App\Models\CashCredit::where('customer_id', $debtor->customer_id)
                            ->where(function ($query) {
                                $query
                                    ->whereNull('status') // Rows where status is null
                                    ->orWhere('status', '!=', 'paid'); // Rows where status is not 'paid'
                            })
                            ->sum('amount');
                        $total_paid = App\Models\CashCredit::where('customer_id', $debtor->customer_id)
                            ->where(function ($query) {
                                $query
                                    ->whereNull('status') // Rows where status is null
                                    ->orWhere('status', '!=', 'paid'); // Rows where status is not 'paid'
                            })
                            ->sum('amount_paid');
                        
                     
                        

                        // Get the last payment where status is paid
                        $last_payment = App\Models\CashCredit::where('customer_id', $debtor->customer_id)
                            ->where('status', 'paid')
                            ->orderBy('created_at', 'desc')
                            ->first();
                    @endphp
                    <td>{{ $total_amount - $total_paid }}</td>
                    <td>{{ $last_payment ? $last_payment->amount_paid : 0 }}</td>
                    
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Actions
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item credits-history" href="#"
                                    data-customer-id="{{ $debtor->customer->id }}">Credits History</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item credits-payment" href="#" data-toggle="modal"
                                    data-target="#creditPaymentModal"
                                    data-customer-id="{{ $debtor->customer->id }}">Credit Payment</a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
