<div class="table-responsive">
    <table class="table" style="width:100%">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Credit Balance</th>
                <th scope="col">Last Payment</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($debtors as $debtor)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $debtor->customer->first_name }}</td>
                    @php
                        $total_amount = App\Models\CashCredit::where('customer_id',$debtor->customer_id)->sum('amount');
                    @endphp
                    <td>{{ $total_amount }}</td>
                    <td>
                      
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item credits-history" href="#" data-customer-id="{{ $debtor->customer->id }}">Credits History</a>
                                <div class="dropdown-divider"></div>
                                <button class="dropdown-item">Credit Payment</button>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
        

    </table>
</div>