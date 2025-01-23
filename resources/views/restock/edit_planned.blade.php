@extends('layouts.app')
@section('PageTitle', 'Edit Planned Restock')

@section('css')
<style>
    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container-fluid">
            <form id="restockForm" action="{{ route('restock.update.planned', $restock->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">Edit Planned Restock</h3>
                                <div>
                                    <span class="me-3">Restock Number: {{ $restock->restock_number }}</span>
                                    <span>Status: {{ ucfirst($restock->status) }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="supplier" class="form-label">Supplier</label>
                                        <select name="supplier_id" id="supplier" class="form-select">
                                            <option value="">No Supplier</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" 
                                                    {{ $restock->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->first_name }} {{ $supplier->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label for="search" class="form-label">Search Products</label>
                                        <div class="input-group">
                                            <input type="text" id="search" class="form-control" placeholder="Search by product name...">
                                            <input type="hidden" id="branch_id" value="{{ auth()->user()->branch_id }}">
                                            <button type="button" id="searchButton" class="btn btn-primary">Search</button>
                                        </div>
                                        <div id="searchResults" class="mt-2"></div>
                                    </div>
                                </div>

                                <div id="selectedProducts">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead class="thead-light position-sticky top-0 bg-white">
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                    <th>Buying Price</th>
                                                    <th>Total</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="productsBody">
                                                @foreach($restockItems as $item)
                                                <tr data-stock-id="{{ $item->stock_id }}">
                                                    <td>{{ $item->stock->name }}</td>
                                                    <td>
                                                        <input type="number" 
                                                               name="stocks[{{ $item->stock_id }}][quantity]" 
                                                               value="{{ $item->ordered_quantity }}" 
                                                               min="1" 
                                                               class="form-control form-control-sm quantity-input">
                                                        <input type="hidden" 
                                                               name="stocks[{{ $item->stock_id }}][id]" 
                                                               value="{{ $item->stock_id }}">
                                                    </td>
                                                    <td>₦{{ number_format($item->new_buying_price, 2) }}</td>
                                                    <td class="row-total">₦{{ number_format($item->ordered_quantity * $item->new_buying_price, 2) }}</td>
                                                    <td>
                                                        <button type="button" 
                                                                class="btn btn-danger btn-sm remove-product">
                                                            Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-end fw-bold">Total Cost</td>
                                                    <td colspan="2" id="totalCost" class="fw-bold">
                                                        ₦{{ number_format($restock->total_cost, 2) }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <a href="{{ route('restock.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Search products
    $('#search').on('input', function() {
        const searchTerm = $(this).val();
        const branchId = $('#branch_id').val();
        
        if (searchTerm.length >= 3) {
            $.ajax({
                url: '{{ route("restock.search.stocks") }}',
                method: 'GET',
                data: { 
                    term: searchTerm,
                    branch_id: branchId
                },
                success: function(response) {
                    let resultsHtml = '<div class="list-group">';
                    response.forEach(function(stock) {
                        resultsHtml += `
                            <a href="#" class="list-group-item list-group-item-action add-product" 
                               data-stock-id="${stock.id}" 
                               data-name="${stock.name}" 
                               data-price="${stock.buying_price}">
                                ${stock.name} - ₦${parseFloat(stock.buying_price).toFixed(2)}
                            </a>
                        `;
                    });
                    resultsHtml += '</div>';
                    $('#searchResults').html(resultsHtml);
                },
                error: function() {
                    Swal.fire('Error', 'Failed to search products', 'error');
                }
            });
        } else {
            $('#searchResults').empty();
        }
    });

    // Utility function to parse currency and calculate precisely
    function parseCurrency(currencyString) {
        return parseFloat(currencyString.replace('₦', '').replace(/,/g, '')) || 0;
    }

    // Update row and total calculations with precise calculations
    function updateCalculations() {
        let totalCost = 0;
        $('#productsBody tr').each(function() {
            const $row = $(this);
            const quantity = parseInt($row.find('.quantity-input').val()) || 0;
            const price = parseCurrency($row.find('td:nth-child(3)').text());
            
            // Precise row total calculation
            const rowTotal = (quantity * price).toFixed(2);
            $row.find('.row-total').text(`₦${parseFloat(rowTotal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
            
            totalCost += parseFloat(rowTotal);
        });
        
        // Update total cost with precise formatting
        $('#totalCost').text(`₦${totalCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
    }

    // Bind calculation update to quantity input
    $(document).on('input', '.quantity-input', function() {
        updateCalculations();
    });

    // Add product handling
    $(document).on('click', '.add-product', function(e) {
        e.preventDefault();
        const stockId = $(this).data('stock-id');
        const name = $(this).data('name');
        const price = parseFloat($(this).data('price')).toFixed(2);

        if ($(`#productsBody tr[data-stock-id="${stockId}"]`).length > 0) {
            Swal.fire('Warning', 'Product already in the list', 'warning');
            return;
        }

        const newRow = `
            <tr data-stock-id="${stockId}">
                <td>${name}</td>
                <td>
                    <input type="number" 
                        name="stocks[${stockId}][quantity]" 
                        value="1" 
                        min="1" 
                        placeholder="Enter quantity"
                        class="form-control form-control-sm quantity-input">
                    <input type="hidden" 
                        name="stocks[${stockId}][id]" 
                        value="${stockId}">
                </td>
                <td>₦${parseFloat(price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="row-total">₦${parseFloat(price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td>
                    <button type="button" 
                            class="btn btn-danger btn-sm remove-product">
                        Remove
                    </button>
                </td>
            </tr>
        `;

        $('#productsBody').prepend(newRow);
        
        $('#searchResults').empty();
        $('#search').val('');
        
        // Ensure total cost is updated
        updateCalculations();
    });

    // Remove product with SweetAlert confirmation
    $(document).on('click', '.remove-product', function() {
        const row = $(this).closest('tr');
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to remove this product from the restock?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                row.remove();
                updateCalculations();
            }
        });
    });

    // Initial calculations on page load
    updateCalculations();
});
</script>
@endsection