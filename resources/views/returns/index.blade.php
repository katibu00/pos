@extends('layouts.app')
@section('PageTitle', 'Return a Sale')
@section('css')
    <style>
        .radio-item input[type="radio"]::before {
            position: relative;
            margin: 4px -25px -4px 0;
            display: inline-block;
            visibility: visible;
            width: 20px;
            height: 20px;
            border-radius: 10px;
            border: 2px inset rgba(150, 150, 150, 0.7);
            background: radial-gradient(ellipse at top)
        }

        @media (max-width: 767px) {
            table.table thead {
                display: none;
            }

            table.table tbody td {
                display: block;
                width: 100%;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }

            table.table tbody td:before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
            }
        }

        .select2-container {
            width: 100% !important;
            font-family: Arial, sans-serif;
        }

        .select2-selection--single {
            height: 38px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 6px 12px !important;
            background-color: #fff !important;
        }

        .select2-selection__arrow {
            height: 36px !important;
            width: 36px !important;
            top: 1px !important;
        }

        .select2-selection__rendered {
            line-height: 24px !important;
        }

        .select2-results__option {
            padding: 8px 12px !important;
        }

        .select2-results__option--highlighted {
            background-color: #e0e0e0 !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            text-align: left;
        }

        ::placeholder {
            visibility: hidden;
        }

        @media (max-width: 767px) {
            ::placeholder {
                visibility: visible;
            }
        }

        .button-group {
            white-space: nowrap;
        }

        .button-group a {
            display: inline-block;
        }
    </style>

@endsection
@section('content')

    <section id="content">
        <div class="content-wraap mt-3">
            <div class="container clearfix">
                <form id="salesForm">
                    <div class="row mb-4">
                        <div class="col-md-8 mb-4">
                            <div class="card mb-2">
                                <div class="card-header bg-transparent">
                                    <marquee behavior="" direction="" class="text-danger"><b>Welcome to El-Habib Plumbing
                                            Material and Services Ltd - {{ auth()->user()->branch->name }} Branch</b>
                                    </marquee>
                                </div>
                                <div class="card-body sales-table">
                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center">
                                            <thead>
                                                <tr>
                                                    <th style="width: 2%"></th>
                                                    <th style="width: 30%">Product</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Discount</th>
                                                    <th>Amount</th>
                                                    <th>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="addMoreRow">

                                                <tr>
                                                    <td>1</td>
                                                    <td>

                                                        <select class="form-select product_id" id="product_id"
                                                            name="product_id[]" required>
                                                            <option value="none"></option>
                                                            @foreach ($products as $product)
                                                                <option data-price="{{ $product->selling_price }}"
                                                                    data-quantity="{{ $product->quantity }}"
                                                                    value="{{ $product->id }}">{{ $product->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" class="product_qty" value="">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="quantity[]" step="0.5"
                                                            id="quantity" class="form-control quantity" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" readonly name="price[]" id="price"
                                                            class="form-control price">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="discount[]" id="discount"
                                                            class="form-control discount">
                                                    </td>
                                                    <td>
                                                        <input type="number" readonly name="total_amount[]"
                                                            id="total_amount" class="form-control total_amount">
                                                    </td>
                                                    <td class="button-group">
                                                        <a href="#"
                                                            class="btn mx-1 btn-danger btn-sm remove_row rounded-circle"><i
                                                                class="fa fa-times-circle"></i></a>
                                                        <a href="#"
                                                            class="btn btn-success btn-sm add_row rounded-circle"><i
                                                                class="fa fa-plus"></i></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @include('returns.recent_sales_table')
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header bg-transparent">
                                    <p>Total: <b class="total"> 0.00 </b></p>
                                </div>

                                <input type="hidden" id="total_hidden">
                                <div class="card-body">
                                    <div class="panel">
                                        <div class="row">
                                            <table class="table table-striped">
                                                <tr>
                                                    <td>
                                                        <label for="">Customer Name</label>
                                                        <input type="text" name="customer_name" id=""
                                                            class="form-control">
                                                    </td>
                                                    <td>
                                                        <label for="">Note</label>
                                                        <input type="text" name="note" id=""
                                                            class="form-control">
                                                    </td>
                                                </tr>
                                            </table>

                                            <td>
                                                <div class="col-12 form-group">
                                                    <label>Payment Method:</label><br>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input required" type="radio"
                                                            name="payment_method" id="cash" value="cash" required>
                                                        <label class="form-check-label nott" for="cash"><i
                                                                class="fa fa-money text-success"></i> Cash</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="payment_method"
                                                            id="pos" value="pos" required>
                                                        <label class="form-check-label nott" for="pos"><i
                                                                class="fa fa-credit-card text-info"></i> POS</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="payment_method"
                                                            id="transfer" value="transfer" required>
                                                        <label class="form-check-label nott" for="transfer"><i
                                                                class="fa fa-credit-card text-info"></i> Transfer</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="submit" id="submitBtn"
                                                    class="btn btn-info text-white btn-lg btn-block mt-2">Save
                                                    Return</button>
                                            </td>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal">
                    <div id="print">
                        @include('returns.receipt')
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $('.product_id').select2();

        $('.sales-table').on('click', '.add_row', function() {
            var product = $('.product_id').html();
            var numberofrow = ($('.addMoreRow tr').length - 0) + 1;
            var tr = '<tr><td class="no">' + numberofrow + '</td>' +
                '<td><select class="form-select product_id" name="product_id[]" required>' + product +
                '</select><input type="hidden" class="product_qty" value=""></td>' +
                '<td><input type="number" name="quantity[]" placeholder="Qty" step="any" class="form-control quantity" required></td>' +
                '<td><input type="number" readonly name="price[]" class="form-control price"></td>' +
                '<td><input type="number" name="discount[]" placeholder="Dicount" class="form-control discount"></td>' +
                '<td><input type="number" readonly name="total_amount[]" class="form-control total_amount"></td>' +
                '<td class="button-group"><a class="btn btn-danger btn-sm mx-1 remove_row rounded-circle"><i class="fa fa-times-circle"></i></a> <a href="#" class="btn btn-success btn-sm add_row rounded-circle"><i class="fa fa-plus"></i></a></td></tr>';
            $('.addMoreRow').append(tr);
            $('.product_id').select2();
        });


        $('.addMoreRow').delegate('.remove_row', 'click', function() {
            $(this).parent().parent().remove();
        });

        $('.product_id').select2();

        function TotalAmount() {
            var total = 0;
            $('.total_amount').each(function(i, e) {
                var amount = $(this).val() - 0;
                total += amount;
            });
            $('.total').html('&#8358;' + total.toLocaleString());
            $('#total_hidden').val(total);
        }

        $('.addMoreRow').delegate('.product_id', 'change', function() {
            var tr = $(this).parent().parent();
            var price = tr.find('.product_id option:selected').attr('data-price');
            var quantity = tr.find('.product_id option:selected').attr('data-quantity');
            tr.find('.price').val(price);
            var qty = tr.find('.quantity').val() - 0;

            var disc = tr.find('.discount').val() - 0;
            var price = tr.find('.price').val() - 0;
            var total_amount = (qty * price) - ((qty * price * disc) / 100);
            tr.find('.total_amount').val(total_amount);
            tr.find('.product_qty').val(quantity);
            TotalAmount();
        });

        $('.addMoreRow').delegate('.quantity, .discount', 'keyup', function() {
            var tr = $(this).parent().parent();
            var qty = tr.find('.quantity').val() - 0;
            var product_qty = tr.find('.product_qty').val() - 0;
            var disc = tr.find('.discount').val() - 0;
            var price = tr.find('.price').val() - 0;
            var total_amount = (qty * price - disc);
            tr.find('.total_amount').val(total_amount);
            TotalAmount();
        });

        function PrintReceiptContent(return_no) {
            data = {
                'return_no': return_no,
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('refresh-receipt-return') }}",
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
                    html +=
                        '<tr style="text-align: center">' +
                        '<td></td>' +
                        '<td colspan="2"><b>Total Amount</b></td>' +
                        '<td><b>&#8358;' + total.toLocaleString() + '</b></td>' +
                        '</tr>';

                    html = $('#receipt_body').html(html);
                    $('.tran_id').html('R' + res.items[0].return_no);

                    var data = document.getElementById('print').innerHTML;

                    myReceipt = window.open("", "myWin", "left=150, top=130,width=300, height=400");

                    myReceipt.screenX = 0;
                    myReceipt.screenY = 0;
                    myReceipt.document.write(data);
                    myReceipt.document.title = "Print Return Certificate";
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

    <script>
        $(document).ready(function() {

            $(document).on('submit', '#salesForm', function(e) {
                e.preventDefault();
                let formData = new FormData($('#salesForm')[0]);
                $.LoadingOverlay("show");
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "{{ route('returns.record') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {

                        if (res.status == 201) {
                            $.LoadingOverlay("hide");
                            $('#salesForm')[0].reset();
                            $('.addMoreRow tr:not(:first)').remove();
                            $(".product_id").val('none').trigger('change');
                            updateTable();
                        }
                        if (res.status == 400) {
                            Command: toastr["error"](res.message);

                            $.LoadingOverlay("hide");
                        }
                    }
                })
            });


            //update table
            function updateTable() {

                data = {}
                $(".recent-table").LoadingOverlay("show");
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "{{ route('refresh-table-return') }}",
                    data: data,
                    success: function(res) {

                        $('.recent').load(location.href + ' .recent');
                        $(".recent-table").LoadingOverlay("hide");

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
            }


        });
    </script>
@endsection
