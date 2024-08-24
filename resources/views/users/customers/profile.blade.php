@extends('layouts.app')
@section('PageTitle', 'User Profile')
@section('content')


    <link rel="stylesheet" href="/css/font-icons.css">

    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <div class="row gx-5">
                    <div class="col-md-9">
                        <div class="heading-block border-0 d-flex justify-content-between">
                            <h3>{{ $user->first_name . '\'s Profile' }}</h3>
                            <a href="{{ route('customers.index') }}" class="btn btn-sm btn-primary"><- customers list</a>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div>
                                    <ul class="nav canvas-alt-tabs tabs-alt tabs nav-tabs mb-3" id="tabs-profile"
                                        role="tablist" style="--bs-nav-link-font-weight: 600;">

                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="canvas-home-alt-tab" data-bs-toggle="pill"
                                                data-bs-target="#home-alt" type="button" role="tab"
                                                aria-controls="canvas-home-alt" aria-selected="true"><i
                                                    class="fas fa-shopping-cart"></i>
                                                Credit Purchases</a></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="canvas-about-alt-tab" data-bs-toggle="pill"
                                                data-bs-target="#about-alt" type="button" role="tab"
                                                aria-controls="canvas-about-alt" aria-selected="false"><i
                                                    class="fa fa-credit-card"></i>
                                                Credits Payments</a></button>
                                        </li>

                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="canvas-contact-alt-tab" data-bs-toggle="pill"
                                                data-bs-target="#contact-alt" type="button" role="tab"
                                                aria-controls="canvas-contact-alt" aria-selected="false"><i
                                                    class="fa fa-history"></i> Shopping History</a></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="active_deposits" data-bs-toggle="pill"
                                                data-bs-target="#deposit-alt" type="button" role="tab"
                                                aria-controls="deposit-alt" aria-selected="false"><i
                                                    class="fas fa-money-bill"></i>
                                                Deposit History</a></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="cash_credits" data-bs-toggle="pill"
                                                    data-bs-target="#cash_credits-alt" type="button" role="tab"
                                                    aria-controls="cash_credits-alt" aria-selected="false">
                                                <i class="fas fa-money-check-alt"></i> Cash Credits
                                            </button>
                                        </li>
                                        
                                    </ul>
                                    <div id="canvas-TabContent2" class="tab-content">

                                        <div class="tab-pane fade show active" id="home-alt" role="tabpanel"
                                            aria-labelledby="canvas-home-tab" tabindex="0">

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
                                                                $sales = App\Models\Sale::select(
                                                                    'stock_id',
                                                                    'price',
                                                                    'quantity',
                                                                    'discount',
                                                                    'status',
                                                                    'payment_amount',
                                                                )
                                                                    ->where('receipt_no', $date->receipt_no)
                                                                    ->where('customer', $user->id)
                                                                    ->get();
                                                                $returns = App\Models\Returns::select(
                                                                    'product_id',
                                                                    'price',
                                                                    'quantity',
                                                                    'discount',
                                                                    'payment_method',
                                                                )
                                                                    ->where('return_no', 'R' . $date->receipt_no)
                                                                    ->get();
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $key3 + 1 }}</td>
                                                                <td colspan="2">
                                                                    {{ $date->created_at->format('l, d F') . ' (S' . $date->receipt_no . ')' }}
                                                                </td>
                                                                <td></td>
                                                                <td></td>
                                                                <td><a href="{{ route('users.return.index', ['id' => $date->receipt_no]) }}"
                                                                        class="btn btn-danger btn-sm"><i
                                                                            class="fas fa-undo text-white"></i></a></td>
                                                            </tr>
                                                            @foreach ($sales as $key2 => $sale)
                                                                <tr
                                                                    @if ($sale->status == 'partial') class="bg-info text-white" @endif>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td>{{ @$sale->product->name }}</td>
                                                                    <td>{{ number_format(@$sale->price, 0) }}</td>
                                                                    <td>{{ @$sale->quantity }}</td>
                                                                    <td>{{ number_format(@$sale->price * @$sale->quantity, 0) }}
                                                                    </td>
                                                                </tr>
                                                                @php
                                                                    $total_amount += @$sale->price * @$sale->quantity;
                                                                    $total_discount += @$sale->discount;
                                                                    $total_return = 0;
                                                                    $return_discount = 0;
                                                                @endphp
                                                            @endforeach
                                                            <tr
                                                                @if (@$sale->status == 'partial') class="bg-info text-white" @endif>
                                                                <td colspan="3"></td>
                                                                <td colspan="2" class="text-center">Sub Total</td>
                                                                <td>{{ number_format($total_amount, 0) }}</td>
                                                            </tr>
                                                            <tr
                                                                @if ($sale->status == 'partial') class="bg-info text-white" @endif>
                                                                <td colspan="3"></td>
                                                                <td colspan="2" class="text-center">Discount</td>
                                                                <td>{{ number_format($total_discount, 0) }}</td>
                                                            </tr>
                                                            @if ($returns->count() > 0)
                                                                @foreach ($returns as $return)
                                                                    <tr class="bg-danger text-white">
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td>{{ $return['product']['name'] }}</td>
                                                                        <td>{{ number_format($return->price, 0) }}</td>
                                                                        <td>{{ $return->quantity }}</td>
                                                                        <td>{{ number_format($return->price * $return->quantity, 0) }}
                                                                        </td>
                                                                    </tr>
                                                                    @php
                                                                        $total_return +=
                                                                            $return->price * $return->quantity;
                                                                        $return_discount += $return->discount;
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
                                                            <tr
                                                                @if ($sale->status == 'partial') class="bg-info text-white" @endif>
                                                                <td colspan="3"></td>
                                                                <td colspan="2" class="text-right"><strong>Net
                                                                        Amount</strong></td>
                                                                @php
                                                                    $net_amount =
                                                                        $total_amount -
                                                                        $total_return -
                                                                        $total_discount +
                                                                        $return_discount;
                                                                    if ($sale->status != 'partial') {
                                                                        $summary_total += $net_amount;
                                                                    }
                                                                @endphp

                                                                <td><strong>&#8358;{{ number_format($net_amount, 0) }}</strong>
                                                                </td>
                                                            </tr>

                                                            @if ($sale->status == 'partial')
                                                                <tr
                                                                    @if ($sale->status == 'partial') class="bg-info text-white" @endif>
                                                                    <td colspan="3"></td>
                                                                    <td colspan="2" class="text-center">Amount Paid
                                                                    </td>
                                                                    <td>{{ number_format($sale->payment_amount, 0) }}</td>
                                                                </tr>
                                                                <tr
                                                                    @if ($sale->status == 'partial') class="bg-info text-white" @endif>
                                                                    <td colspan="3"></td>
                                                                    <td colspan="2" class="text-center">
                                                                        <strong>Remaining
                                                                            Balance</strong></td>
                                                                    @php
                                                                        $remaining =
                                                                            $total_amount -
                                                                            $total_discount -
                                                                            $total_return +
                                                                            $return_discount -
                                                                            $sale->payment_amount;
                                                                        $summary_total += $remaining;
                                                                    @endphp
                                                                    <td><strong>&#8358;{{ number_format($remaining, 0) }}</strong>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                            </div>

                                        </div>

                                        <div class="tab-pane fade" id="contact-alt" role="tabpanel"
                                            aria-labelledby="canvas-contact-tab" tabindex="0">
                                            @php
                                                $total_spent = 0;
                                            @endphp

                                            @foreach ($shoppingHistory as $date => $purchases)
                                                <h3>{{ $date }}</h3>
                                                <div class="table-responsive border">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>S/N</th>
                                                                <th>Item</th>
                                                                <th>Price (&#8358;)</th>
                                                                <th>Quantity</th>
                                                                <th>Discount (&#8358;)</th>
                                                                <th>Subtotal (&#8358;)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $total_price = 0;
                                                                $total_discount = 0;
                                                            @endphp
                                                            @foreach ($purchases as $index => $purchase)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ @$purchase->product->name }}</td>
                                                                    <td>{{ number_format($purchase->price, 0) }}</td>
                                                                    <td>{{ number_format($purchase->quantity, 0) }}</td>
                                                                    <td>{{ number_format($purchase->discount, 0) }}</td>
                                                                    @php
                                                                        $subtotal =
                                                                            $purchase->price * $purchase->quantity;
                                                                        $total_spent += $subtotal - $purchase->discount;
                                                                        $total_price += $subtotal - $purchase->discount;
                                                                        $total_discount += $purchase->discount;
                                                                    @endphp
                                                                    <td>{{ number_format($subtotal - $purchase->discount, 0) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td colspan="4"></td>
                                                                <td><em>Total Discount</em></td>
                                                                <td><strong>&#8358;{{ number_format($total_discount, 0) }}</strong>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4"></td>
                                                                <td><em>Net Total</em></td>
                                                                <td><strong>&#8358;{{ number_format($total_price, 0) }}</strong>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4"></td>
                                                                <td><em>Payment Method</em></td>
                                                                <td><strong>{{ @$purchase->payment_method }}</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4"></td>
                                                                <td><em>Partial Amount Paid (Before Full Payment)</em></td>
                                                                <td><strong>&#8358;{{ number_format(@$purchase->payment_amount, 0) }}</strong>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4"></td>
                                                                <td><em>Payment Status</em></td>
                                                                <td><strong>{{ ucfirst($purchase->status) }}</strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4"></td>
                                                                <td><em>Note</em></td>
                                                                <td><strong>{{ @$purchase->note }}</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endforeach

                                            <h3>Total Money Spent Via Credit = &#8358;{{ number_format($total_spent, 0) }}
                                            </h3>

                                        </div>

                                        <div class="tab-pane fade" id="about-alt" role="tabpanel"
                                            aria-labelledby="canvas-about-tab" tabindex="0">
                                            <div class="clear mt-4"></div>

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
                                                            <td colspan="4" class="bg-danger text-white"> No Records
                                                                Found</td>
                                                        </tr>
                                                    @endforelse

                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>

                                        <div class="tab-pane fade" id="deposit-alt" role="tabpanel"
                                            aria-labelledby="active_deposit" tabindex="0">
                                            <div class="clear mt-4"></div>

                                            @php
                                                $deposits = App\Models\Payment::where('customer_id', $user->id)
                                                    ->where('payment_type', 'deposit')
                                                    ->latest()
                                                    ->get();
                                            @endphp



                                            @include('users.customers.deposit_table')

                                        </div>

                                        <div class="tab-pane fade" id="cash_credits-alt" role="tabpanel" aria-labelledby="cash_credits" tabindex="0">
                                            <div class="clear mt-4"></div>

                                           <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Cashier</th>
                                                        <th>Amount</th>
                                                        <th>Amount Paid</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($cashcredits as $key => $cashcredit)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ optional($cashcredit->cashier)->first_name }}</td>
                                                            <td>{{ $cashcredit->amount }}</td>
                                                            <td>{{ $cashcredit->amount_paid }}</td>
                                                            <td>{{ $cashcredit->status }}</td>
                                                            <td>{{ $cashcredit->created_at->diffForHumans() }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="9">No active cash credits for this customer.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                           </div>

                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 line d-block d-md-none"></div>
                    <div class="col-md-3">
                        <div class="fancy-title mt-5 title-border">
                            <h4>Summary</h4>
                        </div>
                        <div class="list-group">
                            <a href="#"
                                class="list-group-item list-group-item-action d-flex justify-content-between">
                                <div>Credit Balance</div><span class="badge bg-secondary float-end"
                                    style="margin-top: 3px;">&#8358;{{ number_format($summary_total, 0) }}</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action d-flex justify-content-between">
                                <div>Deposit Balance</div><span class="badge bg-secondary float-end"
                                    style="margin-top: 3px;">&#8358;{{ number_format($user->deposit, 0) }}</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action d-flex justify-content-between">
                                <div>Cash Credit Balance</div><span class="badge bg-secondary float-end"
                                    style="margin-top: 3px;">&#8358;{{ number_format($totalCashCreditOwed, 0) }}</span>
                            </a>
                            <a href="#"
                                class="list-group-item list-group-item-action d-flex justify-content-between">
                                <div>Purchases Count</div><span class="badge bg-secondary float-end"
                                    style="margin-top: 3px;">{{ @$key3 + 1 }}</span>
                            </a>


                        </div>
                        <div class="fancy-title mt-5 title-border">
                            <h4>Actions</h4>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target=".addModal">Add
                                Payment</button>
                            <button class="btn btn-secondary mt-2" data-bs-toggle="modal"
                                data-bs-target=".depositModal">New
                                Deposit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade addModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Add New Payment</h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form id="addPaymentForm">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="" class="col-form-label">Payment Method:</label>
                                <select id="paymentMethod" class="form-select" name="payment_method" required>
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
                                        <th>Sales Date</th>
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

                                            $sales = App\Models\Sale::select(
                                                'stock_id',
                                                'price',
                                                'quantity',
                                                'discount',
                                                'status',
                                                'payment_amount',
                                            )
                                                ->where('receipt_no', $date->receipt_no)
                                                ->where('customer', $user->id)
                                                ->get();

                                            $returns = App\Models\Returns::select(
                                                'product_id',
                                                'price',
                                                'quantity',
                                                'discount',
                                                'payment_method',
                                            )
                                                ->where('return_no', 'R' . $date->receipt_no)
                                                ->get();

                                            foreach ($sales as $sale) {
                                                $total_amount += $sale->price * $sale->quantity - $sale->discount;
                                            }

                                            $amount_payable = $total_amount - $sale->payment_amount;

                                            if ($returns->count() > 0) {
                                                foreach ($returns as $return) {
                                                    $total_return += $return->price * $return->quantity;
                                                    $return_discount += $return->discount;
                                                }
                                                $amount_payable =
                                                    $total_amount -
                                                    $total_return +
                                                    $return_discount -
                                                    $sale->payment_amount;
                                            }

                                            $grand_total += $total_amount;
                                        @endphp
                                        <input type="hidden" value="{{ $amount_payable }}"
                                            name="full_payment_payable[]" />
                                        <tr class="{{ $date->status == 'partial' ? 'bg-info text-white' : '' }}">
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $date->created_at }}</td>
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
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total Amount to be Paid:</strong></td>
                                        <td id="totalAmountToPay">&#8358;0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <input type="hidden" id="grand_total" value="{{ $grand_total }}">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="submitFormBtn" class="btn btn-primary ml-2">Add Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 

    <div class="modal fade depositModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
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
                                <input type="number" step="any" class="form-control" placeholder="Amount"
                                    name="amount">
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Deposit Amount</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="paymentAmount">Payment Amount</label>
                            <input type="number" id="paymentAmount" name="payment_amount" class="form-control"
                                required>
                        </div>
                        <!-- Add more input fields for editing other deposit details if needed -->
                        <input type="hidden" id="depositId" name="deposit_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('addPaymentForm');
                const totalAmountToPayElement = document.getElementById('totalAmountToPay');
            
                form.addEventListener('change', calculateTotal);
                form.addEventListener('input', calculateTotal);
            
                function calculateTotal() {
                    let total = 0;
                    const rows = form.querySelectorAll('tbody tr');
            
                    rows.forEach((row, index) => {
                        const amountElement = row.cells[2];
                        const fullPaymentRadio = row.querySelector(`input[name="payment_option[]${index}"][value="Full Payment"]`);
                        const partialPaymentRadio = row.querySelector(`input[name="payment_option[]${index}"][value="Partial Payment"]`);
                        const partialAmountInput = row.querySelector('.partial-amount-input');
            
                        const fullAmount = parseFloat(amountElement.textContent.replace('₦', '').replace(',', ''));
            
                        if (fullPaymentRadio.checked) {
                            total += fullAmount;
                        } else if (partialPaymentRadio.checked && partialAmountInput.value) {
                            total += parseFloat(partialAmountInput.value);
                        }
            
                        // Show/hide partial amount input
                        const partialAmountCell = row.querySelector('.partial-amount');
                        partialAmountCell.classList.toggle('d-none', !partialPaymentRadio.checked);
                    });
            
                    totalAmountToPayElement.textContent = '₦' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                }
            });
            </script>

    <script>
        $(document).ready(function() {

            $('#submitFormBtn').click(function(event) {
                // Prevent default form submission
                event.preventDefault();

                if (!$('#paymentMethod').val()) {
                    swal({
                        title: "Error!",
                        text: "Please select a payment method.",
                        icon: "error",
                    });
                    return; // Exit the function to prevent further execution
                }

                // Serialize form data
                var formData = $('#addPaymentForm').serialize();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.LoadingOverlay("show");

                $.ajax({
                    url: '{{ route('customers.save.payment') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $.LoadingOverlay("hide");
                        $('.addModal').modal('hide');

                        if (response.success) {
                            swal({
                                title: "Success!",
                                text: response.success,
                                icon: "success",
                            }).then((value) => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // If there was an error, handle it accordingly
                        console.error('Error:', error);
                        $.LoadingOverlay("hide");

                        // Check if there's an error message
                        if (xhr.status === 400) {
                            swal({
                                title: "Error!",
                                text: xhr.responseJSON.error,
                                icon: "error",
                            });
                        } else if (xhr.status === 500) {
                            swal({
                                title: "Error!",
                                text: "Internal Server Error. Please try again later.",
                                icon: "error",
                            });
                        }
                    },
                    complete: function(xhr, status) {
                        $.LoadingOverlay("hide");

                        // Check if there's a warning message
                        if (xhr.responseJSON && xhr.responseJSON.warning) {
                            swal({
                                title: "Warning!",
                                text: xhr.responseJSON.warning,
                                icon: "warning",
                            });
                        }
                    }
                });
            });
        });







        function editDeposit(depositId, paymentAmount) {
            $('#editModal').find('#depositId').val(depositId);
            $('#editModal').find('#paymentAmount').val(paymentAmount);

            $('#editModal').modal('show');
        }

        $('#editForm').submit(function(e) {
            e.preventDefault();

            // Get the form data
            var depositId = $('#editModal').find('#depositId').val();
            var newPaymentAmount = $('#editModal').find('#paymentAmount').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('customers.update.deposit') }}',
                type: 'POST',
                data: {
                    'depositId': depositId,
                    'payment_amount': newPaymentAmount
                },
                success: function(response) {

                    toastr.success(response.message);
                    $('#editModal').modal('hide');
                    $('.deposit_table').load(location.href + ' .deposit_table');
                },
                error: function(xhr) {

                }
            });
        });
    </script>



    <script>
        jQuery("#tabs-profile").on("tabsactivate", function(event, ui) {
            jQuery('.flexslider .slide').resize();
        });
    </script>
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
                    var createdAtDate = new Date(res.payment.created_at);
                    var options = {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    };
                    var formattedDate = createdAtDate.toLocaleDateString('en-GB', options);
                    html +=
                        `The payment of &#8358;${res.payment.payment_amount.toLocaleString()} was paid to the above named business on ${formattedDate} in settlement of sales made on ${res.dates}. <br/> Your credit Balance is &#8358;${res.balance.toLocaleString()}`

                    html = $('#content-body').html(html);

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
                        setTimeout(() => {
                            window.location.replace('{{ route('login') }}');
                        }, 2000);
                    }
                },
            });
        }
    </script>

@endsection
