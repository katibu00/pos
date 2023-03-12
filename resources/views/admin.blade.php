@extends('layouts.app')
@section('PageTitle', 'Home')
@section('content')
    <section id="content" style="background: rgb(240, 240, 240)">
        <div class="content-wrap">
            <div class="container">

                <form class="row row-cols-lg-auto g-3 align-items-end" action="{{ route('change_branch') }}" method="POST">
                    @csrf
                    <div class="col-12">
                        <label for="branch" class="visually-hidden">Password</label>
                        <select id="branch" name="branch_id" class="form-select form-select-sm">
                            <option value=""></option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ auth()->user()->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-info col-12">Change</button>
                </form>
                <p>Today's Stats >>></p>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white mb-3" style="max-width: 20rem;">
                            <div class="card-header">Gross Sales</div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($todays_gross, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Returns</div>
                            <div class="card-body">
                                @php
                                    $return = $todays_returns_cash+$todays_returns_bank+$todays_returns_pos;
                                @endphp
                                <p class="card-text">&#8358;{{ number_format($return, 0) }} ({{ $returns_count }})</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Discounts </div>
                            <div class="card-body">
                                @php
                                    $disc = $discounts-$todays_returns_discounts;
                                @endphp
                                <p class="card-text">&#8358;{{ number_format($disc, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Expenses </div>
                            <div class="card-body">
                                @php
                                    $exp = $todays_expense_cash+$todays_expense_bank;
                                @endphp
                                <p class="card-text">&#8358;{{ number_format($exp, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Credit Payments </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($credit_all, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white mb-3" style="max-width: 20rem;">
                            <div class="card-header">Cash </div>
                            <div class="card-body">
                                @php
                                    $cash = $todays_cash-$todays_returns_cash-$todays_expense_cash;
                                @endphp
                                <p class="card-text">&#8358;{{ number_format($cash+$credit_cash, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white mb-3" style="max-width: 20rem;">
                            <div class="card-header">POS </div>
                            <div class="card-body">
                                @php
                                    $pos = $todays_pos-$todays_returns_bank-$todays_returns_pos-$todays_expense_bank;
                                @endphp
                                <p class="card-text">&#8358;{{ number_format($pos+$credit_pos,0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white mb-3" style="max-width: 20rem;">
                            <div class="card-header">Transfer </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($todays_transfer+$credit_transfer, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Credit Sales </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($todays_credit, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Estimates </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($todays_estimate, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Walk-in </div>
                            <div class="card-body">
                                <p class="card-text">{{ number_format($sales_count, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Items Sold </div>
                            <div class="card-body">
                                <p class="card-text">{{ number_format($items_sold, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Purchases </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($todays_purchases, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white  mb-3" style="max-width: 20rem;">
                            <div class="card-header">Net Sales </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format(($cash+$pos+$todays_credit+$todays_transfer), 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white  mb-3" style="max-width: 20rem;">
                            <div class="card-header">Gross Profit </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($todays_net_sales, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Low Stock Counts</div>
                            <div class="card-body">
                                <p class="card-text">{{ $lows .' of '.$total_stock }}</p>
                            </div>
                        </div>
                    </div>

                </div>

                </div>
            </div>
    </section>
@endsection
