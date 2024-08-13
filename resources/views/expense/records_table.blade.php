@foreach($recentExpenses as $expense)
<tr>
    <td>{{ $expense->created_at->format('D, M j, Y \a\t g:i A') }}</td>
    <td>{{ $expense->branch->name }}</td>
    <td>{{ $expense->category->name }}</td>
    <td>₦{{ number_format($expense->amount, 2) }}</td>
    <td>{{ $expense->note }}</td>
    <td>{{ $expense->user->first_name }} {{ $expense->user->last_name }}</td>
</tr>
@endforeach