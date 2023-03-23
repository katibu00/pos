@extends('layouts.app')
@section('PageTitle', 'Home')
@section('content')
    <section id="content" style="background: rgb(240, 240, 240)">
        <div class="content-wrap">
            <div class="container">

                <div class="row">
                    <div class="col-md-6">
                        <form class="row" action="{{ route('change_branch') }}" method="POST">
                            @csrf
                            <div class="col-6">
                                <label for="branch" class="visually-hidden">Password</label>
                                <select id="branch" name="branch_id" class="form-select form-select-sm">
                                    <option value=""></option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ auth()->user()->branch_id == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                            <button type="submit" class="btn btn-sm btn-info text-white col-6">Change</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form class="row" action="{{ route('change_date') }}" method="POST">
                            @csrf
                            <div class="col-6">
                               <input type="date" class="form-control form-control-sm" placeholder="Pick a Date" value="{{ isset($date) ? $date : ''  }}" name="date">
                            </div>
                            <div class="col-6">
                            <button type="submit" class="btn btn-sm btn-secondary text-white col-6">Go >>></button>
                            </div>
                        </form>
                    </div>
                    
                </div>
                <p>Today's Stats >>></p>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white mb-3" style="max-width: 20rem;">
                            <div class="card-header">Gross Sales</div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($grossSales, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Returns</div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($totalReturn, 0) }}
                                    ({{ 'Cash: ' . number_format($cashReturns, 0) . ' POS: ' . number_format($posReturns, 0) . ' Trans: ' . number_format($transferReturns, 0) }})
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Discounts </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($totalDiscount, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Expenses </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($totalExpenses, 0) }}
                                    ({{ 'Cash: ' . number_format($cashExpenses, 0) . ' POS: ' . number_format($posExpenses, 0) . ' Trans: ' . number_format($transferExpenses, 0) }})
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Credit Payments </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($totalCreditPayments, 0) }}
                                    ({{ 'Cash: ' . number_format($cashCreditPayments, 0) . ' POS: ' . number_format($posCreditPayments, 0) . ' Trans: ' . number_format($transferCreditPayments, 0) }})
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white mb-3" style="max-width: 20rem;">
                            <div class="card-header">Cash </div>
                            <div class="card-body">
                                <p class="card-text">
                                    &#8358;{{ number_format($cashSales - ($cashExpenses + $cashReturns), 0) }}
                                    ({{ 'Sales: ' . number_format($cashSales, 0) . ' Returns: ' . number_format($cashReturns, 0) . ' Expense: ' . number_format($cashExpenses, 0) }})
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white mb-3" style="max-width: 20rem;">
                            <div class="card-header">POS </div>
                            <div class="card-body">
                                <p class="card-text">
                                    &#8358;{{ number_format($posSales - ($posExpenses + $posReturns), 0) }}
                                    ({{ 'Sales: ' . number_format($posSales, 0) . ' Returns: ' . number_format($posReturns, 0) . ' Expense: ' . number_format($posExpenses, 0) }})
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white mb-3" style="max-width: 20rem;">
                            <div class="card-header">Transfer </div>
                            <div class="card-body">
                                <p class="card-text">
                                    &#8358;{{ number_format($transferSales - ($transferExpenses + $transferReturns), 0) }}
                                    ({{ 'Sales: ' . number_format($transferSales, 0) . ' Returns: ' . number_format($transferReturns, 0) . ' Expense: ' . number_format($transferExpenses, 0) }})
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Credit Sales </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($creditSales, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Estimates </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($totalEstimate, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Credit Owed </div>
                            @php
                                $owed = App\Models\User::select('balance')->sum('balance');
                            @endphp
                            <div class="card-body">
                                <p class="card-text">{{ number_format($owed,0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Walk-in </div>
                            <div class="card-body">
                                <p class="card-text">{{ number_format($uniqueSalesCount, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Items Sold </div>
                            <div class="card-body">
                                <p class="card-text">{{ number_format($totalItemsSold, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Purchases </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($totalPurchases, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white  mb-3" style="max-width: 20rem;">
                            <div class="card-header">Net Sales </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($grossSales - $totalDiscount, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white  mb-3" style="max-width: 20rem;">
                            <div class="card-header">Gross Profit </div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($grossProfit - $totalDiscount, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card mb-3" style="max-width: 20rem;">
                            <div class="card-header">Low Stock Counts</div>
                            <div class="card-body">
                                <p class="card-text">{{ $lows . ' of ' . $total_stock }}</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
@endsection
