@extends('layouts.app')
@section('PageTitle', 'Inventory Transfer')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1"><i class="fas fa-exchange-alt text-primary me-2"></i>Inventory Transfer</h2>
                            <p class="text-muted mb-0">Transfer items between stores and warehouses</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary" id="resetTransfer">
                                <i class="fas fa-redo me-1"></i>Reset
                            </button>
                            <button class="btn btn-success" id="saveTransfer" disabled>
                                <i class="fas fa-save me-1"></i>Complete Transfer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transfer Configuration -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <!-- Source Selection -->
                                <div class="col-md-5">
                                    <div class="text-center mb-3">
                                        <div class="transfer-section-icon">
                                            <i class="fas fa-arrow-circle-right fa-2x text-primary"></i>
                                        </div>
                                        <h5 class="mt-2">Transfer From</h5>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label">Source Type</label>
                                        <select id="sourceType" class="form-select">
                                            <option value="">Select Source Type</option>
                                            <option value="store">Store Branch</option>
                                            <option value="warehouse">Warehouse</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3" id="sourceSelection" style="display: none;">
                                        <label class="form-label" id="sourceLabel">Select Source</label>
                                        <select id="sourceId" class="form-select">
                                            <option value="">Select...</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Arrow -->
                                <div class="col-md-2 d-flex align-items-center justify-content-center">
                                    <div class="transfer-arrow">
                                        <i class="fas fa-arrow-right fa-3x text-success"></i>
                                    </div>
                                </div>

                                <!-- Destination Selection -->
                                <div class="col-md-5">
                                    <div class="text-center mb-3">
                                        <div class="transfer-section-icon">
                                            <i class="fas fa-arrow-circle-left fa-2x text-success"></i>
                                        </div>
                                        <h5 class="mt-2">Transfer To</h5>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label">Destination Type</label>
                                        <select id="destinationType" class="form-select">
                                            <option value="">Select Destination Type</option>
                                            <option value="store">Store Branch</option>
                                            <option value="warehouse">Warehouse</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3" id="destinationSelection" style="display: none;">
                                        <label class="form-label" id="destinationLabel">Select Destination</label>
                                        <select id="destinationId" class="form-select">
                                            <option value="">Select...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transfer Items -->
            <div class="row" id="transferContent" style="display: none;">
                <!-- Available Items -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-boxes text-primary me-2"></i>Available Items
                                </h5>
                                <span class="badge bg-primary" id="availableCount">0 items</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Search -->
                            <div class="form-group mb-3">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" id="searchItems" class="form-control" placeholder="Search items...">
                                </div>
                            </div>

                            <!-- Items List -->
                            <div id="availableItems" class="items-container">
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
                                    <p>Configure transfer settings to view available items</p>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div id="itemsPagination" class="d-flex justify-content-center mt-3" style="display: none;">
                                <!-- Pagination will be inserted here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Items -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-shopping-cart text-success me-2"></i>Selected Items
                                </h5>
                                <span class="badge bg-success" id="selectedCount">0 items</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="selectedItems" class="items-container">
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-cart-plus fa-3x mb-3 opacity-50"></i>
                                    <p>No items selected for transfer</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .transfer-section-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(0, 123, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .transfer-arrow {
        padding: 20px;
        border-radius: 50%;
        background: rgba(40, 167, 69, 0.1);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .items-container {
        max-height: 500px;
        overflow-y: auto;
    }

    .item-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .item-card:hover {
        border-color: #007bff;
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .item-card.selected {
        border-color: #28a745;
        background-color: #d4edda;
    }

    .item-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .item-name {
        font-weight: 600;
        color: #2c3e50;
    }

    .item-quantity {
        font-size: 0.9em;
        color: #6c757d;
    }

    .quantity-badge {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8em;
        font-weight: 600;
    }

    .selected-item {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 8px;
        background: white;
    }

    .quantity-input {
        width: 80px;
        padding: 4px 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        text-align: center;
    }

    .remove-btn {
        background: none;
        border: none;
        color: #dc3545;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .remove-btn:hover {
        background-color: #f8d7da;
    }

    .pagination {
        margin: 0;
    }

    .pagination .page-link {
        border: none;
        color: #007bff;
        padding: 8px 12px;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    .card {
        border-radius: 12px;
    }

    .form-select, .form-control {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        padding: 12px;
    }

    .form-select:focus, .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
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
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    // Configuration
    const branches = @json($branches);
    const warehouses = @json($warehouses);
    let selectedItems = [];
    let currentPage = 1;
    let searchTimeout;

    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Event Listeners
    $('#sourceType').on('change', function() {
        handleSourceTypeChange();
    });

    $('#destinationType').on('change', function() {
        handleDestinationTypeChange();
    });

    $('#sourceId, #destinationId').on('change', function() {
        if ($('#sourceId').val() && $('#destinationId').val()) {
            $('#transferContent').show();
            loadAvailableItems();
        } else {
            $('#transferContent').hide();
        }
    });

    $('#searchItems').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadAvailableItems, 300);
    });

    $('#resetTransfer').on('click', function() {
        resetTransfer();
    });

    $('#saveTransfer').on('click', function() {
        saveTransfer();
    });

    // Functions
    function handleSourceTypeChange() {
        const sourceType = $('#sourceType').val();
        const $sourceSelection = $('#sourceSelection');
        const $sourceId = $('#sourceId');
        const $sourceLabel = $('#sourceLabel');

        if (sourceType) {
            $sourceSelection.show();
            $sourceId.empty().append('<option value="">Select...</option>');

            if (sourceType === 'store') {
                $sourceLabel.text('Select Branch');
                branches.forEach(branch => {
                    $sourceId.append(`<option value="${branch.id}">${branch.name}</option>`);
                });
            } else {
                $sourceLabel.text('Select Warehouse');
                warehouses.forEach(warehouse => {
                    $sourceId.append(`<option value="${warehouse.id}">${warehouse.name}</option>`);
                });
            }
        } else {
            $sourceSelection.hide();
        }

        // Prevent same source and destination
        updateDestinationOptions();
    }

    function handleDestinationTypeChange() {
        const destinationType = $('#destinationType').val();
        const $destinationSelection = $('#destinationSelection');
        const $destinationId = $('#destinationId');
        const $destinationLabel = $('#destinationLabel');

        if (destinationType) {
            $destinationSelection.show();
            $destinationId.empty().append('<option value="">Select...</option>');

            if (destinationType === 'store') {
                $destinationLabel.text('Select Branch');
                branches.forEach(branch => {
                    $destinationId.append(`<option value="${branch.id}">${branch.name}</option>`);
                });
            } else {
                $destinationLabel.text('Select Warehouse');
                warehouses.forEach(warehouse => {
                    $destinationId.append(`<option value="${warehouse.id}">${warehouse.name}</option>`);
                });
            }
        } else {
            $destinationSelection.hide();
        }
    }

    function updateDestinationOptions() {
        const sourceType = $('#sourceType').val();
        const destinationType = $('#destinationType').val();

        // Prevent same type selection
        if (sourceType === destinationType) {
            $('#destinationType').val('');
            $('#destinationSelection').hide();
        }
    }

    function loadAvailableItems(page = 1) {
        const sourceType = $('#sourceType').val();
        const sourceId = $('#sourceId').val();
        const search = $('#searchItems').val();

        if (!sourceType || !sourceId) return;

        const $container = $('#availableItems');
        $container.html('<div class="text-center py-3"><div class="loading-spinner"></div></div>');

        $.ajax({
            url: "{{ route('admin.warehouse.search-items') }}",
            method: 'GET',
            data: {
                source: sourceType,
                source_id: sourceId,
                query: search,
                page: page
            },
            success: function(response) {
                displayAvailableItems(response.items);
                displayPagination(response.pagination);
                $('#availableCount').text(`${response.pagination.total} items`);
            },
            error: function(xhr) {
                $container.html('<div class="text-center text-danger py-3">Error loading items</div>');
            }
        });
    }

    function displayAvailableItems(items) {
        const $container = $('#availableItems');
        
        if (items.length === 0) {
            $container.html('<div class="text-center text-muted py-5"><i class="fas fa-box-open fa-3x mb-3 opacity-50"></i><p>No items found</p></div>');
            return;
        }

        let html = '';
        items.forEach(item => {
            const isSelected = selectedItems.find(selected => selected.id === item.id);
            const itemName = item.name || item.stock?.name;
            const itemQuantity = item.quantity;

            html += `
                <div class="item-card ${isSelected ? 'selected' : ''}" data-id="${item.id}" data-name="${itemName}" data-quantity="${itemQuantity}">
                    <div class="item-info">
                        <div>
                            <div class="item-name">${itemName}</div>
                            <div class="item-quantity">Available: ${itemQuantity}</div>
                        </div>
                        <div class="quantity-badge">${itemQuantity}</div>
                    </div>
                </div>
            `;
        });

        $container.html(html);

        // Add click handlers
        $('.item-card').on('click', function() {
            if (!$(this).hasClass('selected')) {
                addItemToSelection($(this));
            }
        });
    }

    function displayPagination(pagination) {
        const $pagination = $('#itemsPagination');
        
        if (pagination.last_page <= 1) {
            $pagination.hide();
            return;
        }

        let html = '<nav><ul class="pagination pagination-sm">';
        
        // Previous button
        if (pagination.current_page > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a></li>`;
        }

        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === pagination.current_page) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            }
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a></li>`;
        }

        html += '</ul></nav>';
        $pagination.html(html).show();

        // Add click handlers
        $('.page-link').on('click', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page) {
                loadAvailableItems(page);
            }
        });
    }

    function addItemToSelection($itemCard) {
        const item = {
            id: $itemCard.data('id'),
            name: $itemCard.data('name'),
            available: $itemCard.data('quantity'),
            quantity: 1
        };

        selectedItems.push(item);
        $itemCard.addClass('selected');
        updateSelectedItemsDisplay();
        updateSaveButton();
    }

    function removeItemFromSelection(itemId) {
        selectedItems = selectedItems.filter(item => item.id !== itemId);
        $(`.item-card[data-id="${itemId}"]`).removeClass('selected');
        updateSelectedItemsDisplay();
        updateSaveButton();
    }

    function updateSelectedItemsDisplay() {
        const $container = $('#selectedItems');
        
        if (selectedItems.length === 0) {
            $container.html('<div class="text-center text-muted py-5"><i class="fas fa-cart-plus fa-3x mb-3 opacity-50"></i><p>No items selected for transfer</p></div>');
            $('#selectedCount').text('0 items');
            return;
        }

        let html = '';
        selectedItems.forEach(item => {
            html += `
                <div class="selected-item" data-id="${item.id}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <div class="fw-bold">${item.name}</div>
                            <small class="text-muted">Available: ${item.available}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" class="quantity-input" value="${item.quantity}" 
                                   min="1" max="${item.available}" data-id="${item.id}">
                            <button class="remove-btn" data-id="${item.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        $container.html(html);
        $('#selectedCount').text(`${selectedItems.length} items`);

        // Add event handlers
        $('.quantity-input').on('change', function() {
            const itemId = $(this).data('id');
            const quantity = parseInt($(this).val());
            const item = selectedItems.find(item => item.id === itemId);
            if (item) {
                item.quantity = quantity;
            }
        });

        $('.remove-btn').on('click', function() {
            const itemId = $(this).data('id');
            removeItemFromSelection(itemId);
        });
    }

    function updateSaveButton() {
        const $saveBtn = $('#saveTransfer');
        if (selectedItems.length > 0) {
            $saveBtn.prop('disabled', false);
        } else {
            $saveBtn.prop('disabled', true);
        }
    }

    function resetTransfer() {
        selectedItems = [];
        currentPage = 1;
        $('#sourceType').val('');
        $('#destinationType').val('');
        $('#sourceSelection').hide();
        $('#destinationSelection').hide();
        $('#transferContent').hide();
        $('#searchItems').val('');
        updateSelectedItemsDisplay();
        updateSaveButton();
    }

    function saveTransfer() {
        const sourceType = $('#sourceType').val();
        const sourceId = $('#sourceId').val();
        const destinationType = $('#destinationType').val();
        const destinationId = $('#destinationId').val();

        if (!sourceType || !sourceId || !destinationType || !destinationId) {
            Swal.fire('Error', 'Please complete all transfer settings', 'error');
            return;
        }

        if (selectedItems.length === 0) {
            Swal.fire('Error', 'Please select items to transfer', 'error');
            return;
        }

        // Validate quantities
        for (let item of selectedItems) {
            if (item.quantity <= 0 || item.quantity > item.available) {
                Swal.fire('Error', `Invalid quantity for ${item.name}`, 'error');
                return;
            }
        }

        // Show confirmation
        Swal.fire({
            title: 'Confirm Transfer',
            text: `Transfer ${selectedItems.length} item(s) from ${sourceType} to ${destinationType}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Yes, Transfer!'
        }).then((result) => {
            if (result.isConfirmed) {
                executeTransfer();
            }
        });
    }

    function executeTransfer() {
        const $saveBtn = $('#saveTransfer');
        const originalText = $saveBtn.html();
        $saveBtn.html('<div class="loading-spinner me-2"></div>Processing...').prop('disabled', true);

        const transferData = {
            source_type: $('#sourceType').val(),
            source_id: $('#sourceId').val(),
            destination_type: $('#destinationType').val(),
            destination_id: $('#destinationId').val(),
            items: selectedItems.map(item => ({
                id: item.id,
                quantity: item.quantity
            }))
        };

        $.ajax({
            url: "{{ route('admin.warehouse.transfer.post') }}",
            method: 'POST',
            data: transferData,
            success: function(response) {
                Swal.fire({
                    title: 'Success!',
                    text: response.message,
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    resetTransfer();
                });
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred during transfer';
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }

                Swal.fire('Transfer Failed', errorMessage, 'error');
            },
            complete: function() {
                $saveBtn.html(originalText).prop('disabled', false);
            }
        });
    }
});
</script>
@endsection