@extends('layouts.app')
@section('PageTitle', 'Home')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
<style>
    .dashboard-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    .card-icon {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    .stats-section {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .stats-title {
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .chart-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }
    .form-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }
    @media (max-width: 768px) {
        .col-md-4 {
            margin-bottom: 20px;
        }
    }
</style>
@endsection

@section('content')
<section id="content" style="background: rgb(240, 240, 240)">
    <div class="content-wrap">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card dashboard-card bg-primary text-white">
                        <div class="card-body">
                            <i class="fas fa-money-bill-wave card-icon"></i>
                            <h5 class="card-title">Cash Balance</h5>
                            <?php
                            $result = $cashSales - ($cashExpenses + $cashReturns) + $cashCreditPayments + $cashDepositPayments - $cashCreditToday + $CreditPaymentSummary['cash'] + ($cashFundTransfer);
                            $formattedResult = number_format($result);
                            ?>
                            <p class="card-text h3">&#8358;{{ $formattedResult }}</p>
                            <p class="card-subtitle mb-2 small">
                                {{ 'Sales: ' . $cashSales . ', Returns: ' . $cashReturns . ', Expenses: ' . $cashExpenses . ', Repayments: ' . $cashCreditPayments . ', Deposit ' . $cashDepositPayments . ', Cash Credit: ' . $cashCreditToday . ', CC Repayment: ' . $CreditPaymentSummary['cash'].', Funds Transfer '.$cashFundTransfer }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card dashboard-card bg-success text-white">
                        <div class="card-body">
                            <i class="fas fa-exchange-alt card-icon"></i>
                            <h5 class="card-title">Transfer Balance</h5>
                            <?php
                            $transferResult = $transferSales - ($transferExpenses + $transferReturns) + $transferCreditPayments + $transferDepositPayments + $CreditPaymentSummary['transfer'] + ($transferFundTransfer);
                            $formattedTransferResult = number_format($transferResult);
                            ?>
                            <p class="card-text h3">&#8358;{{ $formattedTransferResult }}</p>
                            <p class="card-subtitle mb-2 small">
                                {{ 'Sales: ' . $transferSales . ', Returns: ' . $transferReturns . ', Expenses: ' . $transferExpenses . ', Repayments: ' . $transferCreditPayments . ', Deposit ' . $transferDepositPayments . ', CC Repayment: ' . $CreditPaymentSummary['transfer'].', Funds Transfer '.$transferFundTransfer }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card dashboard-card bg-info text-white">
                        <div class="card-body">
                            <i class="fas fa-credit-card card-icon"></i>
                            <h5 class="card-title">POS Balance</h5>
                            <?php
                            $posResult = $posSales - ($posExpenses + $posReturns) + $posCreditPayments + $posDepositPayments + $CreditPaymentSummary['pos'] + ($posFundTransfer);
                            $formattedPosResult = number_format($posResult);
                            ?>
                            <p class="card-text h3">&#8358;{{ $formattedPosResult }}</p>
                            <p class="card-subtitle mb-2 small">
                                {{ 'Sales: ' . $posSales . ', Returns: ' . $posReturns . ', Expenses: ' . $posExpenses . ', Repayments: ' . $posCreditPayments . ', Deposit ' . $posDepositPayments . ', CC Repayment: ' . $CreditPaymentSummary['pos'].', Funds Transfer '.$posFundTransfer }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stats-section">
                <h3 class="stats-title text-center mb-4">
                    @if (isset($start_date) && isset($end_date))
                        Stats for {{ \Carbon\Carbon::parse($start_date)->toFormattedDateString() }}
                        - {{ \Carbon\Carbon::parse($end_date)->toFormattedDateString() }}
                        <small>({{ \Carbon\Carbon::parse($start_date)->diffInDays($end_date) }} days apart)</small>
                    @elseif (isset($selected_date))
                        Stats for {{ \Carbon\Carbon::parse($selected_date)->toFormattedDateString() }}
                    @else
                        Today's Stats
                    @endif
                </h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="stat-card bg-primary text-white mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Gross Sales</h5>
                                <p class="stat-card-text">Total sales before deductions</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($grossSales, 0) }}</h3>
                            </div>
                        </div>
                        <div class="stat-card bg-danger text-white mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Total Returns</h5>
                                <p class="stat-card-text">Cash: {{ number_format($cashReturns, 0) }}, POS: {{ number_format($posReturns, 0) }}, Transfer: {{ number_format($transferReturns, 0) }}, Credit: {{ number_format($profileReturns, 0) }}, Discount: {{ number_format($returnDiscounts, 0) }}</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($totalReturn, 0) }}</h3>
                            </div>
                        </div>
                        <div class="stat-card bg-warning text-dark mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Discounts</h5>
                                <p class="stat-card-text">Sales Discount: {{ number_format($totalDiscounts, 0) }}, Return Discount: {{ number_format($returnDiscounts, 0) }}</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($totalDiscounts - $returnDiscounts, 0) }}</h3>
                            </div>
                        </div>
                        <div class="stat-card bg-info text-white mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Expenses</h5>
                                <p class="stat-card-text">Cash: {{ number_format($cashExpenses, 0) }}, POS: {{ number_format($posExpenses, 0) }}, Transfer: {{ number_format($transferExpenses, 0) }}</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($totalExpenses, 0) }}</h3>
                            </div>
                        </div>
                        <div class="stat-card bg-secondary text-white mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Purchases</h5>
                                <p class="stat-card-text">Total purchases made</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($totalPurchases, 0) }}</h3>
                            </div>
                        </div>
                        <div class="stat-card bg-light text-dark mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Estimates</h5>
                                <p class="stat-card-text">Total estimated sales</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($totalEstimate, 0) }}</h3>
                            </div>
                        </div>
                        @php
                        $owed = App\Models\User::select('balance')
                                ->where('branch_id', auth()->user()->branch_id)
                                ->sum('balance');
                        @endphp
                        <div class="stat-card bg-dark text-white mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Total Credit</h5>
                                <p class="stat-card-text">Total credit given to customers</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($owed, 0) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card bg-success text-white mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Net Sales</h5>
                                <p class="stat-card-text">Gross: {{ number_format($grossSales, 0) }}, Discount: {{ number_format($totalDiscount, 0) }}, Return: {{ number_format($totalReturn, 0) }}, Return Discount: {{ number_format($returnDiscounts, 0) }}</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($grossSales - $totalDiscount - ($totalReturn - $returnDiscounts), 0) }}</h3>
                            </div>
                        </div>
                        <div class="stat-card bg-purple text-white mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Gross Profit</h5>
                                <p class="stat-card-text">Sales Profit: {{ number_format($grossProfit, 0) }}, Discount: {{ number_format($totalDiscount, 0) }}, Return Profit: {{ number_format($returnProfit, 0) }}, Return Discount: {{ number_format($returnDiscounts, 0) }}</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($grossProfit - $totalDiscounts - ($returnProfit - $returnDiscounts), 0) }}</h3>
                            </div>
                        </div>
                        <div class="stat-card bg-teal text-white mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Net Profit (approx.)</h5>
                                <p class="stat-card-text">Gross Profit: {{ number_format($grossProfit, 0) }}, Discounts: {{ number_format($totalDiscount, 0) }}, Return Profit: {{ number_format($returnProfit, 0) }}, Return Discount: {{ number_format($returnDiscounts, 0) }}, Expenses: {{ number_format($totalExpenses, 0) }}</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($grossProfit - $totalDiscounts - ($returnProfit - $returnDiscounts) - $totalExpenses, 0) }}</h3>
                            </div>
                        </div>
                        <div class="stat-card bg-info text-white mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Total Deposit Balance</h5>
                                <p class="stat-card-text">Current deposit balance</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($deposits, 0) }}</h3>
                            </div>
                        </div>
                        <div class="stat-card bg-warning text-dark mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Today's Deposit</h5>
                                <p class="stat-card-text">Cash: {{ number_format($cashDepositPayments, 0) }}, POS: {{ number_format($posDepositPayments, 0) }}, Transfer: {{ number_format($transferDepositPayments, 0) }}</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($totalDepositPayments, 0) }}</h3>
                            </div>
                        </div>
                        <div class="stat-card bg-secondary text-white mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Walk-in Count</h5>
                                <p class="stat-card-text">Number of unique sales</p>
                                <h3 class="stat-card-value">{{ number_format($uniqueSalesCount, 0) }}</h3>
                            </div>
                        </div>
                        <div class="stat-card bg-danger text-white mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Awaiting Pickup</h5>
                                <p class="stat-card-text">Number of uncollected sales</p>
                                <h3 class="stat-card-value">{{ number_format(@$uncollectedSales->count(), 0) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="stat-card bg-light text-dark mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Total Cash Credit Balance Remaining</h5>
                                <p class="stat-card-text">Outstanding cash credit</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format(@$TotalcashCredit, 0) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-info text-white mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Total Stock Value</h5>
                                <p class="stat-card-text">Current value of all stock</p>
                                <h3 class="stat-card-value">&#8358;{{ number_format($total_stock_value,0) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-warning text-dark mb-3">
                            <div class="stat-card-body">
                                <h5 class="stat-card-title">Low Stock Counts</h5>
                                <p class="stat-card-text">Number of items with low stock</p>
                                <h3 class="stat-card-value">{{ $low_stocks_count }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="card dashboard-card bg-info text-white">
                            <div class="card-body">
                                <i class="fas fa-credit-card card-icon"></i>
                                <h5 class="card-title">Credit Payments</h5>
                                <?php
                                $formattedCreditPayments = number_format($totalCreditPayments);
                                ?>
                                <p class="card-text h3">&#8358;{{ $formattedCreditPayments }}</p>
                                <p class="card-subtitle mb-2 small">
                                    {{ 'Cash: ' . number_format($cashCreditPayments, 0) . ', POS: ' . number_format($posCreditPayments, 0) . ', Transfer: ' . number_format($transferCreditPayments, 0) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card dashboard-card bg-warning text-dark">
                            <div class="card-body">
                                <i class="fas fa-money-check-alt card-icon"></i>
                                <h5 class="card-title">Credit Payments by Deposit</h5>
                                <p class="card-text h3">&#8358;{{ number_format($depositCreditPayments, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card dashboard-card bg-danger text-white">
                            <div class="card-body">
                                <i class="fas fa-hand-holding-usd card-icon"></i>
                                <h5 class="card-title">Deposit Sales</h5>
                                <p class="card-text h3">&#8358;{{ number_format($depositSales, 0) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card dashboard-card bg-danger text-white">
                            <div class="card-body">
                                <i class="fas fa-cash-register card-icon"></i>
                                <h5 class="card-title">Cash Sales</h5>
                                <p class="card-text h3">&#8358;{{ number_format($cashSales - $cashReturns, 0) }}</p>
                                <p class="card-subtitle mb-2 small">
                                    {{ 'Sales: ' . number_format($cashSales, 0) . ', Returns: ' . number_format($cashReturns, 0) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card dashboard-card bg-danger text-white">
                            <div class="card-body">
                                <i class="fas fa-exchange-alt card-icon"></i>
                                <h5 class="card-title">Transfer Sales</h5>
                                <p class="card-text h3">&#8358;{{ number_format($transferSales - $transferReturns, 0) }}</p>
                                <p class="card-subtitle mb-2 small">
                                    {{ 'Sales: ' . number_format($transferSales, 0) . ', Returns: ' . number_format($transferReturns, 0) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card dashboard-card bg-danger text-white">
                            <div class="card-body">
                                <i class="fas fa-credit-card card-icon"></i>
                                <h5 class="card-title">POS Sales</h5>
                                <p class="card-text h3">&#8358;{{ number_format($posSales - $posReturns, 0) }}</p>
                                <p class="card-subtitle mb-2 small">
                                    {{ 'Sales: ' . number_format($posSales, 0) . ', Returns: ' . number_format($posReturns, 0) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    

                </div>
            </div>
            
            <style>
            .stats-section {
                background-color: #f8f9fa;
                border-radius: 15px;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .stats-title {
                color: #333;
                border-bottom: 2px solid #007bff;
                padding-bottom: 10px;
            }
            .stat-card {
                border-radius: 10px;
                overflow: hidden;
                transition: transform 0.3s ease-in-out;
            }
            .stat-card:hover {
                transform: translateY(-5px);
            }
            .stat-card-body {
                padding: 15px;
            }
            .stat-card-title {
                font-size: 1.1rem;
                font-weight: bold;
                margin-bottom: 5px;
            }
            .stat-card-text {
                font-size: 0.9rem;
                margin-bottom: 10px;
            }
            .stat-card-value {
                font-size: 1.5rem;
                font-weight: bold;
                margin-bottom: 0;
            }
            .bg-purple {
                background-color: #6f42c1;
            }
            .bg-teal {
                background-color: #20c997;
            }
            </style>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="chart-card">
                        <h5>Sales Over the last 7 Days</h5>
                        <canvas id="salesChart" width="400" height="230"></canvas>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="chart-card">
                        <h5>Today's Sales by the Time of the Day</h5>
                        <canvas id="salesByTimeChart" width="400" height="230"></canvas>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="chart-card">
                        <h5>Best Selling Items over the last 7 days</h5>
                        <canvas id="bestSellersChart" width="400" height="250"></canvas>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="chart-card">
                        <h5>Yesterday's Sales by Branches</h5>
                        <canvas id="salesByBranchChart" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="form-card">
                        <h5>Select Date Range</h5>
                        <form class="row g-3" action="{{ route('change_date') }}" method="POST" id="date_form">
                            @csrf
                            <div class="col-5">
                                <label for="date_type" class="form-label">Select Date Type</label>
                                <select class="form-control form-control-sm" id="date_type" name="date_type" required>
                                    <option value="single" {{ isset($date_type) && $date_type === 'single' ? 'selected' : '' }}>Single Date</option>
                                    <option value="range" {{ isset($date_type) && $date_type === 'range' ? 'selected' : '' }}>Date Range</option>
                                </select>
                            </div>
                            <div id="single_date_fields" class="col-5" style="{{ isset($date_type) && $date_type === 'single' ? 'display: block;' : 'display: none;' }}">
                                <label for="selected_date" class="form-label">Selected Date</label>
                                <input type="date" class="form-control form-control-sm" id="selected_date" name="selected_date" {{ isset($date_type) && $date_type === 'single' ? 'required' : '' }} value="{{ isset($selected_date) ? $selected_date : '' }}">
                            </div>
                            <div id="range_date_fields" class="col-5" style="{{ isset($date_type) && $date_type === 'range' ? 'display: block;' : 'display: none;' }}">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" {{ isset($date_type) && $date_type === 'range' ? 'required' : '' }} value="{{ isset($start_date) ? $start_date : '' }}">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" {{ isset($date_type) && $date_type === 'range' ? 'required' : '' }} value="{{ isset($end_date) ? $end_date : '' }}">
                            </div>
                            <div class="col-2">
                                <label class="invisible">Submit</label>
                                <button type="submit" class="btn btn-sm btn-primary text-white col-12">View Stats</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="form-card">
                        <h5>Select Branch</h5>
                        <form class="row g-3" action="{{ route('change_branch') }}" method="POST">
                            @csrf
                            <div class="col-8">
                                <label for="branch" class="form-label">Select Branch</label>
                                <select id="branch" name="branch_id" class="form-select form-select-sm" required>
                                    <option value=""></option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ auth()->user()->branch_id == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="invisible">Submit</label>
                                <button type="submit" class="btn btn-sm btn-info text-white col-12">Change</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var dateTypeSelect = document.getElementById('date_type');
        var singleDateFields = document.getElementById('single_date_fields');
        var rangeDateFields = document.getElementById('range_date_fields');

        function toggleDateFields() {
            if (dateTypeSelect.value === 'single') {
                singleDateFields.style.display = 'block';
                rangeDateFields.style.display = 'none';
                document.getElementById('selected_date').setAttribute('required', '');
                document.getElementById('start_date').removeAttribute('required');
                document.getElementById('end_date').removeAttribute('required');
            } else {
                singleDateFields.style.display = 'none';
                rangeDateFields.style.display = 'block';
                document.getElementById('selected_date').removeAttribute('required');
                document.getElementById('start_date').setAttribute('required', '');
                document.getElementById('end_date').setAttribute('required', '');
            }
        }

        toggleDateFields();
        dateTypeSelect.addEventListener('change', toggleDateFields);
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
<script>
    var ctx = document.getElementById('salesChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dates) !!},
            datasets: [{
                label: 'Sales Revenue',
                data: {!! json_encode($revenues) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>

<script>
    var ctx = document.getElementById('bestSellersChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Total Quantity Sold',
                data: {!! json_encode($values) !!},
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9933',
                    '#00CC99',
                    '#FF6666',
                    '#FFCC99',
                    '#6699FF'
                ]
            }]
        },
        options: {
            responsive: true,
            legend: {
                position: 'bottom'
            },
            title: {
                display: true,
                text: 'Top 10 Best Selling Items'
            }
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var chartData = @json($chartData);

        new Chart(document.getElementById('salesByTimeChart'), {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Sales',
                    data: chartData.data,
                    fill: false,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Time of Day (Hour)'
                        },
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1,
                            max: 23
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Amount'
                        }
                    }
                }
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var pieChartData = @json($pieChartData);

        var salesByBranchChart = new Chart(document.getElementById('salesByBranchChart'), {
            type: 'pie',
            data: {
                labels: pieChartData.labels,
                datasets: [{
                    data: pieChartData.data,
                    backgroundColor: pieChartData.backgroundColor
                }]
            },
            options: {}
        });
    });
</script>
@endsection