@extends('layouts.app')
@section('PageTitle', 'All Sales')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <!-- Default panel contents -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-2 d-none d-md-block"><span class="text-bold fs-16">All Sales</span></div>
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
                            '<td style="font-size: 12px;">' + (key + 1) + '</td>' +
                            '<td style="text-align: left"><span style="font-size: 12px;" >' + item
                            .product.name +
                            '</span></td>' +
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
                    $('.tran_id').html('S' + res.items[0].receipt_no);

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
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@endsection
