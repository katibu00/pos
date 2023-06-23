@extends('layouts.app')
@section('PageTitle', 'New Reorder')
@section('content')

    <!-- ============ Body content start ============= -->
    <section id="content">
        <div class="content-wraap mt-3">
            <div class="container clearfix">
                <div class="row mb-4">
                    <div class="col-md-7 mb-4">

                        <div class="card mb-2">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="branch">Branch:</label>
                                        <select id="branch" class="form-select">
                                            <option value=""></option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="product-type">Product Types:</label>
                                        <select id="product-type" class="form-select">
                                            <option value=""></option>
                                            <option value="critical">Below Critical Level</option>
                                            <option value="out-of-stock">Out of Stock</option>
                                            <option value="low">Getting Low</option>
                                            <option value="well-stocked">Well Stocked</option>
                                            <option value="all">All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body sales-table">
                                <div class="table-responsive container">
                                    <table class="table table-bordered" id="products-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="text-left">Name</th>
                                                <th>Buying (₦)</th>
                                                <th>Quantity</th>
                                                <th>Critical Level</th>
                                                <th>Percentage</th>
                                                <th>Select</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($stocks as $key => $stock)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td class="text-left">{{ $stock->name }}</td>
                                                    <td>{{ $stock->buying_price }}</td>
                                                    <td>{{ $stock->quantity }}</td>
                                                    <td>{{ $stock->critical_level }}</td>
                                                    <td>{{ ($stock->quantity / $stock->critical_level) * 100 }}%</td>
                                                    <td><input type="checkbox" class="product-checkbox"
                                                            value="{{ $stock->id }}"></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-5 mb-4">
                        <div class="card">
                            <div class="card-body sales-table">
                                <form id="supplier-form">
                                    <input type="hidden" name="bind_branch_id" id="bind_branch_id"/>
                                    <div class="table-responsive container">
                                        <table class="table table-bordered" id="selected-products-table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Product</th>
                                                    <th style="width: 10%">Buying (₦)</th>
                                                    <th style="width: 10%">Quantity</th>
                                                    <th>X</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="total-buying-price" class="mt-3">Total Order Price: ₦0.00</div>
                                    <div class="form-group mt-3">
                                        <label for="supplier">Select Supplier:</label>
                                        <select id="supplier" class="form-select" name="supplier">
                                            <option value=""></option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">
                                                    {{ $supplier->first_name . ' ' . $supplier->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" id="submit-button" class="btn btn-info">Place Order</button>
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

    <script>
        $(document).ready(function() {
           
            $('#supplier-form').submit(function(e) {
                e.preventDefault();

                // Get the form data
                var formData = $(this).serialize();
                var supplierId = $('#supplier').val();
                if (supplierId === '') {
                    showToast('Supplier field is required.');
                    return; 
                }
                if ($('#selected-products-table tbody tr').length === 0) {
                    showToast('No products selected.');
                    return; 
                }
                $('#submit-button').prop('disabled', false).html('<i class="fa fa-spinner fa-spin"></i> Loading...');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route('reorder.store') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        
                        Command: toastr["success"](response.message);

                        $('#submit-button').prop('disabled', false).html('Place Order');
                        $('.product-checkbox').prop('checked', false);
                        $('#supplier-form')[0].reset();
                        $('#selected-products-table tbody').empty();
                        $('#supplier').val('').change();

                    },
                    error: function(xhr, status, error) {
                        // Handle the error response
                        console.log(error);
                    }
                });
            });

            $('#product-type').change(function() {
                var branchId = $('#branch').val();
                $('#bind_branch_id').val(branchId);

                if (!branchId) {
                    showToast('Please select a branch.');
                    return;
                }

                filterProducts();
            });

            // Filter the products based on the selected branch and product type
            function filterProducts() {
                var branchId = $('#branch').val();
                var productType = $('#product-type').val();

                $.LoadingOverlay("show");

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route('fetch-products') }}',
                    method: 'POST',
                    data: {
                        branch_id: branchId,
                        product_type: productType
                    },
                    success: function(response) {
                        // Clear the table body
                        $('#products-table tbody').empty();
                        $.LoadingOverlay("hide");
                        // Append the filtered products to the table
                        response.products.forEach(function(product, index) {
                            var percentage = Math.round((product.quantity / product
                                .critical_level) * 100);
                            var serialNumber = index + 1;
                            var row = '<tr>' +
                                '<td>' + serialNumber + '</td>' +
                                '<td>' + product.name + '</td>' +
                                '<td>' + product.buying_price + '</td>' +
                                '<td>' + product.quantity + '</td>' +
                                '<td>' + product.critical_level + '</td>' +
                                '<td>' + percentage + '%</td>' +
                                '<td><input type="checkbox" class="product-checkbox" value="' +
                                product.id + '"></td>' +
                                '</tr>';
                            $('#products-table tbody').append(row);
                        });

                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                    }
                });
            }

            $(document).on('change', '.product-checkbox', function() {
                if (this.checked) {
                    var row = $(this).closest('tr');
                    var productName = row.find('td:nth-child(2)').text();
                    var buyingPrice = row.find('td:nth-child(3)').text();
                    var productId = $(this).val();

                    // Check if the product already exists in the table
                    var exists = false;
                    $('#selected-products-table tbody tr').each(function() {
                        if ($(this).find('input[name="product_id[]"]').val() == productId) {
                            exists = true;
                            return false;
                        }
                    });

                    if (exists) {
                        showToast('Product already added to the selected products.');
                    } else {
                        var removeButton =
                            '<button class="btn btn-sm btn-danger remove-product"><i class="fa fa-times text-daanger"></i></button>';
                        var newRow = '<tr>' +
                            '<td></td>' +
                            '<td>' + productName + '</td>' +
                            '<td>' + buyingPrice + '</td>' +
                            '<td>' +
                            '<input type="hidden" name="product_id[]" value="' + productId + '">' +
                            '<input type="number" class="form-control product-quantity" name="product_quantity[]" value="1">' +
                            '</td>' +
                            '<td>' +
                            '<input type="hidden" name="buying_price[]" value="' + buyingPrice + '">' +
                            removeButton +
                            '</td>' +
                            '</tr>';
                        $('#selected-products-table tbody').append(newRow);

                        updateSerialNumbers();
                        updateTotalBuyingPrice();
                    }
                } else {
                    var productId = $(this).val();
                    $('#selected-products-table tbody').find('tr').each(function() {
                        if ($(this).find('input[name="product_id[]"]').val() == productId) {
                            $(this).remove();
                            updateSerialNumbers();
                            updateTotalBuyingPrice();
                        }
                    });
                }
            });

            // Update the serial numbers in selected-products-table
            function updateSerialNumbers() {
                $('#selected-products-table tbody tr').each(function(index) {
                    $(this).find('td:first-child').text(index + 1);
                });
            }

            // Show toast message
            function showToast(message) {
                Command: toastr["error"](message);
            }

            $(document).on('click', '.remove-product', function() {
                var row = $(this).closest('tr');
                var productId = row.find('input[name="product_id[]"]').val();

                row.remove();
                updateSerialNumbers();
                updateTotalBuyingPrice();

                // Uncheck the checkbox in products-table
                $('#products-table tbody').find('tr').each(function() {
                    if ($(this).find('.product-checkbox').val() == productId) {
                        $(this).find('.product-checkbox').prop('checked', false);
                    }
                });
            });

            $(document).on('change', '.product-quantity', function() {
                updateTotalBuyingPrice();
            });

            function updateTotalBuyingPrice() {
                var total = 0;
                $('#selected-products-table tbody tr').each(function() {
                    var buyingPrice = parseFloat($(this).find('td:nth-child(3)').text());
                    var quantity = parseInt($(this).find('.product-quantity').val());
                    total += buyingPrice * quantity;
                });

                var formattedTotal = '₦' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","); 

                $('#total-buying-price').text('Total Order Price: ' + formattedTotal);
            }

            
        });
    </script>
@endsection
