<!-- resources/views/your/table/view.blade.php -->

<table class="table" id="transferFundsTable">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Description</th>
            <th>From Account</th>
            <th>To Account</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($fundTransfers as $key => $record)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $record->description }}</td>
                <td>{{ $record->from_account }}</td>
                <td>{{ $record->to_account }}</td>
                <td>{{ $record->amount }}</td>
                <td>{{ $record->created_at->format('d M, Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
