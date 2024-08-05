@extends('layouts.app')
@section('PageTitle', 'Record Expense')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Record Expense</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" name="description" id="description" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Record Expense</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection