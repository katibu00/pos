@extends('layouts.app')
@section('PageTitle', 'Stock Transfers')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <h1>Stock Transfers</h1>
            <a href="{{ route('stock-transfers.create') }}" class="btn btn-primary mb-3">Create New Transfer</a>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Transfer Number</th>
                        <th>From Branch</th>
                        <th>To Branch</th>
                        <th>Transfer Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfers as $transfer)
                    <tr>
                        <td>{{ $transfer->transfer_number }}</td>
                        <td>{{ $transfer->fromBranch->name }}</td>
                        <td>{{ $transfer->toBranch->name }}</td>
                        <td>{{ $transfer->transfer_date->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('stock-transfers.show', $transfer->id) }}" class="btn btn-info btn-sm">Details</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $transfers->links() }}
        </div>
    </div>
</section>
@endsection