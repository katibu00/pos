@extends('layouts.app')
@section('PageTitle', 'Expense Account Details')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Expense Account Details</h3>
                </div>
                <div class="card-body">
                    <h5>Branch: {{ $account->branch->name }}</h5>
                    <h5>Current Balance: {{ number_format($account->balance, 2) }}</h5>
                    <h5>Daily Limit: {{ $account->daily_limit ? number_format($account->daily_limit, 2) : 'Unlimited' }}</h5>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Deposit Funds</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('expense-accounts.deposit', $account) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Deposit Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Deposit</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Set Daily Limit</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('expense-accounts.set-limit', $account) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="daily_limit" class="form-label">Daily Limit (leave blank for unlimited)</label>
                            <input type="number" name="daily_limit" id="daily_limit" class="form-control" step="0.01" min="0" value="{{ $account->daily_limit }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Set Limit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection