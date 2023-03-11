@extends('layouts.app')
@section('PageTitle', 'All Estimates')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <!-- Default panel contents -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-2 d-none d-md-block"><span class="text-bold fs-16">Estimates</span></div>
                    </div>
                    <div class="card-body">
                        <div class="table-data">
                            @include('estimate.all_table')
                        </div>
                    </div>
                </div>

                <div class="modal">
                    <div id="print">
                        @include('estimate.receipt')
                    </div>
                </div>

            </div>
        </div>
    </section><!-- #content end -->

@endsection

@section('js')


    <script>
        //search
        $(document).on('keyup', function(e) {
            e.preventDefault();

            let query = $('#search').val();
            let branch_id = $('#branch').val();

            if (branch_id == '') {
                alert('please choose a branch');
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('search-stocks') }}",
                method: 'POST',
                data: {
                    'query': query,
                    'branch_id': branch_id
                },

                success: function(response) {
                    $('.table-data').html(response);
                    $('.table').removeClass('d-none');

                    if (response.status == 404) {
                        $('.table-data').html(
                            '<p class="text-danger text-center">No Data Matched the Query</p>');
                    }
                }
            });

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

                        html +=
                            '<tr style="text-align: center">' +
                            '<td style="font-size: 12px;">' + (key + 1) + '</td>' +
                            '<td style="text-align: left"><span style="font-size: 12px;" >' + item
                            .product.name + '</span></td>' +
                            '<td style="font-size: 12px;">' + item.quantity + '</td>' +
                            '<td style="font-size: 12px;">' + item.quantity * item.price + '</td>' +
                            '</tr>';
                        total += item.quantity * item.price;
                    });
                    html +=
                        '<tr style="text-align: center">' +
                        '<td></td>' +
                        '<td colspan="2"><b>Total Amount</b></td>' +
                        '<td><b>&#8358;' + total.toLocaleString() + '</b></td>' +
                        '</tr>';

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
        $(document).on('click', '.delete', function(e) {
            e.preventDefault();

            let id = $(this).data('id');

            swal({
                    title: "Delete Product?",
                    text: "Once deleted, you will not be able to recover it!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {

                        var data = {
                            'id': id,
                        }

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "POST",
                            url: "{{ route('stock.delete') }}",
                            data: data,
                            dataType: "json",
                            success: function(response) {

                                $('.table').load(location.href + ' .table');


                            }
                        });

                    }
                });
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
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@endsection
