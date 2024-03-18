@extends('layouts.app')
@section('PageTitle', 'Correct Sales')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <style>
                    .attention-required {
                        border-color: red;
                    }
                </style>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-2 d-none d-md-block"><span class="text-bold fs-16">Correct Sales</span></div>
                        <div class="col-sm-5 col-md-3">
                            <select class="form-select form-select-sm" id="branch_id">
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-5 col-md-3">
                            <select class="form-select form-select-sm" id="stock_id"></select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sales_table"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('js')
    <script>
        // Set CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {
            $('#stock_id').change(function() {
                var stockId = $(this).val();
                if (stockId) {
                    $.ajax({
                        type: 'GET',
                        url: '{{ route('fetch.sales') }}',
                        data: {
                            stock_id: stockId
                        },
                        success: function(response) {
                            var salesTable = '<table class="table">';
                            salesTable +=
                                '<thead><tr><th>Serial Number</th><th>Stock Name</th><th>Price</th><th>Buying Price</th></tr></thead><tbody>';
                            $.each(response.sales, function(index, sale) {
                                var serialNumber = index + 1;
                                var buyingPriceClass = sale.buying_price == 0 ? ' attention-required' : '';
                                salesTable += '<tr>';
                                salesTable += '<td>' + serialNumber + '</td>';
                                salesTable += '<td>' + sale.product.name + '</td>';
                                salesTable += '<td>' + sale.price + '</td>';
                                salesTable += '<td><input type="text" class="form-control buying-price' + buyingPriceClass + '" value="' + sale.buying_price + '" data-sale-id="' + sale.id + '"></td>';
                                salesTable += '</tr>';
                            });
                            salesTable += '</tbody></table>';
                            $('#sales_table').html(salesTable);

                            // Add event listener for input blur to update buying price
                            $('.buying-price').blur(function() {
                                var saleId = $(this).data('sale-id');
                                var newBuyingPrice = $(this).val();
                                updateBuyingPrice(saleId, newBuyingPrice);
                            });
                        }
                    });
                } else {
                    $('#sales_table').html('<p>No sales records found for the selected stock</p>');
                }
            });
        });

        function updateBuyingPrice(saleId, newBuyingPrice) {
            $.ajax({
                type: 'POST',
                url: '{{ route('update.buying_price') }}',
                data: {
                    id: saleId,
                    buying_price: newBuyingPrice
                },
                success: function(response) {
                    if (newBuyingPrice != 0) {
                        $('[data-sale-id="' + saleId + '"]').removeClass('attention-required');
                    }
                    toastr.success('Buying price updated successfully');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }



        $(document).ready(function() {
            $('#branch_id').change(function() {
                var branchId = $(this).val();
                if (branchId) {
                    $.ajax({
                        type: 'GET',
                        url: '{{ route('fetch.stocks') }}',
                        data: {
                            branch_id: branchId
                        },
                        success: function(response) {
                            var options = '<option value="">Select Stock</option>';
                            if (response.stocks.length > 0) {
                                $.each(response.stocks, function(index, value) {
                                    // Add serial numbers before the names
                                    var serialNumber = index + 1;
                                    options += '<option value="' + value.id + '">' +
                                        serialNumber + '. ' + value.name + '</option>';
                                });
                            } else {
                                options += '<option value="">No stocks found</option>';
                            }
                            $('#stock_id').html(options);
                        }
                    });
                } else {
                    $('#stock_id').html('<option value="">Select Branch first</option>');
                }
            });
        });
    </script>


@endsection
