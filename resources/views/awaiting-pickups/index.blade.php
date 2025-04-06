<!-- resources/views/awaiting-pickups/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Awaiting Pickups</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col">Receipt No</th>
                            <th scope="col">Date</th>
                            <th scope="col">Customer</th>
                            <th scope="col" class="text-center">Total Items</th>
                            <th scope="col" class="text-center">Value (&#8358;)</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($awaitingPickups as $key => $row)
                            @php
                                $firstPickup = App\Models\AwaitingPickup::where('receipt_no', $row->receipt_no)
                                    ->where('status', 'awaiting')
                                    ->first();
                                
                                $totalItems = App\Models\AwaitingPickup::where('receipt_no', $row->receipt_no)
                                    ->where('status', 'awaiting')
                                    ->sum('quantity');
                                
                                $totalValue = App\Models\AwaitingPickup::where('receipt_no', $row->receipt_no)
                                    ->where('status', 'awaiting')
                                    ->selectRaw('SUM(price * quantity) as total')
                                    ->first()->total;
                                
                                $sale = App\Models\Sale::where('receipt_no', $row->receipt_no)->first();
                                $customer = is_null($sale->customer) ? 'Walk-in Customer' : 
                                    (is_numeric($sale->customer) ? App\Models\User::find($sale->customer)->first_name : $sale->customer);
                            @endphp
                            <tr>
                                <td class="text-center">{{ $key + $awaitingPickups->firstItem() }}</td>
                                <td>{{ $row->receipt_no }}</td>
                                <td>{{ $firstPickup->created_at->format('l, d F') }}</td>
                                <td>{{ $customer }}</td>
                                <td class="text-center">{{ number_format($totalItems, 2) }}</td>
                                <td class="text-center">{{ number_format($totalValue, 0) }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="dots"></span>
                                            <span class="dots"></span>
                                            <span class="dots"></span>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="#" onclick="deliverItems('{{ $row->receipt_no }}')">
                                                <i class="fas fa-shipping-fast"></i> Deliver Items
                                            </a>
                                            <a class="dropdown-item" href="#" onclick="PrintReceiptContent('{{ $row->receipt_no }}', 'AwaitingPickup')">
                                                <i class="fa fa-print"></i> Print Pickup Slip
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        {{ $awaitingPickups->links() }}
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Include the delivery modal -->
@include('partials.deliver-items-modal')

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Script code is already added to your index page
    });
</script>
@endsection