@extends('layouts.app')
@section('PageTitle', 'Warehouse Management')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-warehouse text-primary me-2"></i>
                                {{ $warehouse->name ?? 'Warehouse Management' }}
                            </h2>
                            <p class="text-muted mb-0">{{ $warehouse->location ?? 'Manage your warehouse inventory' }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <select id="warehouseSelector" class="form-select" style="width: auto;">
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ $warehouse->id == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                            <a href="{{ route('admin.warehouse.transfer-form') }}" class="btn btn-primary">
                                <i class="fas fa-exchange-alt me-1"></i>Transfer Items
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50 mb-2">Total Items</h6>
                                    <h3 class="mb-0">{{ number_format($stats['total_items']) }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-boxes fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50 mb-2">Total Value</h6>
                                    <h3 class="mb-0">${{ number_format($stats['total_value'], 2) }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100 bg-gradient-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50 mb-2">Unique Products</h6>
                                    <h3 class="mb-0">{{ $stats['unique_products'] }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-cube fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-white-50 mb-2">Today's Movements</h6>
                                    <h3 class="mb-0">{{ $stats['today_movements'] }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-exchange-alt fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0">
                            <h6 class="mb-0">
                                <i class="fas fa-arrow-down text-success me-2"></i>Last Received
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($stats['last_move_in'])
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $stats['last_move_in']->stock->name }}</div>
                                        <small class="text-muted">{{ $stats['last_move_in']->quantity }} items</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $stats['last_move_in']->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @else
                                <div class="text-muted text-center py-3">No items received yet</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0">
                            <h6 class="mb-0">
                                <i class="fas fa-arrow-up text-danger me-2"></i>Last Shipped Out
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($stats['last_move_out'])
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $stats['last_move_out']->stock->name }}</div>
                                        <small class="text-muted">{{ $stats['last_move_out']->quantity }} items</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $stats['last_move_out']->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @else
                                <div class="text-muted text-center py-3">No items shipped yet</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list text-primary me-2"></i>Inventory Items
                                    </h5>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" id="searchInventory" class="form-control" placeholder="Search items...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="inventory-table">
                                @include('warehouse.partials.items_table')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-shipping-fast me-2"></i>Transfer to Store
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Item</label>
                    <input type="text" id="transferItemName" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Available Quantity</label>
                    <input type="text" id="transferAvailable" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity to Transfer</label>
                    <input type="number" id="transferQuantity" class="form-control" min="1" required>
                </div>
                <input type="hidden" id="transferItemId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTransfer">
                    <i class="fas fa-check me-1"></i>Confirm Transfer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .bg-gradient-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
    }
    
    .bg-gradient-success {
        background: linear-gradient(45deg, #28a745, #1e7e34);
    }
    
    .bg-gradient-info {
        background: linear-gradient(45deg, #17a2b8, #117a8b);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(45deg, #ffc107, #e0a800);
    }

    .stat-icon {
        opacity: 0.7;
    }

    .card {
        border-radius: 12px;
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background-color: #f8f9fa;
        border-top: none;
        font-weight: 600;
        color: #495057;
        padding: 15px;
    }

    .table td {
        padding: 15px;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .dropdown-toggle::after {
        display: none;
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .dropdown-item {
        padding: 8px 16px;
        transition: background-color 0.3s;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.75em;
        padding: 6px 12px;
    }

    .btn {
        border-radius: 8px;
        font-weight: 600;
    }

    .btn-sm {
        padding: 4px 12px;
        font-size: 0.8em;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px 0 0 8px;
    }

    .pagination {
        margin: 0;
    }

    .pagination .page-link {
        border: none;
        color: #007bff;
        padding: 8px 16px;
        margin: 0 4px;
        border-radius: 8px;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75em;
        font-weight: 600;
    }

    .status-low {
        background-color: #ffebee;
        color: #c62828;
    }

    .status-medium {
        background-color: #fff3e0;
        color: #ef6c00;
    }

    .status-good {
        background-color: #e8f5e8;
        color: #2e7d32;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    let searchTimeout;

    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Warehouse selector change
    $('#warehouseSelector').on('change', function() {
        const warehouseId = $(this).val();
        if (warehouseId) {
            window.location.href = `{{ route('admin.warehouse.index') }}?warehouse_id=${warehouseId}`;
        }
    });

    // Search functionality
    $('#searchInventory').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val();
        
        searchTimeout = setTimeout(function() {
            loadInventoryItems(1, query);
        }, 300);
    });

    // Pagination handling
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const page = new URL(url).searchParams.get('page');
        const search = $('#searchInventory').val();
        loadInventoryItems(page, search);
    });

    // Transfer modal
    $(document).on('click', '.transfer-btn', function() {
        const itemId = $(this).data('id');
        const itemName = $(this).data('name');
        const quantity = $(this).data('quantity');
        
        $('#transferItemId').val(itemId);
        $('#transferItemName').val(itemName);
        $('#transferAvailable').val(quantity);
        $('#transferQuantity').attr('max', quantity).val(1);
        
        $('#transferModal').modal('show');
    });

    // Save transfer
    $('#saveTransfer').on('click', function() {
        const itemId = $('#transferItemId').val();
        const quantity = $('#transferQuantity').val();
        const available = parseInt($('#transferAvailable').val());

        if (!quantity || quantity <= 0) {
            Swal.fire('Error', 'Please enter a valid quantity', 'error');
            return;
        }

        if (parseInt(quantity) > available) {
            Swal.fire('Error', 'Quantity cannot exceed available stock', 'error');
            return;
        }

        const $btn = $(this);
        const originalText = $btn.html();
        $btn.html('<div class="loading-spinner me-2"></div>Processing...').prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.warehouse.transfer-to-store") }}',
            method: 'POST',
            data: {
                warehouse_item_id: itemId,
                quantity: quantity
            },
            success: function(response) {
                $('#transferModal').modal('hide');
                Swal.fire('Success', response.message, 'success');
                
                // Update the quantity in the table
                const $quantityCell = $(`#quantity-${itemId}`);
                $quantityCell.text(response.new_quantity);
                
                // Update the transfer button data
                $(`.transfer-btn[data-id="${itemId}"]`).data('quantity', response.new_quantity);
                
                // If quantity is 0, hide the row or disable the button
                if (response.new_quantity === 0) {
                    $(`tr:has([data-id="${itemId}"])`).fadeOut();
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred';
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }

                Swal.fire('Transfer Failed', errorMessage, 'error');
            },
            complete: function() {
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Load inventory items
    function loadInventoryItems(page = 1, search = '') {
        const warehouseId = $('#warehouseSelector').val();
        const $table = $('#inventory-table');
        
        // Show loading state
        $table.html('<div class="text-center py-5"><div class="loading-spinner"></div></div>');

        $.ajax({
            url: '{{ route("admin.warehouse.index") }}',
            data: {
                page: page,
                search: search,
                warehouse_id: warehouseId
            },
            success: function(data) {
                $table.html(data);
            },
            error: function() {
                $table.html('<div class="text-center text-danger py-5">Error loading inventory data</div>');
            }
        });
    }
});
</script>
@endsection