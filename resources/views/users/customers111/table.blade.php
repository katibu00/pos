<table class="table" style="width:100%">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Phone</th>
            <th scope="col">Credit Balance</th>
            <th scope="col">Deposit Balance</th>
            <th scope="col">Last Sales Date</th>
            <th scope="col">Last Payment Date</th>
            <th scope="col">Last Payment Amount</th>
            <th scope="col">Days Since Last Payment</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customers as $key => $customer)
            <tr>
                <th scope="row">{{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}</th>
                <td>{{ $customer['first_name'] }}</td>
                <td>{{ $customer['phone'] }}</td>
                <td>&#8358;{{ number_format($customer['credit_balance']) }}</td>
                <td>&#8358;{{ number_format($customer['deposit']) }}</td>
                <td>{{ $customer['last_sales_date'] }}</td>
                <td>{{ $customer['last_payment_date'] }}</td>
                <td>{!! $customer['last_payment_amount'] !== 'N/A' ? '&#8358;' . number_format($customer['last_payment_amount']) : 'N/A' !!}</td>
                <td>{{ $customer['days_since_last_payment'] }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ route('customers.profile', $customer['customer_id']) }}"><i class="fa fa-user"></i> Go to Profile</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('customers.edit', $customer['customer_id']) }}"><i class="fa fa-edit"></i> Edit Customer</a>
                            <button class="dropdown-item deleteItem" data-id="{{ $customer['customer_id'] }}" data-name="{{ $customer['first_name'] }}"><i class="fa fa-trash"></i> Delete User</button>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="d-flex justify-content-center">
    {{ $customers->links() }}
</div>
