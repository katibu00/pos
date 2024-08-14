<table class="table table-striped">
    <thead>
        <tr>
            <th>Restock Number</th>
            <th>Type</th>
            <th>Status</th>
            <th>Supplier</th>
            <th>Total Cost</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($restocks as $restock)
        <tr>
            <td>{{ $restock->restock_number }}</td>
            <td>{{ ucfirst($restock->type) }}</td>
            <td>{{ ucfirst($restock->status) }}</td>
            <td>{{ $restock->supplier->name ?? 'N/A' }}</td>
            <td>${{ number_format($restock->total_cost, 2) }}</td>
            <td>{{ $restock->created_at->format('Y-m-d H:i') }}</td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $restock->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $restock->id }}">
                        <li><a class="dropdown-item" href="#">View Details</a></li>
                        <li><a class="dropdown-item" href="#">Edit</a></li>
                        <li><a class="dropdown-item" href="#">Delete</a></li>
                    </ul>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="d-flex justify-content-center">
    {{ $restocks->links() }}
</div>
