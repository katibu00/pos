@extends('layouts.app')
@section('PageTitle', 'Users')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <!-- Default panel contents -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">{{ $user->first_name . '\'s Profile' }}</span></div>
                        <div class="col-md-2 float-right"><a href="{{ route('customers.index') }}"
                                class="btn btn-sm btn-primary me-2">
                                <--- Back to customers list</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h4>Shopping History</h4>
                                <div class="table-responsive border">
                                    <table class=" table" style="width:100%; font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Item</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">Total</th>
                                            </tr>
                                        </thead>
                                        @php
                                            $summary_total = 0;
                                        @endphp
                                        <tbody>
                                            @foreach ($dates as $key3 => $date)
                                                @php
                                                    $total_amount = 0;
                                                    $total_discount = 0;
                                                    $sales = App\Models\Sale::select('stock_id', 'price', 'quantity', 'discount', 'status', 'payment_amount')
                                                        ->where('receipt_no', $date->receipt_no)
                                                        ->get();
                                                    $returns = App\Models\Returns::select('product_id', 'price', 'quantity', 'discount', 'payment_method')
                                                        ->where('return_no', 'R'.$date->receipt_no)
                                                        ->get();
                                                @endphp
                                                <tr>
                                                    <td>{{ $key3 + 1 }}</td>
                                                    <td colspan="2">{{ $date->created_at->format('l, d F') }}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td><a href="{{ route('users.return.index', ['id' => $date->receipt_no]) }}" class="btn btn-danger btn-sm"><i class="fa fa-rotate-left text-white"></i></a></td>
                                                </tr>
                                                @foreach ($sales as $key2 => $sale)
                                                    <tr @if ($sale->status == 'partial') class="bg-info text-white" @endif>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ @$sale->product->name }}</td>
                                                        <td>{{ number_format(@$sale->price, 0) }}</td>
                                                        <td>{{ @$sale->quantity }}</td>
                                                        <td>{{ number_format(@$sale->price * @$sale->quantity, 0) }}</td>
                                                    </tr>
                                                    @php
                                                        $total_amount += @$sale->price * @$sale->quantity;
                                                        $total_discount += @$sale->discount;
                                                        $total_return = 0;
                                                        $return_discount = 0;
                                                    @endphp
                                                @endforeach
                                                <tr @if (@$sale->status == 'partial') class="bg-info text-white" @endif>
                                                    <td colspan="3"></td>
                                                    <td colspan="2" class="text-center">Sub Total</td>
                                                    <td>{{ number_format($total_amount, 0) }}</td>
                                                </tr>
                                                <tr @if ($sale->status == 'partial') class="bg-info text-white" @endif>
                                                    <td colspan="3"></td>
                                                    <td colspan="2" class="text-center">Discount</td>
                                                    <td>{{ number_format($total_discount, 0) }}</td>
                                                </tr>
                                                @if($returns->count() > 0)
                                                @foreach ($returns as $return)
                                                    <tr class="bg-danger text-white">
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $return['product']['name'] }}</td>
                                                        <td>{{ number_format($return->price, 0) }}</td>
                                                        <td>{{ $return->quantity }}</td>
                                                        <td>{{ number_format($return->price * $return->quantity, 0) }}</td>
                                                    </tr>
                                                    @php
                                                        $total_return += $return->price * $return->quantity;
                                                        $return_discount += $return->discount ;
                                                    @endphp
                                                @endforeach
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td colspan="2" class="text-right">Total Return</td>
                                                    <td>{{ number_format($total_return, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td colspan="2">Return Discount</td>
                                                    <td>{{ number_format($return_discount, 0) }}</td>
                                                </tr>
                                                @endif
                                                <tr @if ($sale->status == 'partial') class="bg-info text-white" @endif>
                                                    <td colspan="3"></td>
                                                    <td colspan="2" class="text-right"><strong>Net Amount</strong></td>
                                                    @php
                                                        $net_amount = $total_amount -  $total_return - $total_discount + $return_discount;
                                                        if ($sale->status != 'partial') {
                                                            $summary_total += $net_amount;
                                                        }
                                                    @endphp
                                                   
                                                    <td><strong>&#8358;{{ number_format($net_amount, 0) }}</strong></td>
                                                </tr>
                                              
                                                @if ($sale->status == 'partial')
                                                    <tr @if ($sale->status == 'partial') class="bg-info text-white" @endif>
                                                        <td colspan="3"></td>
                                                        <td colspan="2" class="text-center">Amount Paid</td>
                                                        <td>{{ number_format($sale->payment_amount, 0) }}</td>
                                                    </tr>
                                                    <tr @if ($sale->status == 'partial') class="bg-info text-white" @endif>
                                                        <td colspan="3"></td>
                                                        <td colspan="2" class="text-center"><strong>Remaining
                                                                Balance</strong></td>
                                                        @php
                                                            $remaining = $total_amount - $total_discount  - $total_return + $return_discount - $sale->payment_amount;
                                                            $summary_total += $remaining;
                                                        @endphp
                                                        <td><strong>&#8358;{{ number_format($remaining, 0) }}</strong></td>
                                                    </tr>
                                                @endif
                                               
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <h4>Payment History</h4>

                                <div class="table-responsive border">
                                    <table class=" table" style="width:100%; font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Method</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        @forelse ($payments as $key => $payment)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $payment->created_at->diffForHumans() }}</td>
                                                <td>{{ number_format($payment->payment_amount, 0) }}</td>
                                                <td>{{ ucfirst($payment->payment_method) }}</td>
                                                <td>
                                                    <button type="button"
                                                        onclick="PrintReceiptContent('{{ $payment->id }}')"
                                                        class="btn btn-secondary btn-sm"><i
                                                            class="fa fa-print text-white"></i></button>
                                                </td>
                                            </tr>

                                        @empty
                                            <tr>
                                                <td colspan="4" class="bg-danger text-white"> No Records Found</td>
                                            </tr>
                                        @endforelse

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                @php
                                    $deposits = App\Models\Payment::where('customer_id',$user->id)->where('payment_type','deposit')->latest()->get();
                                    $total_deposit = $deposits->sum('payment_amount');
                                @endphp

                                @if($total_deposit > 1)
                                <h4 class="mt-2">Active Deposits</h4>

                                <div class="table-responsive border">
                                    <table class=" table" style="width:100%; font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Method</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        @forelse ($deposits as $key => $deposit)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $deposit->created_at->diffForHumans() }}</td>
                                                <td>{{ number_format($deposit->payment_amount, 0) }}</td>
                                                <td>{{ ucfirst($deposit->payment_method) }}</td>
                                                <td>
                                                    <button type="button"
                                                        onclick="PrintReceiptContent('{{ $deposit->id }}')"
                                                        class="btn btn-secondary btn-sm"><i
                                                            class="fa fa-print text-white"></i></button>
                                                </td>
                                            </tr>

                                        @empty
                                            <tr>
                                                <td colspan="4" class="bg-danger text-white"> No Records Found</td>
                                            </tr>
                                        @endforelse

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <h4>Summary</h4>
                                <div class="table-responsive border">
                                    <table class=" table" style="width:100%; font-size: 12px;">
                                        <tbody>
                                        <tr>
                                            <th>Purchase Count</th>
                                            <td>{{ @$key3 + 1 }}</td>
                                        </tr>
                                     
                                        <tr>
                                            <th>Deposit Balance</th>
                                            <td>&#8358;{{ number_format($total_deposit,0) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Credit Balance</th>
                                            <td>&#8358;{{ number_format($summary_total, 0) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between">
                                <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target=".addModal">Add
                                    Payment</button>
                                <button class="btn btn-secondary mt-2" data-bs-toggle="modal" data-bs-target=".depositModal">New
                                    Deposit</button></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- #content end -->

    <div class="modal fade addModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Add New Payment</h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('customers.save.payment') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="" class="col-form-label">Payment Method:</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value=""></option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="POS">POS</option>
                                    <option value="deposit">Deposit</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" value="{{ $user->id }}" name="customer_id">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Sales ID</th>
                                        <th>Amount</th>
                                        <th>Payment Option</th>
                                        <th>Partial Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grand_total = 0;
                                    @endphp
                                    @foreach ($dates as $key => $date)
                                        @php
                                            $total_amount = 0;
                                            $total_return = 0;
                                            $return_discount = 0;

                                            $sales = App\Models\Sale::select('stock_id', 'price', 'quantity', 'discount', 'status', 'payment_amount')
                                                ->where('receipt_no', $date->receipt_no)
                                                ->get();

                                            $returns = App\Models\Returns::select('product_id', 'price', 'quantity', 'discount', 'payment_method')
                                                    ->where('return_no', 'R'.$date->receipt_no)
                                                    ->get();
                                                
                                            foreach ($sales as $sale) {
                                                $total_amount += $sale->price * $sale->quantity - $sale->discount;
                                            }

                                            $amount_payable = $total_amount - $sale->payment_amount;

                                            if($returns->count() > 0)
                                            {
                                                foreach ($returns as $return)
                                                 {
                                                    $total_return += $return->price * $return->quantity;
                                                    $return_discount += $return->discount ;
                                                 }
                                                 $amount_payable =  $total_amount - $total_return + $return_discount - $sale->payment_amount;
                                            }
                                            
                                            $grand_total += $total_amount;
                                        @endphp
                                        <input type="hidden" value="{{ $amount_payable }}" name="full_payment_payable[]" />
                                        <tr class="{{ $date->status == 'partial' ? 'bg-info text-white' : '' }}">
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $date->receipt_no }}</td>
                                            <td>&#8358;{{ number_format($amount_payable, 0) }}</td>
                                            <td>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="payment_option[]{{ $key }}"
                                                        id="fullPayment{{ $date->receipt_no }}" value="Full Payment">
                                                    <label class="form-check-label"
                                                        for="fullPayment{{ $date->receipt_no }}">Full </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="payment_option[]{{ $key }}"
                                                        id="partialPayment{{ $date->receipt_no }}"
                                                        value="Partial Payment">
                                                    <label class="form-check-label"
                                                        for="partialPayment{{ $date->receipt_no }}">Partial</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="payment_option[]{{ $key }}"
                                                        id="noPayment{{ $date->receipt_no }}" checked value="No Payment">
                                                    <label class="form-check-label"
                                                        for="noPayment{{ $date->receipt_no }}">None</label>
                                                </div>
                                            </td>
                                            <input type="hidden" name="receipt_no[]" value="{{ $date->receipt_no }}">
                                            <input type="hidden" name="full_price[]"
                                                value="{{ $total_amount - $sale->payment_amount }}">
                                            <td class="partial-amount d-none">
                                                <input type="number" name="partial_amount[]"
                                                    class="form-control partial-amount-input"
                                                    placeholder="Enter Partial Amount">
                                            </td>
                                            <td class="holdtd"></td>


                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" id="grand_total" value="{{ $grand_total }}">
                        {{-- <h3>Grand Total: <span id="grand_total_span"></span></h3> --}}

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ml-2">Add Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade depositModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Add New Deposit</h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('customers.save.deposit') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="" class="col-form-label">Payment Amount:</label>
                                <input type="number" step="any" class="form-control" placeholder="Amount" name="amount">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="" class="col-form-label">Payment Method:</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value=""></option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="POS">POS</option>
                                </select>
                            </div>
                        </div>
                       
                        <input type="hidden" value="{{ $user->id }}" name="customer_id">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ml-2">Add Deposit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal">
        <div id="print">
            @include('users.customers.receipt')
        </div>
    </div>



@endsection

@section('js')
    <script>
        $(document).ready(function() {

            var paid_amount = $('#paid_amount').val();
            var grand_total = $('#grand_total').val();
            $('#grand_total_span').html(grand_total);
            var new_grand_total = 0;
            $('input[type="radio"]').click(function() {
                var selectedOption = $(this).val();

                if (selectedOption === 'Partial Payment') {
                    $(this).closest('tr').find('.partial-amount').removeClass('d-none');
                    $(this).closest('tr').find('.holdtd').addClass('d-none');
                    var price = $(this).closest('tr').find('td:eq(2)').text();

                } else {
                    $(this).closest('tr').find('.partial-amount').addClass('d-none');
                    $(this).closest('tr').find('.holdtd').removeClass('d-none');
                }

                if (selectedOption === 'Full Payment') {

                    var full_paid = $(this).closest('tr').find('td:eq(2)').text();
                    // console.log(full_paid);
                    new_grand_total = (parseInt(grand_total) - parseInt(full_paid));
                    $('#grand_total_span').html(parseInt(new_grand_total));

                }

            });


            $(".partial-amount-input").on("keyup", function() {
                var partial_paid = $(this).val();
                var price = $(this).closest('tr').find('td:eq(2)').text();
                new_grand_total = grand_total - partial_paid;
                $('#grand_total_span').html(new_grand_total);

            });

        });
    </script>

    <script>
        function PrintReceiptContent(payment_id) {
          console.log(payment_id)
            data = {
                'payment_id': payment_id,
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('load-receipt') }}",
                data: data,
                success: function(res) {

                    var html = '';
                    var total = 0;

                    html += `The payment of &#8358;${res.payment.payment_amount.toLocaleString()} was paid to El-Habib plumbing on ${res.date} in settlement of Sales Receipt ${res.payment.receipt_nos}. <br/> Your Updated Current Balance is &#8358;${res.balance.toLocaleString()}`
                    
                    // html +=
                    //     '<tr style="text-align: center">' +
                    //     '<td></td>' +
                    //     '<td colspan="2"><b>Total Amount</b></td>' +
                    //     '<td><b>&#8358;' + total.toLocaleString() + '</b></td>' +
                    //     '</tr>';

                    html = $('#content-body').html(html);
                    // $('.tran_id').html('S' + res.items[0].receipt_no);

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
        }
    </script>
@endsection
