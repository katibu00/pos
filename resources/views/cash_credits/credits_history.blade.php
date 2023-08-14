<!-- resources/views/partials/credits_history.blade.php -->
<h4>{{ $customer->first_name }} - Credits History</h4>
<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Amount</th>
            <th>Paid</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($creditsHistory as $credit)
        <tr>
            <td>{{ \Carbon\Carbon::parse($credit->created_at)->format('D d F') }}</td>
            <td>{{ number_format($credit->amount, 0) }}</td>
            <td>{{ number_format($credit->amount_paid, 0) }}</td>
            <td>
                @if($credit->amount_paid == 0)
                    <span class="badge bg-danger">No Payment</span>
                @elseif($credit->amount == $credit->amount_paid)
                    <span class="badge bg-success">Settled</span>
                @else
                    <span class="badge bg-warning">Partial</span>
                @endif
            </td>
        </tr>
    @endforeach
    
    </tbody>
</table>
