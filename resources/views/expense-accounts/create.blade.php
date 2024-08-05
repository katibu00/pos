@extends('layouts.app')
@section('PageTitle', 'Create Expense Account')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Expense Account</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('expense-accounts.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="branch_id" class="form-label">Branch</label>
                            <select name="branch_id" id="branch_id" class="form-select" required>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="balance" class="form-label">Initial Balance</label>
                            <input type="number" name="balance" id="balance" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="daily_limit" class="form-label">Daily Limit (optional)</label>
                            <input type="number" name="daily_limit" id="daily_limit" class="form-control" step="0.01" min="0">
                        </div>
                        <button type="submit" class="btn btn-primary">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection