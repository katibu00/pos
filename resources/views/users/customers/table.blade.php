<div class="table-responsive">
    <table class="table" style="width:100%">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Phone</th>
                <th scope="col">Credit Bal.</th>
                <th scope="col">Deposit Bal.</th>
                <th scope="col">Last Credit Payment</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
    
        <tbody>
            @foreach ($customers as $key => $user)
                <tr>
                    <th scope="row">{{ $key + 1 }}</th>
                    <td>{{ $user->first_name }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>&#8358;{{ number_format($user->balance) }}</td>
                    @php
                        $payment = App\Models\Payment::select('created_at', 'payment_amount')
                            ->where('payment_type', 'credit')
                            ->where('customer_id', $user->id)
                            ->latest()
                            ->first();
                       
                    @endphp
                    <td>&#8358;{{ number_format($user->deposit) }}</td>
                    <td>{!! @$payment
                        ? '&#8358;' . number_format($payment->payment_amount, 0) . ', ' . $payment->created_at->diffForHumans()
                        : ' - ' !!}</td>
                   <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ route('customers.profile', $user->id) }}"><i class="fa fa-user"></i> Go to Profile</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('customers.edit', $user->id) }}"><i class="fa fa-edit"></i> Edit Customer</a>
                            <button class="dropdown-item deleteItem" data-id="{{ $user->id }}" data-name="{{ $user->first_name }}"><i class="fa fa-trash"></i> Delete User</button>
                        </div>
                    </div>
                </td>
                
                </tr>
            @endforeach

        </tbody>

    </table>
</div>