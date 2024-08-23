<table class="table">
    <thead>
        <tr>
            <th class="sortable" data-sort="created_at">
                Date
                <span class="sort-icon {{ request('sort') == 'created_at' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}"></span>
            </th>
            <th class="sortable" data-sort="batch_number">
                Batch Number
                <span class="sort-icon {{ request('sort') == 'batch_number' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}"></span>
            </th>
            <th class="sortable" data-sort="type">
                Type
                <span class="sort-icon {{ request('sort') == 'type' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}"></span>
            </th>
            <th class="sortable" data-sort="source">
                Source
                <span class="sort-icon {{ request('sort') == 'source' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}"></span>
            </th>
            <th class="sortable" data-sort="total_quantity">
                Total Quantity
                <span class="sort-icon {{ request('sort') == 'total_quantity' ? (request('direction') == 'asc' ? 'asc' : 'desc') : '' }}"></span>
            </th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $transaction->batch_number }}</td>
                <td>{{ ucfirst($transaction->type) }}</td>
                <td>{{ ucfirst($transaction->source) }}</td>
                <td>{{ $transaction->total_quantity }}</td>
                <td>
                    <button class="btn btn-sm btn-info view-details" data-batch="{{ $transaction->batch_number }}">
                        View Details
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $transactions->links() }}