@extends('layouts.app')
@section('PageTitle', 'All Estimates')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <h2 class="text-bold fs-20">All Estimates</h2>
                        </div>
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <div class="form-group">
                                <input type="text" class="form-control" id="searchInput"
                                    placeholder="Search by Estimate ID or Note">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <div class="form-group">
                                <select class="form-select" id="cashier_id">
                                    <option value="">Sort by Cashier</option>
                                    <option value="all">All</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">
                                            {{ $staff->first_name . ' ' . $staff->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="table-data">
                            @include('estimate.all_table')
                        </div>
                    </div>
                </div>

                <div class="modal fade addModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="">Mark As Sold </h4>
                                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"
                                    aria-hidden="true"></button>
                            </div>
                            <form action="{{ route('estimate.all.store') }}" method="POST">
                                @csrf
                                <div class="modal-body">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-1">
                                                <select class="form-select form-select-sm" id="payment_method"
                                                    name="payment_method" required>
                                                    <option value="">--Payment Method--</option>
                                                    <option value="cash">Cash</option>
                                                    <option value="transfer">Transfer</option>
                                                    <option value="pos">POS</option>
                                                    <option value="credit">Credit</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 customer_div d-none">
                                            <div class="mb-1">
                                                <select class="form-select form-select-sm" id="customer" name="customer"
                                                    required>
                                                    <option value="">-- Customer --</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ @$customer->id }}">{{ @$customer->first_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <div class="mb-1">
                                                Estimate ID:<span id="estimate_no_span"></span>
                                            </div>
                                            <div class="mb-1">
                                                Amount Payable:<span id="payable"></span>
                                            </div>
                                            <div class="mb-1">
                                                Customer Name:<span id="name"></span>
                                            </div>
                                            <div class="mb-1">
                                                Note:<span id="note"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="estimate_no" name="estimate_no">
                                    <input type="hidden" id="total_amount" name="total_amount">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary ml-2">Mark as Sold</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="modal">
                    <div id="print">
                        @include('estimate.receipt')
                    </div>
                </div>


                <div class="modal fade" id="editEstimateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Edit Estimate</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Discount</th>
                                            <th>Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="estimateTableBody">
                                        <!-- Rows will be dynamically added here -->
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-success addRow">+ Add More Rows</button>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="updateEstimateBtn">Update Estimate</button>
                                <div>Total Price: <span id="totalPrice">0</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                




            </div>
        </div>
    </section><!-- #content end -->

@endsection

@section('js')


    <script>
       $(document).on('click', '.editEstimate', function() {
    var estimateNo = $(this).data('estimate_no');
    var modal = $('#editEstimateModal');

    // Encode the estimate number
    var encodedEstimateNo = encodeURIComponent(estimateNo);

    // Show loading spinner
    modal.find('.modal-body').html(
        '<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i></div>');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '{{ route('estimate.edit') }}',
        method: 'POST',
        data: {
            encoded_estimate_no: encodedEstimateNo,
        },
        success: function(response) {
            if (response.error) {
                console.error(response.error);
            } else {
                estimates = response.estimates;
                products = response.products;

                var formHtml = '<form id="editEstimateForm">';
                formHtml += '<table class="table">';
                formHtml +=
                    '<thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Discount</th><th>Total Price</th><th>Action</th></tr></thead>';
                formHtml += '<tbody class="modaltbody">';

                for (var i = 0; i < estimates.length; i++) {
                    var estimate = estimates[i];
                    formHtml += '<tr>';
                    formHtml += '<td>' + estimate.product.name +
                        '<input type="hidden" name="product[]" value="' + estimate.product.id +
                        '"><input type="hidden" name="estimate_no" value="' + estimate
                        .estimate_no + '"></td>';
                    formHtml +=
                        '<td><input type="number" class="form-control price-field" name="price[]" value="' +
                        estimate.price + '" readonly></td>';
                    formHtml +=
                        '<td><input type="text" class="form-control" name="quantity[]" value="' +
                        estimate.quantity + '"></td>';
                    formHtml +=
                        '<td><input type="text" class="form-control" name="discount[]" value="' +
                        estimate.discount + '"></td>';
                    formHtml +=
                        '<td><input type="text" class="form-control total-price-field" name="total_price[]" value="' +
                        (estimate.price * estimate.quantity) + '" readonly></td>';
                    formHtml +=
                        '<td><button class="btn btn-danger removeEstimate" data-estimate_id="' +
                        estimate.id + '">X</button></td>';
                    formHtml += '</tr>';
                }

                formHtml += '</tbody></table>';
                formHtml +=
                    '<button type="button" class="btn btn-success addRow">+ Add More Rows</button>';
                formHtml += '</form>';

                modal.find('.modal-body').html(formHtml);
                modal.modal('show');

                $('.product-select').select2();

                $('.addRow').on('click', function() {
                    var newRow = '<tr>';
                    newRow +=
                        '<td><select class="form-select product-select select2" name="product[]">' +
                        '<option value="">Select Product</option>';
                    for (var j = 0; j < products.length; j++) {
                        newRow += '<option value="' + products[j].id +
                            '" data-price="' + products[j].selling_price + '">' +
                            products[j].name + '</option>';
                    }
                    newRow += '</select></td>';
                    newRow +=
                        '<td><input type="text" class="form-control price-field" name="price[]" readonly></td>';
                    newRow +=
                        '<td><input type="text" class="form-control" name="quantity[]"></td>';
                    newRow +=
                        '<td><input type="text" class="form-control" name="discount[]"></td>';
                    newRow +=
                        '<td><input type="text" class="form-control total-price-field" name="total_price[]" readonly></td>';
                    newRow +=
                        '<td><button class="btn btn-danger removeRow">X</button></td>';
                    newRow += '</tr>';

                    $('.modaltbody').append(newRow);

                    $('.removeRow').on('click', function() {
                        $(this).closest('tr').remove();
                        updateTotalPrice();
                    });

                    $('.product-select').on('change', function() {
                        updatePriceField($(this));
                    });

                    $('[name="quantity[]"], [name="discount[]"]').on('input', function() {
                        updateTotalPrice();
                    });
                });

                $('.removeEstimate').on('click', function() {
                    var estimateId = $(this).data('estimate_id');
                    $(this).closest('tr').remove();
                    updateTotalPrice();
                });

                $('[name="quantity[]"], [name="discount[]"]').on('input', function() {
                    updateTotalPrice();
                });

                $('#editEstimateForm').on('submit', function(event) {
                    event.preventDefault();
                    console.log(123);
                });
            }
        },

        error: function(xhr) {
            console.error(xhr.responseText);
        }
    });
});

function updatePriceField(productSelect) {
    var selectedProduct = products.find(product => product.id == productSelect.val());
    if (selectedProduct) {
        productSelect.closest('tr').find('.price-field').val(selectedProduct.selling_price);
        updateTotalPrice();
    }
}

function updateTotalPrice() {
    var total = 0;
    $('.modaltbody tr').each(function() {
        var quantity = parseFloat($(this).find('[name="quantity[]"]').val()) || 0;
        var price = parseFloat($(this).find('.price-field').val()) || 0;
        var discount = parseFloat($(this).find('[name="discount[]"]').val()) || 0;
        var totalPerRow = (price * quantity) - discount;
        total += totalPerRow;
        $(this).find('.total-price-field').val(totalPerRow.toFixed(2));
    });
    $('#totalPrice').text(total.toFixed(2));
}

       

        



        // Add logic to update estimate data when the "Update Estimate" button is clicked
        $(document).on('click', '#updateEstimateBtn', function() {
            var modal = $('#editEstimateModal');
            var formData = modal.find('form').serialize();

            // Use AJAX to send updated data to the server
            $.ajax({
                url: '/estimate/update',
                method: 'POST',
                data: formData,
                success: function(response) {
                    // Handle success response
                    modal.modal('hide');
                    $('.maintable').load(location.href+' .maintable');
                    toastr.success('Estimates updated successfully');
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    // Handle error response
                    var errors = JSON.parse(xhr.responseText);
        
                    if (errors && errors.errors) {
                        var errorMessage = 'Error:';
                        $.each(errors.errors, function(key, value) {
                            errorMessage += '\n' + value;
                        });
                        toastr.error(errorMessage);
                    } else {
                        toastr.error('An error occurred while updating estimates.');
                    }
                }
            });
        });
    </script>



    <script>
        function handleSearch() {
            var query = $('#searchInput').val();

            $('.pagination').hide();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('estimate.all.search') }}',
                method: 'POST',
                data: {
                    query: query
                },
                success: function(response) {
                    // Empty the table
                    $('.table').empty();

                    // Check if the response is empty
                    if ($(response).find('tbody tr').length > 0) {
                        $('.table').html(response);
                    } else {
                        // Display a message if no rows are found
                        $('.table tbody').empty().append(
                            '<tr><td colspan="9" class="text-center">No results found.</td></tr>');
                        toastr.warning('No results found.');
                    }

                },

                error: function(xhr) {
                    // Handle the error response here
                    console.log(xhr.responseText);
                }
            });
        }
        $('#searchInput').on('input', handleSearch);
    </script>



    <script>
        $(document).ready(function() {
            $('#cashier_id').on('change', function() {

                var cashierId = $('#cashier_id').val();
                $.LoadingOverlay("show")
                $('.pagination').hide();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route('estimate.all.sort') }}',
                    method: 'POST',
                    data: {
                        cashier_id: cashierId,
                    },
                    success: function(response) {
                        $('.table').empty();
                        $.LoadingOverlay("hide")
                        $('.table').html(response);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
        $(document).on('change', '#payment_method', function() {

            var payment_method = $('#payment_method').val();

            if (payment_method == 'credit') {
                $('.customer_div').removeClass('d-none');
                $('#customer').attr('required', true);
            } else {
                $('.customer_div').addClass('d-none');
                $('#customer').attr('required', false);
            }

        });
        $(document).on('click', '.saleItem', function(e) {
            e.preventDefault();

            $('#estimate_no').html();
            $('#payable').html();
            $('#customer').html();
            $('#note').html();

            let estimate_no = $(this).data('estimate_no');
            let payable = $(this).data('payable');
            let customer = $(this).data('customer');
            let note = $(this).data('note');
            let total_amount = $(this).data('payable');

            $('#estimate_no_span').html(estimate_no);
            $('#estimate_no').val(estimate_no);
            $('#total_amount').val(total_amount);
            $('#payable').html(payable);
            $('#customer').html(customer);
            $('#note').html(note);


        });
    </script>


    <script>
        function PrintReceiptContent(estimate_no) {

            data = {
                'estimate_no': estimate_no,
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('refresh-receipt-estimate') }}",
                data: data,
                success: function(res) {
                    var html = '';
                    var total = 0;

                    $.each(res.items, function(key, item) {
                        total += item.quantity * item.price;

                        html +=
                            '<tr style="text-align: center">' +
                            '<td style="text-align: left"><span style="font-size: 12px;" >' + item
                            .product.name + '</span></td>' +
                            '<td style="font-size: 12px;">' + item.quantity + '</td>' +
                            '<td style="font-size: 12px;">' + item.price.toLocaleString() + '</td>' +
                            '<td style="font-size: 12px;">' + (item.quantity * item.price)
                            .toLocaleString() + '</td>' +
                            '</tr>';
                    });

                    if (res.items[0].labor_cost !== null) {
                        var laborCost = parseInt(res.items[0]
                            .labor_cost); // Convert labor cost from string to integer

                        html +=
                            '<tr style="text-align: center">' +
                            '<td></td>' +
                            '<td colspan="2"><b>Sub-total</b></td>' +
                            '<td><b>&#8358;' + total.toLocaleString() + '</b></td>' +
                            '</tr>';

                        html +=
                            '<tr style="text-align: center">' +
                            '<td></td>' +
                            '<td colspan="2"><b>Labor Cost</b></td>' +
                            '<td><b>&#8358;' + laborCost.toLocaleString() + '</b></td>' +
                            '</tr>';

                        total += laborCost; // Add labor cost to the total

                        html +=
                            '<tr style="text-align: center">' +
                            '<td></td>' +
                            '<td colspan="2"><b>Total</b></td>' +
                            '<td><b>&#8358;' + total.toLocaleString() + '</b></td>' +
                            '</tr>';

                        html +=
                            '<tr style="text-align: center">' +
                            '<td colspan="4"><i>Labor cost is separate, not related to the above company.</i></td>' +
                            '</tr>';
                    } else {
                        html +=
                            '<tr style="text-align: center">' +
                            '<td></td>' +
                            '<td colspan="2"><b>Total Amount</b></td>' +
                            '<td><b>&#8358;' + total.toLocaleString() + '</b></td>' +
                            '</tr>';
                    }

                    html = $('#receipt_body').html(html);
                    $('.tran_id').html('E' + res.items[0].estimate_no);

                    var data = document.getElementById('print').innerHTML;

                    myReceipt = window.open("", "myWin", "left=150, top=130,width=300, height=400");

                    myReceipt.screenX = 0;
                    myReceipt.screenY = 0;
                    myReceipt.document.write(data);
                    myReceipt.document.title = "Print Estimate Certificate";
                    myReceipt.focus();
                    myReceipt.print();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    if (xhr.status === 419) {
                        Command: toastr["error"](
                            "Session expired. please login again."
                        );

                        setTimeout(() => {
                            window.location.replace('{{ route('login') }}');
                        }, 2000);
                    }
                },
            });


            setTimeout(() => {
                // myReceipt.close();
            }, 8000);
        }
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@endsection
