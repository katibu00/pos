@extends('layouts.app')
@section('PageTitle', 'Record a Credit Sale')

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




    <!-- ============ Body content start ============= -->
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
                                    <div class="table-responsive container">
                                        <table class="table table-bordered text-center">
                                            <thead>
                                                <tr>
                                                    <th style="width: 2%"></th>
                                                    <th style="width: 30%">Product</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Discount</th>
                                                    <th>Amount</th>
                                                    <th> </th>
                                                </tr>
                                            </thead>
                                            <tbody class="addMoreRow">
                                                <tr>
                                                    <td>1</td>
                                                    <td>

                                                        <select class="form-select product_id" id="product_id"
                                                            name="product_id[]" required>
                                                            <option value=""></option>
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
                                                        <input type="number" name="quantity[]" step="any"
                                                            placeholder="Qty" id="quantity" class="form-control quantity"
                                                            required>
                                                    </td>
                                                    <td>
                                                        <input type="number" readonly name="price[]" id="price"
                                                            class="form-control price">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="discount[]" placeholder="Discount"
                                                            id="discount" class="form-control discount">
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
                            @include('sales.credit.recent_sales_table')
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
                                                        <label for="">Customer</label>
                                                        <select class="form-select" name="customer" id="customer" required>
                                                            <option value=""></option>
                                                            @foreach ($customers as $customer)
                                                                <option value="{{ $customer->id }}">
                                                                    {{ $customer->first_name }}</option>
                                                            @endforeach
                                                        </select>
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
                                                            name="payment_method" id="credit" value="credit" required>
                                                        <label class="form-check-label nott" for="credit"><i
                                                                class="fa fa-money text-success"></i> Credit</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="payment_method"
                                                            id="deposit" value="deposit" required>
                                                        <label class="form-check-label nott" for="deposit"><i
                                                                class="fa fa-credit-card text-info"></i> Deposit</label>
                                                    </div>

                                                </div>
                                            </td>
                                            <style>
                                                p {
                                                    margin: 0;
                                                    font-weight: bold;
                                                    font-size: 1.2em;
                                                }

                                                span {
                                                    font-weight: normal;
                                                }
                                            </style>

                                            <td>
                                                <p>Credit Balance: <span id="pre_balance_span">0.00</span></p>
                                                <input type="hidden" name="pre_balance" id="pre_balance"
                                                    class="form-control mb-2" readonly>
                                            </td>
                                            <td>
                                                <p>Deposit Balance: <span id="deposit_bal_span">0.00</span></p>
                                                <input type="hidden" name="deposit_bal" id="deposit_bal"
                                                    class="form-control mb-2" readonly>
                                            </td>
                                            <td>
                                                <p> <span id="balance_txt">New Balance:</span> <span
                                                        id="new_balance_span">0.00</span></p>
                                                <input type="hidden" name="new_balance" id="new_balance"
                                                    class="form-control mb-2" readonly>
                                            </td>

                                            <td>
                                                <button type="submit" id="submitBtn"
                                                    class="btn btn-secondary btn-lg btn-block mt-2">Record Credit
                                                    Sale</button>
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
                        @include('sales.credit.receipt')
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('js')


    <script>
        $(document).ready(function() {


            $("input[name='payment_method']").change(function() {
                
                var total = parseInt($("#total_hidden").val());
                var paymentMethod = $("input[name='payment_method']:checked").val();
                var preBalance = parseInt($("#pre_balance").val());
                var depositBalance = parseInt($("#deposit_bal").val());
                var balanceTxt = '';
                var newBalance = 0;

                if (isNaN(total)) {
                    toastr.error("Please choose a product");
                    $("input[name='payment_method']").prop("checked", false);
                    return;
                }


                if (paymentMethod === "credit") {
                    newBalance = preBalance + total;
                    balanceTxt = "New Credit Balance";
                } else if (paymentMethod === "deposit") {

                    if (depositBalance < total) {
                        toastr.error("Balance not enough. Please try another payment method");
                        $("input[name='payment_method']").prop("checked", false);
                        newBalance = 0;
                        return;
                    }
                    balanceTxt = "New Deposit Balance";
                    newBalance = depositBalance - total;
                }

                $("#new_balance_span").text("₦" + newBalance.toLocaleString());
                $("#balance_txt").text(balanceTxt);
               
            });
        });
    </script>


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

        $('#customer').select2();


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

            if (quantity < 1) {
                Command: toastr["error"](quantity + ' Remaining')

                tr.find('.quantity').val('');
            }


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
            if (qty > product_qty) {
                Command: toastr["error"](product_qty + ' Product Quantity Remaining Only.')

                tr.find('.quantity').val('');

            }
            var disc = tr.find('.discount').val() - 0;
            var price = tr.find('.price').val() - 0;
            var total_amount = (qty * price - disc);
            tr.find('.total_amount').val(total_amount);
            TotalAmount();
            $("input[name='payment_method']").prop("checked", false);

        });



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
                    url: "{{ route('credit.store') }}",
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
                            toastr.success(res.message, "Success", {
                                timeOut: 3000
                            });
                        }
                        if (res.status == 400) {
                            $.LoadingOverlay("hide");

                            toastr.warning(res.message, "Insuffient Balance", {
                                timeOut: 3000
                            });
                        }

                    },
                    error: function(xhr, status, error) {

                        toastr.error("An error occurred: " + error, "Error", {
                            timeOut: 3000
                        });
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
                    url: "{{ route('refresh-table') }}",
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


            //fetch balance
            $(document).on('change', '#customer', function() {

                var customer_id = $('#customer').val();
                $("input[name='payment_method']").prop("checked", false);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: '{{ route('fetch-balance') }}',
                    data: {
                        'customer_id': customer_id
                    },
                    success: function(res) {

                        if (res.status === 200) {
                            $('#pre_balance').val(res.balance);
                            $('#deposit_bal').val(res.deposits);
                            $("#pre_balance_span").text("₦" + res.balance.toLocaleString());
                            $("#deposit_bal_span").text("₦" + Number(res.deposits)
                                .toLocaleString());
                        }

                        if (res.status === 404) {
                            $('#pre_balance').val('');
                            $('#deposit_bal').val('');
                        }
                    }
                });
            });

        });
    </script>

@endsection
