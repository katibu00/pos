@extends('layouts.app')
@section('PageTitle', 'Users')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <!-- Default panel contents -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">{{ $user->first_name.'\'s Profile' }}</span></div>
                        {{-- <div class="col-md-2 float-right"><button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal"
                                data-bs-target=".addModal"> <- Back</button></div> --}}
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
                                        <tbody>
                                            @foreach ($dates as $key => $date)
                                                @php
                                                    $total_amount = 0;
                                                    $sales = App\Models\Sale::select('stock_id', 'price', 'quantity', 'discount')
                                                        ->where('receipt_no', $date->receipt_no)
                                                        ->get();
                                                @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td colspan="2">{{ $date->created_at->format('l, d F') }}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                @foreach ($sales as $key2 => $sale)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $sale['product']['name'] }}</td>
                                                        <td>{{ number_format($sale->price, 0) }}</td>
                                                        <td>{{ number_format($sale->quantity, 0) }}</td>
                                                        <td>{{ number_format($sale->price * $sale->quantity, 0) }}</td>
                                                    </tr>
                                                    @php
                                                        $total_amount += $sale->price * $sale->quantity - $sale->discount;
                                                    @endphp
                                                @endforeach
                                                <tr class="bg-info text-white">
                                                    <td colspan="5">Total</td>
                                                    <td>{{ number_format($total_amount, 0) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{ $dates->links() }}
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
                                            </tr>
                                        </thead>
                                        @forelse ($payments as $key => $payment)
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ $payment->created_at->diffForHumans() }}</td>
                                                <td>{{ number_format($payment->payment_amount, 0) }}</td>
                                                <td>{{ number_format($payment->payment_type, 0) }}</td>
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
                            </div>
                            <div class="col-md-4">
                                <h4>Summary</h4>
                                <div class="table-responsive border">
                                    <table class=" table" style="width:100%; font-size: 12px;">
                                        <tr>
                                            <th>Sales Count</th>
                                            <td>---</td>
                                        </tr>
                                        <tr>
                                            <th>Balance</th>
                                            <td>---</td>
                                        </tr>
                                        <tr>
                                            <th>Discount</th>
                                            <td>---</td>
                                        </tr>
                                    </table>
                                </div>
                                <button class="btn btn-primary mt-2" data-bs-toggle="modal"
                                data-bs-target=".addModal">Add Payment</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- #content end -->

   <!-- Large Modal -->
   <div class="modal fade addModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Add New Payment</h4>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('customers.payment') }}" method="POST">
                @csrf
                <div class="modal-body">

                   
                <div class="form-group">
                    <label for="first_name" class="col-form-label">Amount:</label>
                    <input type="number" class="form-control" id="" name="amount" required>
                </div>
                <input type="hidden" value="{{ $user->id }}" name="customer_id">
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary ml-2">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection
