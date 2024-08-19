@extends('layouts.app')
@section('PageTitle', 'Create Stock Transfer')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <h1>Create Stock Transfer</h1>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="from_branch">From Branch:</label>
                    <select id="from_branch" class="form-control" required>
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="to_branch">To Branch:</label>
                    <select id="to_branch" class="form-control" required>
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="search">Search Stock:</label>
                    <input type="text" id="search" class="form-control" placeholder="Enter stock name" disabled>
                </div>
            </div>
            <div id="search_results" class="mb-3" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <h4>From Branch Stocks</h4>
                        <ul id="from_stocks" class="list-group"></ul>
                    </div>
                    <div class="col-md-6">
                        <h4>To Branch Stocks</h4>
                        <ul id="to_stocks" class="list-group"></ul>
                    </div>
                </div>
            </div>
            <h3>Added Products</h3>
            <table id="added_products" class="table table-striped">
                <thead>
                    <tr>
                        <th>From Stock</th>
                        <th>To Stock</th>
                        <th>From Quantity</th>
                        <th>To Quantity</th>
                        <th>Transfer Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <button id="submit_transfer" class="btn btn-primary">Submit Transfer</button>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
$(document).ready(function() {
    let selectedFromBranch, selectedToBranch;

    $('#from_branch, #to_branch').change(function() {
        selectedFromBranch = $('#from_branch').val();
        selectedToBranch = $('#to_branch').val();
        if (selectedFromBranch && selectedToBranch && selectedFromBranch !== selectedToBranch) {
            $('#search').prop('disabled', false);
        } else {
            $('#search').prop('disabled', true);
        }
    });

    $('#search').on('input', function() {
        let keyword = $(this).val();
        if (keyword.length >= 3) {
            $.ajax({
                url: '{{ route("stock-transfers.search") }}',
                method: 'GET',
                data: {
                    from_branch_id: selectedFromBranch,
                    to_branch_id: selectedToBranch,
                    keyword: keyword,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    displaySearchResults(response);
                }
            });
        } else {
            $('#search_results').hide();
        }
    });

    function displaySearchResults(response) {
        $('#from_stocks').empty();
        $('#to_stocks').empty();
        response.fromStocks.forEach(function(stock) {
            $('#from_stocks').append(`<li class="list-group-item from-stock" data-id="${stock.id}" data-name="${stock.name}" data-quantity="${stock.quantity}">${stock.name} (Available: ${stock.quantity})</li>`);
        });
        response.toStocks.forEach(function(stock) {
            $('#to_stocks').append(`<li class="list-group-item to-stock" data-id="${stock.id}" data-name="${stock.name}" data-quantity="${stock.quantity}">${stock.name} (Available: ${stock.quantity})</li>`);
        });
        $('#search_results').show();
    }

    let selectedFromStock, selectedToStock;

    $(document).on('click', '.from-stock', function() {
        $('.from-stock').removeClass('active');
        $(this).addClass('active');
        selectedFromStock = $(this).data();
        checkAndAddProduct();
    });

    $(document).on('click', '.to-stock', function() {
        $('.to-stock').removeClass('active');
        $(this).addClass('active');
        selectedToStock = $(this).data();
        checkAndAddProduct();
    });

    function checkAndAddProduct() {
        if (selectedFromStock && selectedToStock) {
            addProductToTable();
            resetSelection();
        }
    }

    function addProductToTable() {
        let rowHtml = `
            <tr class="${$('#added_products tbody tr').length % 2 === 0 ? 'table-light' : 'table-secondary'}" 
                data-from-stock-id="${selectedFromStock.id}" 
                data-to-stock-id="${selectedToStock.id}">
                <td>${selectedFromStock.name}</td>
                <td>${selectedToStock.name}</td>
                <td>${selectedFromStock.quantity}</td>
                <td>${selectedToStock.quantity}</td>
                <td><input type="number" class="form-control transfer-quantity" min="1" max="${selectedFromStock.quantity}"></td>
                <td><button class="btn btn-danger btn-sm remove-product">Remove</button></td>
            </tr>
        `;
        $('#added_products tbody').append(rowHtml);
    }

    function resetSelection() {
        selectedFromStock = null;
        selectedToStock = null;
        $('.from-stock, .to-stock').removeClass('active');
        $('#search').val('');
        $('#search_results').hide();
    }

    $(document).on('click', '.remove-product', function() {
        $(this).closest('tr').remove();
        updateRowColors();
    });

    function updateRowColors() {
        $('#added_products tbody tr').each(function(index) {
            $(this).removeClass('table-light table-secondary').addClass(index % 2 === 0 ? 'table-light' : 'table-secondary');
        });
    }

    $('#submit_transfer').click(function() {
        let transfers = [];
        $('#added_products tbody tr').each(function() {
            let row = $(this);
            let quantity = row.find('.transfer-quantity').val();
            if (quantity && quantity > 0) {
                transfers.push({
                    from_stock_id: row.data('fromStockId'),
                    to_stock_id: row.data('toStockId'),
                    quantity: parseInt(quantity)
                });
            }
        });

        if (transfers.length > 0) {
            $.ajax({
                url: '{{ route("stock-transfers.store") }}',
                method: 'POST',
                data: {
                    from_branch_id: selectedFromBranch,
                    to_branch_id: selectedToBranch,
                    transfers: transfers,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Transfer successful');
                    window.location.href = '{{ route("stock-transfers.index") }}';
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        } else {
            alert('Please add products and specify transfer quantities.');
        }
    });
});
</script>
@endsection