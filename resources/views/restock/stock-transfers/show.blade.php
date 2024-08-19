@extends('layouts.app')
@section('PageTitle', 'Stock Transfer Details')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <h1>Stock Transfer Details</h1>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Transfer Number: {{ $transfer->transfer_number }}</h5>
                    <p class="card-text">From Branch: {{ $transfer->fromBranch->name }}</p>
                    <p class="card-text">To Branch: {{ $transfer->toBranch->name }}</p>
                    <p class="card-text">Transfer Date: {{ $transfer->transfer_date->format('Y-m-d H:i') }}</p>
                    <p class="card-text">Notes: {{ $transfer->notes ?? 'N/A' }}</p>
                </div>
            </div>
            <h2>Transferred Items</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>From Stock</th>
                        <th>To Stock</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfer->items as $item)
                    <tr>
                        <td>{{ $item->fromStock->name }}</td>
                        <td>{{ $item->toStock->name }}</td>
                        <td>{{ $item->quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="{{ route('stock-transfers.index') }}" class="btn btn-primary">Back to List</a>
        </div>
    </div>
</section>
@endsection