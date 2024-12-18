@extends('layouts.app')
@section('PageTitle', 'All Sales')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <h2 class="text-bold fs-20">All Sales</h2>
                        </div>
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <div class="form-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search By Customer name or Note">
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
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <select class="form-select" id="transaction_type">
                                    <option value="">Filter by Transaction Type</option>
                                    <option value="all">All</option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="pos">POS</option>
                                    <option value="credit">Credit</option>
                                    <option value="awaiting_pickup">Awaiting Pickup</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-data">
                            @include('sales.all_table')
                        </div>
                    </div>
                </div>

                <div class="modal">
                    <div id="print">
                        @include('transactions.receipt')
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection



@section('js')



    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.min.js"></script>


    <script>
              
              function PrintReceiptContent(receipt_no, transaction_type) {
            var data = {
                'receipt_no': receipt_no,
                'transaction_type': transaction_type,
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('refresh-receipt') }}",
                data: data,
                success: function(res) {
                    var html = '';
                    var total = 0;
                    var paidAmount = res.paid_amount || 0;

                    $.each(res.items, function(key, item) {
                        var productName = item.product.name;
                        var quantity = item.quantity;
                        var price = item.price;
                        var totalPrice = quantity * price;

                        html += '<tr style="text-align: center">' +
                            '<td style="text-align: left"><span style="font-size: 12px;">' +
                            productName + '</span></td>' +
                            '<td style="font-size: 12px;">' + quantity + '</td>' +
                            '<td style="font-size: 12px;">' + price.toLocaleString() + '</td>' +
                            '<td style="font-size: 12px;">' + totalPrice.toLocaleString() + '</td>' +
                            '</tr>';
                        total += totalPrice;
                    });

                    var laborCost = res.items[0].labor_cost ? parseInt(res.items[0].labor_cost) : 0;
                    if (laborCost) {
                        var subTotal = total;
                        total += laborCost;

                        html += '<tr style="text-align: center">' +
                            '<td></td>' +
                            '<td colspan="2"><b>Sub-total</b></td>' +
                            '<td><b>&#8358;' + subTotal.toLocaleString() + '</b></td>' +
                            '</tr>';

                        html += '<tr style="text-align: center">' +
                            '<td></td>' +
                            '<td colspan="2"><b>Labor Cost</b></td>' +
                            '<td><b>&#8358;' + laborCost.toLocaleString() + '</b></td>' +
                            '</tr>';
                    }

                    html += '<tr style="text-align: center">' +
                        '<td></td>' +
                        '<td colspan="2"><b>Total Amount</b></td>' +
                        '<td><b>&#8358;' + total.toLocaleString() + '</b></td>' +
                        '</tr>';

                    if (paidAmount > 0) {
                        var balance = total - paidAmount;

                        html += '<tr style="text-align: center">' +
                            '<td></td>' +
                            '<td colspan="2"><b>Amount Paid</b></td>' +
                            '<td><b>&#8358;' + paidAmount.toLocaleString() + '</b></td>' +
                            '</tr>';

                        html += '<tr style="text-align: center">' +
                            '<td></td>' +
                            '<td colspan="2"><b>Balance</b></td>' +
                            '<td><b>&#8358;' + balance.toLocaleString() + '</b></td>' +
                            '</tr>';
                    }

                    $('#receipt_body').html(html);
                    $('#transaction_type_span').html('<u>' + transaction_type + ' Receipt</u>');
                    $('#transaction_date_span').text(res.transaction_date);

                    var printableContent = document.getElementById('print').innerHTML;

                    var printWindow = window.open("", "myWin", "left=150, top=130, width=300, height=400");
                    printWindow.document.write(printableContent);
                    printWindow.document.title = "Print Estimate Certificate";
                    printWindow.focus();
                    printWindow.print();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    if (xhr.status === 419) {
                        toastr.error("Session expired. Please login again.");
                        setTimeout(function() {
                            window.location.replace('{{ route('login') }}');
                        }, 2000);
                    }
                },
            });
        }
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
                url: '{{ route('sales.all.search') }}',
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
            $('#cashier_id, #transaction_type').on('change', function() {

                var cashierId = $('#cashier_id').val();
                var transactionType = $('#transaction_type').val();
                $.LoadingOverlay("show")
                $('.pagination').hide();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route('sales.all.sort') }}',
                    method: 'POST',
                    data: {
                        cashier_id: cashierId,
                        transaction_type: transactionType
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
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>



    <script>
        function confirmDeliver(receiptNo) {
            Swal.fire({
                title: "Confirm Delivery",
                text: 'Are you sure you want to mark sale ' + receiptNo + ' as Delivered?',
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, items Delivered!",
                cancelButtonText: "Cancel",
                dangerMode: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    markAsDeivered(receiptNo);
                }
            });
        }

        function markAsDeivered(receiptNo) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('sales.deliver') }}',
                method: 'POST',
                data: {
                    receiptNo: receiptNo
                },
                success: function(response) {
                    // Handle the success response from the backend
                    Swal.fire('Success', response.message);
                    $('.table').load(location.href + ' .table');

                },
                error: function(xhr, status, error) {
                    // Handle the error response from the backend
                    Swal.fire('Error', 'Failed to confirm pickup. Please try again.', 'error');
                }
            });
        }
    </script>

    <script>
        function confirmPickup(receiptNo) {
            Swal.fire({
                title: "Confirm Awaiting Pickup",
                text: 'Are you sure you want to mark sale ' + receiptNo + ' as Awaiting Pickup?',
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, items Not received!",
                cancelButtonText: "Cancel",
                dangerMode: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    sendReceiptNumber(receiptNo);
                }
            });
        }

        function sendReceiptNumber(receiptNo) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('sales.awaiting_pickup') }}',
                method: 'POST',
                data: {
                    receiptNo: receiptNo
                },
                success: function(response) {
                    // Handle the success response from the backend
                    Swal.fire('Success', 'Pickup confirmed for receipt number ' + receiptNo, 'success');
                    $('.table').load(location.href + ' .table');

                },
                error: function(xhr, status, error) {
                    // Handle the error response from the backend
                    Swal.fire('Error', 'Failed to confirm pickup. Please try again.', 'error');
                }
            });
        }
    </script>




@endsection
