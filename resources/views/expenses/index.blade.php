@extends('layouts.app')
@section('PageTitle', 'Expenses')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Expenses</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary mb-3">Record New Expense</a>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td>{{ number_format($expense->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $expenses->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection