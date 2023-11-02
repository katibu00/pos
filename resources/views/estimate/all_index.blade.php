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
                                <input type="text" class="form-control" id="searchInput" placeholder="Search by Estimate ID or Note">
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
