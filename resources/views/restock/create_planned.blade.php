@extends('layouts.app')
@section('PageTitle', 'Create Planned Restock')

@section('css')
<style>
    .selected-products-table {
        font-size: 0.9rem;
    }
    .selected-products-table th,
    .selected-products-table td {
        padding: 0.5rem;
    }
    .selected-products-table .product-name {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .quantity-input {
        width: 60px;
    }
    .loading-spinner {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
</style>
@endsection

@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Stock Selection</h3>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end mb-3">
                                <div class="col-md-4">
                                    <label for="branch">Branch</label>
                                    <select id="branch" class="form-select">
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="stockLevel">Stock Level</label>
                                    <select id="stockLevel" class="form-select">
                                        <option value="all">All Stocks</option>
                                        <option value="very_high">Very High</option>
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low">Low</option>
                                        <option value="very_low">Very Low</option>
                                        <option value="out_of_stock">Out of Stock</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="search">Search Stock</label>
                                    <input type="text" id="search" class="form-control" placeholder="Search...">
                                </div>
                            </div>
                            <div id="stocksTableContainer" class="position-relative">
                                @include('restock.partials.stocks_table', ['stocks' => $stocks])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Selected Products</h3>
                        </div>
                        <div class="card-body">
                            <form id="restockForm" action="{{ route('restock.store.planned') }}" method="POST">
                                @csrf
                                <input type="hidden" name="branch_id" id="selectedBranch" value="{{ $branches->first()->id }}">
                                <div class="form-group">
                                    <label for="supplier">Supplier</label>
                                    <select name="supplier_id" id="supplier" class="form-select">
                                        <option value="">No Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->first_name }} {{ $supplier->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="selectedProducts"></div>
                                <div id="summary" class="mt-3">
                                    <h4>Summary</h4>
                                    <p>Total Products: <span id="totalProducts">0</span></p>
                                    <p>Total Cost: ₦<span id="totalCost">0.00</span></p>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-3">Submit Restock Order</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let debounceTimer;

    function debounce(func, delay) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(func, delay);
    }

    function showLoadingSpinner() {
        $('#stocksTableContainer').append('<div class="loading-spinner"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
    }

    function hideLoadingSpinner() {
        $('.loading-spinner').remove();
    }

    function updateStocksTable(url) {
        showLoadingSpinner();
        $.ajax({
            url: url || '{{ route("restock.fetch.stocks") }}',
            method: 'GET',
            data: {
                branch_id: $('#branch').val(),
                stock_level: $('#stockLevel').val(),
                search: $('#search').val()
            },
            success: function(response) {
                $('#stocksTableContainer').html(response.stocks);
                hideLoadingSpinner();
                updatePaginationLinks();
            },
            error: function(xhr, status, error) {
                console.error("AJAX error: " + status + ": " + error);
                hideLoadingSpinner();
            }
        });
    }

    function updatePaginationLinks() {
        $('#stocksTableContainer .pagination a').on('click', function(e) {
            e.preventDefault();
            updateStocksTable($(this).attr('href'));
        });
    }

    // Initial setup for pagination links
    updatePaginationLinks();

    $('#branch, #stockLevel').change(function() {
        updateStocksTable();
    });

    $('#search').on('input', function() {
        debounce(updateStocksTable, 300);
    });

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        updateStocksTable($(this).attr('href'));
    });

    let selectedProducts = {};

    $(document).on('change', '.stock-checkbox', function() {
        const stockId = $(this).val();
        const stockName = $(this).data('name');
        const buyingPrice = $(this).data('buying-price');

        if (this.checked) {
            selectedProducts[stockId] = { name: stockName, quantity: 1, price: buyingPrice };
        } else {
            delete selectedProducts[stockId];
        }

        updateSelectedProductsTable();
    });

    function updateSelectedProductsTable() {
        let tableHtml = '<table class="table table-sm selected-products-table"><thead><tr><th>#</th><th>Product</th><th>Qty</th><th></th></tr></thead><tbody>';
        let totalProducts = 0;
        let totalCost = 0;
        let index = 1;

        for (const [stockId, product] of Object.entries(selectedProducts)) {
            tableHtml += `
                <tr>
                    <td>${index}</td>
                    <td class="product-name" title="${product.name}">${product.name}</td>
                    <td>
                        <input type="number" name="stocks[${stockId}][quantity]" value="${product.quantity}" min="1" class="form-control form-control-sm quantity-input" data-stock-id="${stockId}">
                        <input type="hidden" name="stocks[${stockId}][id]" value="${stockId}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-link text-danger remove-product" data-stock-id="${stockId}">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
            totalProducts += product.quantity;
            totalCost += product.quantity * product.price;
            index++;
        }

        tableHtml += '</tbody></table>';
        $('#selectedProducts').html(tableHtml);
        $('#totalProducts').text(totalProducts);
        $('#totalCost').text(totalCost.toFixed(2));
    }

    $(document).on('input', '.quantity-input', function() {
        const stockId = $(this).data('stock-id');
        selectedProducts[stockId].quantity = parseInt($(this).val()) || 0;
        updateSelectedProductsTable();
    });

    $(document).on('click', '.remove-product', function() {
        const stockId = $(this).data('stock-id');
        delete selectedProducts[stockId];
        $(`#stock-${stockId}`).prop('checked', false);
        updateSelectedProductsTable();
    });

    $('#restockForm').submit(function(e) {
        e.preventDefault();
        if (Object.keys(selectedProducts).length === 0) {
            Swal.fire('Error', 'Please select at least one product for restock.', 'error');
            return;
        }

        let isValid = true;
        $('.quantity-input').each(function() {
            if ($(this).val() <= 0) {
                isValid = false;
                return false;
            }
        });

        if (!isValid) {
            Swal.fire('Error', 'Please enter a valid quantity for all selected products.', 'error');
            return;
        }

        this.submit();
    });

    $('#branch').change(function() {
        $('#selectedBranch').val($(this).val());
    });
});
</script>
@endsection