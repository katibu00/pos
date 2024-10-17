@extends('layouts.app')
@section('PageTitle', 'Create Direct Restock')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <h1 class="mb-4">Create Direct Restock</h1>

            <!-- Success Message -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif


            <div class="row mb-4">
                <div class="col-md-4">
                    <select class="form-select form-select-lg" id="branch" name="branch_id" required>
                        <option value="">Select a branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control form-control-lg" id="stockSearch" placeholder="Search stocks..." style="opacity: 0.6;" disabled>
                </div>
            </div>

            <div id="branchWarning" class="alert alert-warning d-none" role="alert">
                Please select a branch before searching for stocks.
            </div>

            <div class="row">
                <div class="col-md-9">
                    <div id="stockSuggestions" class="mb-3"></div>

                    <form id="directRestockForm" action="{{ route('restock.store.direct') }}" method="POST">
                        @csrf
                        <input type="hidden" name="branch_id" id="selectedBranchId">
                        <table class="table table-bordered table-hover" id="restockTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Stock Name</th>
                                    <th>Current Quantity</th>
                                    <th>Current Buying Price (₦)</th>
                                    <th>Current Selling Price (₦)</th>
                                    <th>Restock Quantity</th>
                                    <th>New Buying Price (₦)</th>
                                    <th>New Selling Price (₦)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Restock items will be added here dynamically -->
                            </tbody>
                        </table>

                        <button type="submit" class="btn btn-primary btn-lg mt-3">Create Restock</button>
                    </form>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Restock Summary</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Total Items
                                    <span id="totalItems" class="badge bg-primary rounded-pill">0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Total Quantity
                                    <span id="totalQuantity" class="badge bg-info rounded-pill">0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Total Cost
                                    <span id="totalCost" class="badge bg-success rounded-pill">₦0.00</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Items with Price Change
                                    <span id="priceChangedItems" class="badge bg-warning rounded-pill">0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Total Price Increase
                                    <span id="totalPriceIncrease" class="badge bg-danger rounded-pill">₦0.00</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .stock-suggestion {
        cursor: pointer;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    .stock-suggestion:hover {
        background-color: #f8f9fa;
    }
    .price-changed {
        background-color: #fff3cd;
    }
    #stockSearch::placeholder {
        opacity: 0.4;
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    let debounceTimer;

    // Enable stock search after selecting branch
    $('#branch').change(function() {
        const branchId = $(this).val();
        $('#selectedBranchId').val(branchId);
        $('#stockSearch').prop('disabled', !branchId);
    });

    // Handle stock search with debounce
    $('#stockSearch').on('input', function() {
        clearTimeout(debounceTimer);
        const searchTerm = $(this).val();
        const branchId = $('#branch').val();

        if (!branchId) {
            $('#branchWarning').removeClass('d-none');
            return;
        }

        $('#branchWarning').addClass('d-none');

        if (searchTerm.length < 2) {
            $('#stockSuggestions').empty();
            return;
        }

        debounceTimer = setTimeout(function() {
            $.ajax({
                url: '{{ route("restock.search.stocks") }}',
                method: 'GET',
                data: { term: searchTerm, branch_id: branchId },
                success: function(response) {
                    displayStockSuggestions(response);
                }
            });
        }, 300);
    });

    // Display stock suggestions
    function displayStockSuggestions(stocks) {
        const suggestions = stocks.map(stock => `
            <div class="stock-suggestion" data-stock='${JSON.stringify(stock).replace(/'/g, "&apos;")}'>
                ${stock.name} (Quantity: ${stock.quantity})
            </div>
        `).join('');

        $('#stockSuggestions').html(suggestions);
    }

    // Add stock to the table
    $(document).on('click', '.stock-suggestion', function() {
        const stockData = $(this).attr('data-stock');
        const stock = JSON.parse(stockData);
        addStockToTable(stock);
        $('#stockSearch').val('');
        $('#stockSuggestions').empty();
    });

   // Modify the addStockToTable function
   function addStockToTable(stock) {
    const row = `
        <tr data-stock-id="${stock.id}">
            <td>${stock.name}</td>
            <td>${stock.quantity}</td>
            <td class="original-buying-price">${formatCurrency(stock.buying_price)}</td>
            <td class="original-selling-price">${formatCurrency(stock.selling_price)}</td>
            <td>
                <input type="number" name="restock_quantity[]" 
                       class="form-control restock-quantity" 
                       min="1" required>
            </td>
            <td>
                <input type="number" 
                       name="new_buying_price[]" 
                       class="form-control new-buying-price" 
                       step="0.01" 
                       value="${stock.buying_price}" 
                       data-original="${stock.buying_price}"
                       required>
            </td>
            <td>
                <input type="number" 
                       name="new_selling_price[]" 
                       class="form-control new-selling-price" 
                       step="0.01" 
                       value="${stock.selling_price}" 
                       data-original="${stock.selling_price}"
                       required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-stock">Remove</button>
                <input type="hidden" name="stock_id[]" value="${stock.id}">
            </td>
        </tr>
    `;
    $('#restockTable tbody').append(row);
    updateSummary();
}

// Update the input change handler
$(document).on('input', '.new-buying-price, .new-selling-price', function() {
    const row = $(this).closest('tr');
    updateRowStyle(row);
    updateSummary();
});

// Update row style based on price changes
function updateRowStyle(row) {
    const originalBuyingPrice = parseFloat(row.find('.original-buying-price').text().replace('₦', '').replace(',', ''));
    const originalSellingPrice = parseFloat(row.find('.original-selling-price').text().replace('₦', '').replace(',', ''));
    const newBuyingPrice = parseFloat(row.find('.new-buying-price').val());
    const newSellingPrice = parseFloat(row.find('.new-selling-price').val());

    if (newBuyingPrice !== originalBuyingPrice || newSellingPrice !== originalSellingPrice) {
        row.addClass('price-changed');
    } else {
        row.removeClass('price-changed');
    }
}

// Update summary statistics
function updateSummary() {
    let totalItems = $('#restockTable tbody tr').length;
    let totalQuantity = 0;
    let totalCost = 0;
    let priceChangedItems = 0;
    let totalPriceIncrease = 0;

    $('#restockTable tbody tr').each(function() {
        const quantity = parseInt($(this).find('.restock-quantity').val()) || 0;
        const originalBuyingPrice = parseFloat($(this).find('.original-buying-price').text().replace('₦', '').replace(',', ''));
        const newBuyingPrice = parseFloat($(this).find('.new-buying-price').val());
        
        totalQuantity += quantity;
        totalCost += quantity * newBuyingPrice;

        if (newBuyingPrice !== originalBuyingPrice) {
            priceChangedItems++;
            totalPriceIncrease += (newBuyingPrice - originalBuyingPrice) * quantity;
        }
    });

    $('#totalItems').text(totalItems);
    $('#totalQuantity').text(totalQuantity);
    $('#totalCost').text(formatCurrency(totalCost));
    $('#priceChangedItems').text(priceChangedItems);
    $('#totalPriceIncrease').text(formatCurrency(totalPriceIncrease));
}

// Add this new function to handle checkbox changes
function updatePriceChangeStatus(checkbox, type) {
    const row = $(checkbox).closest('tr');
    const priceInput = row.find(type === 'buying' ? '.new-buying-price' : '.new-selling-price');
    const hiddenInput = row.find(`input[name="price_change_${type}[]"]`);
    
    hiddenInput.val(checkbox.checked ? '1' : '0');
    priceInput.prop('readonly', !checkbox.checked);
    
    if (!checkbox.checked) {
        const originalPrice = parseFloat(row.find(
            type === 'buying' ? 'td:eq(2)' : 'td:eq(3)'
        ).text().replace('₦', '').replace(',', ''));
        priceInput.val(originalPrice);
    }
    
    updateRowStyle(row);
    updateSummary();
}

    // Enable or disable price inputs based on checkbox state
    $(document).on('click', '.price-change-checkbox', function() {
        const input = $(this).closest('.input-group').find('input[type="number"]');
        input.prop('disabled', !this.checked);
        input.prop('required', this.checked);
        updateRowStyle($(this).closest('tr'));
        updateSummary();
    });

    // Remove stock from the table
    $(document).on('click', '.remove-stock', function() {
        $(this).closest('tr').remove();
        updateSummary();
    });

    // Update row styles and summaries based on input changes
    $(document).on('input', '.restock-quantity, .new-buying-price, .new-selling-price', function() {
        updateRowStyle($(this).closest('tr'));
        updateSummary();
    });

  

    

    // Format currency values consistently
    function formatCurrency(amount) {
        amount = parseFloat(amount) || 0; // Ensure the amount is a number
        return '₦' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

});
</script>
@endsection
