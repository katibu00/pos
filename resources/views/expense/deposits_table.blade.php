@foreach($recentDeposits as $deposit)
<tr>
    <td>{{ $deposit->created_at->format('D, M j, Y \a\t g:i A') }}</td>
    <td>{{ $deposit->branch->name }}</td>
    <td>₦{{ number_format($deposit->amount, 0) }}</td>
    <td>{{ $deposit->note }}</td>
    <td>{{ $deposit->user->first_name }} {{ $deposit->user->last_name }}</td>
</tr>
@endforeach