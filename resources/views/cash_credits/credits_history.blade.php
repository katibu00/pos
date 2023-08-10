<!-- resources/views/partials/credits_history.blade.php -->
<h4>{{ $customer->first_name }} - Credits History</h4>
<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Amount</th>
            <th>Paid</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($creditsHistory as $credit)
            <tr>
                <td>{{ \Carbon\Carbon::parse($credit->created_at)->format('D d F') }}</td>
                <td>{{ number_format($credit->amount, 0) }}</td>
                <td>{{ number_format($credit->paid, 0) }}</td>
                <td>
                    <button class="btn btn-primary settle-btn" data-credit-id="{{ $credit->id }}" data-toggle="modal" data-target="#paymentModal">Settle</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
