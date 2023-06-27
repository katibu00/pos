@extends('layouts.app')
@section('PageTitle', 'All Sales')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <!-- Default panel contents -->
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <h2 class="text-bold fs-20">All Sales</h2>
                        </div>
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <div class="form-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <div class="form-group">
                                <select class="form-select" id="cashier_id">
                                    <option value="">Sort by Cashier</option>
                                    <option value="all">All</option>
                                   @foreach ($staffs as $staff)
                                   <option value="{{ $staff->id }}">{{ $staff->first_name.' '.$staff->last_name }}</option>
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
                                    <option value="credit">Credit</option>
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
                        @include('sales.receipt')
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection



@section('js')





    <script>
        function PrintReceiptContent(receipt_no) {
            data = {
                'receipt_no': receipt_no,
            }

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
                    $.each(res.items, function(key, item) {

                        html +=
                            '<tr style="text-align: center">' +
                            '<td style="text-align: left"><span style="font-size: 12px;" >' + item
                            .product.name +
                            '</span></td>' +
                            '<td style="font-size: 12px;">' + item.quantity + '</td>' +
                            '<td style="font-size: 12px;">' + item.price.toLocaleString() + '</td>' +
                            '<td style="font-size: 12px;">' + (item.quantity * item.price)
                            .toLocaleString() + '</td>' +
                            '</tr>';
                        total += item.quantity * item.price;
                    });


                    html = $('#receipt_body').html(html);
                    $('.tran_id').html('S' + res.items[0].receipt_no);
                    $('#total').html('₦' + total.toLocaleString());

                    var data = document.getElementById('print').innerHTML;

                    myReceipt = window.open("", "myWin", "left=150, top=130,width=300, height=400");

                    myReceipt.screenX = 0;
                    myReceipt.screenY = 0;
                    myReceipt.document.write(data);
                    myReceipt.document.title = "Print Peceipt";
                    myReceipt.focus();
                    myReceipt.print();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    if (xhr.status === 419) {
                        Command: toastr["error"](
                            "Session expired. please login again."
                        );
                        toastr.options = {
                            closeButton: false,
                            debug: false,
                            newestOnTop: false,
                            progressBar: false,
                            positionClass: "toast-top-right",
                            preventDuplicates: false,
                            onclick: null,
                            showDuration: "300",
                            hideDuration: "1000",
                            timeOut: "5000",
                            extendedTimeOut: "1000",
                            showEasing: "swing",
                            hideEasing: "linear",
                            showMethod: "fadeIn",
                            hideMethod: "fadeOut",
                        };
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
                        $('.table tbody').empty().append('<tr><td colspan="9" class="text-center">No results found.</td></tr>');
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


@endsection
