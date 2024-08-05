@extends('layouts.app')
@section('PageTitle', 'Expense Accounts')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Expense Accounts</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('expense-accounts.create') }}" class="btn btn-primary mb-3">Create New Account</a>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th>Balance</th>
                                    <th>Daily Limit</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($accounts as $account)
                                <tr>
                                    <td>{{ $account->branch->name }}</td>
                                    <td>{{ number_format($account->balance, 2) }}</td>
                                    <td>{{ $account->daily_limit ? number_format($account->daily_limit, 2) : 'Unlimited' }}</td>
                                    <td>
                                        <a href="{{ route('expense-accounts.show', $account) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('expense-accounts.edit', $account) }}" class="btn btn-sm btn-warning">Edit</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $accounts->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection