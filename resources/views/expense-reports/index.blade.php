@extends('layouts.app')
@section('PageTitle', 'Expense Reports')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Expense Reports</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('expense-reports.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="branch_id" class="form-label">Branch</label>
                                <select name="branch_id" id="branch_id" class="form-select">
                                    <option value="">All Branches</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-3 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <h4>Account Balances</h4>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th>Current Balance</th>
                                    <th>Daily Limit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($accounts as $account)
                                <tr>
                                    <td>{{ $account->branch->name }}</td>
                                    <td>{{ number_format($account->balance, 2) }}</td>
                                    <td>{{ $account->daily_limit ? number_format($account->daily_limit, 2) : 'Unlimited' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h4>Deposits</h4>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Branch</th>
                                    <th>Admin</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deposits as $deposit)
                                <tr>
                                    <td>{{ $deposit->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $deposit->expenseAccount->branch->name }}</td>
                                    <td>{{ $deposit->admin->name }}</td>
                                    <td>{{ number_format($deposit->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h4>Expenses</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Branch</th>
                                    <th>Cashier</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $expense->expenseAccount->branch->name }}</td>
                                    <td>{{ $expense->cashier->name }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td>{{ number_format($expense->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection