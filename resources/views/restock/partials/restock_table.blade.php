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
            <td>₦{{ number_format($restock->total_cost, 2) }}</td>
            <td>{{ $restock->created_at->format('Y-m-d H:i') }}</td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $restock->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $restock->id }}">
                        <li><a class="dropdown-item view-details" href="#" data-restock-id="{{ $restock->id }}">
                            <i class="fas fa-eye"></i> View Details
                        </a></li>
                        <li><a class="dropdown-item manage-expenses" href="#" data-restock-id="{{ $restock->id }}">
                            <i class="fas fa-money-bill-alt"></i> Manage Expenses
                        </a></li>
                        <li><a class="dropdown-item manage-damages" href="#" data-restock-id="{{ $restock->id }}">
                            <i class="fas fa-exclamation-triangle"></i> Manage Damages
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('restock.complete.form', $restock->id) }}">
                            <i class="fas fa-check-circle"></i> Complete
                        </a></li>
                        <li><a class="dropdown-item download-pdf" href="#" data-restock-id="{{ $restock->id }}">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a></li>
                        @if($restock->status == 'pending' && $restock->type == 'planned')
                        <li>
                            <a class="dropdown-item" href="{{ route('restock.edit.planned', $restock->id) }}">
                                <i class="fas fa-edit"></i> Edit Restock
                            </a>
                        </li>
                        @endif
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