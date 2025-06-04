<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>Item Details</th>
                <th>Branch</th>
                <th>Stock Level</th>
                <th>Value</th>
                <th>Last Received</th>
                <th>Last Shipped</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($warehouseItems as $item)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="item-icon me-3">
                                <i class="fas fa-cube fa-lg text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $item->stock->name }}</div>
                                <small class="text-muted">SKU: {{ $item->stock->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-info">{{ $item->stock->branch->name ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <div class="fw-bold" id="quantity-{{ $item->id }}">{{ number_format($item->quantity) }}</div>
                                <small class="text-muted">units</small>
                            </div>
                            @php
                                $stockLevel = 'good';
                                if ($item->quantity <= 10) {
                                    $stockLevel = 'low';
                                } elseif ($item->quantity <= 50) {
                                    $stockLevel = 'medium';
                                }
                            @endphp
                            <span class="status-badge status-{{ $stockLevel }}">
                                @if($stockLevel === 'low')
                                    <i class="fas fa-exclamation-triangle me-1"></i>Low
                                @elseif($stockLevel === 'medium')
                                    <i class="fas fa-exclamation-circle me-1"></i>Medium
                                @else
                                    <i class="fas fa-check-circle me-1"></i>Good
                                @endif
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-success">
                            ${{ number_format($item->quantity * $item->stock->buying_price, 2) }}
                        </div>
                        <small class="text-muted">${{ number_format($item->stock->buying_price, 2) }}/unit</small>
                    </td>
                    <td>
                        @if($item->last_move_in)
                            <div class="text-success">
                                <i class="fas fa-arrow-down me-1"></i>
                                {{ $item->last_move_in->quantity }} units
                            </div>
                            <small class="text-muted">{{ $item->last_move_in->created_at->format('M d, Y') }}</small>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-minus"></i> No records
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($item->last_move_out)
                            <div class="text-danger">
                                <i class="fas fa-arrow-up me-1"></i>
                                {{ $item->last_move_out->quantity }} units
                            </div>
                            <small class="text-muted">{{ $item->last_move_out->created_at->format('M d, Y') }}</small>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-minus"></i> No records
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cog me-1"></i>Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item transfer-to-store-btn" href="#" 
                                       data-id="{{ $item->id }}" 
                                       data-name="{{ $item->stock->name }}" 
                                       data-quantity="{{ $item->quantity }}">
                                        <i class="fas fa-share text-primary me-2"></i>Transfer to Store
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item view-history-btn" href="#" 
                                       data-stock-id="{{ $item->stock_id }}" 
                                       data-warehouse-id="{{ $item->warehouse_id }}">
                                        <i class="fas fa-history text-info me-2"></i>View History
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item adjust-stock-btn" href="#" 
                                       data-id="{{ $item->id }}" 
                                       data-name="{{ $item->stock->name }}" 
                                       data-quantity="{{ $item->quantity }}">
                                        <i class="fas fa-edit text-warning me-2"></i>Adjust Stock
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item view-details-btn" href="#" 
                                       data-id="{{ $item->id }}">
                                        <i class="fas fa-eye text-secondary me-2"></i>View Details
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
                            <h5>No items in warehouse</h5>
                            <p>This warehouse is currently empty. Transfer some items to get started.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<!-- Pagination -->
@if($warehouseItems->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Showing {{ $warehouseItems->firstItem() }} to {{ $warehouseItems->lastItem() }} 
            of {{ $warehouseItems->total() }} results
        </div>
        <div>
            {{ $warehouseItems->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endif