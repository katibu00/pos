@if($transactions->count() > 0)
<table class="table table-hover mb-0">
    <thead>
        <tr>
            <th>Date & Time</th>
            <th>Batch Number</th>
            <th>Type</th>
            <th>Item Details</th>
            <th>Warehouse</th>
            <th>Branch</th>
            <th>Quantity</th>
            <th>Source</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
        <tr class="transaction-row" data-batch="{{ $transaction->batch_number }}">
            <td>
                <div class="timestamp">
                    <strong>{{ $transaction->created_at->format('M d, Y') }}</strong><br>
                    <small class="text-muted">{{ $transaction->created_at->format('h:i A') }}</small>
                </div>
            </td>
            <td>
                <span class="batch-number">{{ $transaction->batch_number }}</span>
            </td>
            <td>
                <span class="type-badge type-{{ $transaction->type }}">
                    @if($transaction->type == 'in')
                        <i class="fas fa-arrow-down me-1"></i>Move In
                    @else
                        <i class="fas fa-arrow-up me-1"></i>Move Out
                    @endif
                </span>
            </td>
            <td>
                <div class="item-name">{{ $transaction->stock->name ?? 'N/A' }}</div>
                @if($transaction->stock && $transaction->stock->sku)
                    <small class="text-muted">SKU: {{ $transaction->stock->sku }}</small>
                @endif
            </td>
            <td>
                <div class="warehouse-name">
                    <i class="fas fa-warehouse me-1"></i>
                    {{ $transaction->warehouse->name ?? 'N/A' }}
                </div>
            </td>
            <td>
                <span class="branch-name">
                    <i class="fas fa-building me-1"></i>
                    {{ $transaction->stock->branch->name ?? 'N/A' }}
                </span>
            </td>
            <td>
                <span class="quantity-display {{ $transaction->type == 'in' ? 'text-success' : 'text-danger' }}">
                    {{ $transaction->type == 'in' ? '+' : '-' }}{{ number_format($transaction->quantity) }}
                </span>
            </td>
            <td>
                @if($transaction->source)
                    <span class="source-badge">{{ ucfirst(str_replace('_', ' ', $transaction->source)) }}</span>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
            <td>
                <button class="btn btn-outline-primary btn-sm view-details" 
                        data-batch="{{ $transaction->batch_number }}"
                        title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="no-data">
    <i class="fas fa-inbox"></i>
    <h5>No Transactions Found</h5>
    <p class="text-muted">No transactions match your current filters. Try adjusting your search criteria.</p>
    <button class="btn btn-outline-primary" id="clearFiltersBtn">
        <i class="fas fa-filter me-1"></i>Clear Filters
    </button>
</div>
@endif