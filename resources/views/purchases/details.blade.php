@extends('layouts.app')
@section('PageTitle', 'Purchases')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="row my-4">
                <div class="col-md-12 mb-4">
                    <div class="table-responsive my-5">
                        <table class="table table-bordered text-left">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" class="text-center">S/N</th>
                                    <th scope="col">Product</th>
                                    <th scope="col">Cost Price (&#8358;)</th>
                                    <th scope="col">New Buying Price (&#8358;)</th>
                                    <th scope="col">Old Buying Price (&#8358;)</th>
                                    <th scope="col">Old Selling Price (&#8358;)</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Old Quantity</th>
                                    <th scope="col">Amount (&#8358;)</th>
                                    <th scope="col">Change in Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($purchases as $key => $purchase)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <th>{{ $purchase['product']['name'] }}</th>
                                        <td>{{ number_format($purchase['product']['buying_price'], 0) }}</td>
                                        <td>{{ number_format($purchase->new_buying_price, 0) }}</td>
                                        <td>
                                            @if($purchase->old_buying_price != $purchase->new_buying_price)
                                                {{ number_format($purchase->old_buying_price, 0) }}
                                            @endif
                                        </td>
                                      
                                        <td>
                                            @if($purchase->old_selling_price != $purchase->new_selling_price)
                                                {{ number_format($purchase->old_selling_price, 0) }}
                                            @endif
                                        </td>
                                        <td>{{ number_format($purchase->quantity, 0) }}</td>
                                        <td>{{ number_format($purchase->old_quantity, 0) }}</td>
                                        <td>{{ number_format($sum = $purchase->new_buying_price * $purchase->quantity, 0) }}
                                        </td>
                                        <td>
                                            @if($purchase->old_buying_price != $purchase->new_buying_price || $purchase->old_selling_price != $purchase->new_selling_price)
                                                Yes
                                            @endif
                                        </td>
                                    </tr>
                                    @php
                                        $total += $sum;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td colspan="7"></td>
                                    <td class="text-right"><strong>Grand Total</strong></td>
                                    <td><strong>&#8358;{{ number_format($total, 0) }}</strong></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
