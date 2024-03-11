@extends('layouts.app')
@section('PageTitle', 'Home')
@section('css')
    <style>
        .card {
            margin-bottom: 15px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #f8f9fa;
            border: none;
            border-radius: 10px;
        }

        .card-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .card-text {
            font-size: 18px;
            color: #333;
            margin-bottom: 5px;
        }

        .card-description {
            font-size: 12px;
            color: #777;
        }

        .stats-section {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .stats-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .stats-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .stats-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ced4da;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .stats-label {
            font-size: 14px;
            font-weight: bold;
            margin-right: 10px;
        }

        .stats-value {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
@endsection
@section('content')
    <section id="content" style="background: rgb(240, 240, 240)">
        <div class="content-wrap">
            <div class="container">

                <div class="container">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Cash Balance</h5>
                                    <?php
                                    $result = $cashSales - ($cashExpenses + $cashReturns) + $cashCreditPayments + $cashDepositPayments - $cashCreditToday + $CreditPaymentSummary['cash'] + ($cashFundTransfer);
                                    $formattedResult = number_format($result);
                                    ?>

                                    <p class="card-text">&#8358;{{ $formattedResult }}</p>
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        {{ 'Sales: ' . $cashSales . ', Returns: ' . $cashReturns . ', Expenses: ' . $cashExpenses . ', Repayments: ' . $cashCreditPayments . ', Deposit ' . $cashDepositPayments . ', Cash Credit: ' . $cashCreditToday . ', CC Repayment: ' . $CreditPaymentSummary['cash'].', Funds Transfer '.$cashFundTransfer }}
                                    </h6>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Transfer Balance</h5>

                                    <?php
                                    $transferResult = $transferSales - ($transferExpenses + $transferReturns) + $transferCreditPayments + $transferDepositPayments + $CreditPaymentSummary['transfer'] + ($transferFundTransfer);
                                    $formattedTransferResult = number_format($transferResult);
                                    ?>

                                    <p class="card-text">&#8358;{{ $formattedTransferResult }}</p>

                                    <h6 class="card-subtitle mb-2 text-muted">
                                        {{ 'Sales: ' . $transferSales . ', Returns: ' . $transferReturns . ', Expenses: ' . $transferExpenses . ', Repayments: ' . $transferCreditPayments . ', Deposit ' . $transferDepositPayments . ', CC Repayment: ' . $CreditPaymentSummary['transfer'].', Funds Transfer '.$transferFundTransfer }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">POS Balance</h5>
                                    <?php
                                    $posResult = $posSales - ($posExpenses + $posReturns) + $posCreditPayments + $posDepositPayments + $CreditPaymentSummary['pos'] + ($posFundTransfer);
                                    $formattedPosResult = number_format($posResult);
                                    ?>

                                    <p class="card-text">&#8358;{{ $formattedPosResult }}</p>

                                    <h6 class="card-subtitle mb-2 text-muted">
                                        {{ 'Sales: ' . $posSales . ', Returns: ' . $posReturns . ', Expenses: ' . $posExpenses . ', Repayments: ' . $posCreditPayments . ', Deposit ' . $posDepositPayments . ', CC Repayment: ' . $CreditPaymentSummary['pos'].', Funds Transfer '.$posFundTransfer }}
                                    </h6>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="container mt-1">
                    <div class="stats-section">
                        <h3 class="stats-title">
                            @if (isset($start_date) && isset($end_date))
                            Stats for {{ \Carbon\Carbon::parse($start_date)->toFormattedDateString() }}
                            - {{ \Carbon\Carbon::parse($end_date)->toFormattedDateString() }}
                            ({{ \Carbon\Carbon::parse($start_date)->diffInDays($end_date) }} days apart)
                        @elseif (isset($selected_date))
                            Stats for {{ \Carbon\Carbon::parse($selected_date)->toFormattedDateString() }}
                        @else
                            Today's Stats &gt;&gt;&gt;
                        @endif
                        
                        </h3>
                        <div class="row">

                            <div class="col-md-6">
                                <ul class="iconlist fw-medium">

                                    <li class="border border-primary py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Gross Sales: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($grossSales, 0) }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>
                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Total Returns: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($totalReturn, 0) }}</span></span>
                                        <span style="margin-left: auto;">
                                            ({{ 'Cash: ' . number_format($cashReturns, 0) . ' POS: ' . number_format($posReturns, 0) . ' Trans: ' . number_format($transferReturns, 0) }})</span>
                                    </li>
                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Discounts: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($totalDiscounts - $returnDiscounts, 0) }}</span></span>
                                        <span
                                            style="margin-left: auto;">({{ 'Sales Discount: ' . number_format($totalDiscounts, 0) . ' Return Discount: ' . number_format($returnDiscounts, 0) }})
                                        </span>
                                    </li>
                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Expenses: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($totalExpenses, 0) }}</span></span>
                                        <span style="margin-left: auto;">
                                            ({{ 'Cash: ' . number_format($cashExpenses, 0) . ' POS: ' . number_format($posExpenses, 0) . ' Trans: ' . number_format($transferExpenses, 0) }})</span>
                                    </li>

                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Purchases: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($totalPurchases, 0) }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>
                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Estimates: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($totalEstimate, 0) }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>
                                    @php
                                        $owed = App\Models\User::select('balance')
                                            ->where('branch_id', auth()->user()->branch_id)
                                            ->sum('balance');
                                    @endphp
                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Total Credit: <span class="fw-bold"
                                                style="margin-left: 5px;">{{ number_format($owed, 0) }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>
                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Total Deposit Balance: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($deposits, 0) }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>
                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Today's Deposit: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($totalDepositPayments, 0) }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>
                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Walk-in Count: <span class="fw-bold"
                                                style="margin-left: 5px;">{{ number_format($uniqueSalesCount, 0) }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>
                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Awaiting Pickup: <span class="fw-bold"
                                                style="margin-left: 5px;">{{ number_format(@$uncollectedSales->count(), 0) }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>
                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Total Cash Credit Balance Remaining: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format(@$TotalcashCredit, 0) }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>

                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="iconlist fw-medium">

                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Number of Item Sold: <span class="fw-bold"
                                                style="margin-left: 5px;">{{ number_format($totalItemsSold, 0) }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>
                                    <li class="border border-danger py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Credit Sales: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($creditSales, 0) }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>
                                    <li class="border border-danger py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Deposit Sales: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($depositSales, 0) }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>

                                    <li class="border border-danger py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Cash Sales: <span class="fw-bold" style="margin-left: 5px;">
                                                &#8358;{{ number_format($cashSales - $cashReturns, 0) }}</span></span>
                                        <span
                                            style="margin-left: auto;">({{ 'Sales: ' . number_format($cashSales, 0) . ' Returns: ' . number_format($cashReturns, 0) }})
                                        </span>
                                    </li>

                                    <li class="border border-danger py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>POS Sales: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($posSales - $posReturns, 0) }}</span></span>
                                        <span
                                            style="margin-left: auto;">({{ 'Sales: ' . number_format($posSales, 0) . ' Returns: ' . number_format($posReturns, 0) }})
                                        </span>
                                    </li>

                                    <li class="border border-danger py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Transfer Sales: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($transferSales - $transferReturns, 0) }}
                                            </span></span>
                                        <span
                                            style="margin-left: auto;">({{ 'Sales: ' . number_format($transferSales, 0) . ' Returns: ' . number_format($transferReturns, 0) }})
                                        </span>
                                    </li>
                                    <li class="border border-danger py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Net Sales: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($grossSales - $totalDiscount - ($totalReturn - $returnDiscounts), 0) }}</span></span>
                                        <span
                                            style="margin-left: auto;">({{ 'Gross Sale: ' . number_format($grossSales, 0) . ' Sales Discount: ' . number_format($totalDiscount, 0) . ' Total Return: ' . number_format($totalReturn, 0) . ' Return Discount: ' . number_format($returnDiscounts, 0) }})
                                        </span>
                                    </li>
                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Credit Payments: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($totalCreditPayments, 0) }}</span></span>
                                        <span style="margin-left: auto;">
                                            ({{ 'Cash: ' . number_format($cashCreditPayments, 0) . ' POS: ' . number_format($posCreditPayments, 0) . ' Trans: ' . number_format($transferCreditPayments, 0) }})</span>
                                    </li>

                                    <li class="border border-danger py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Gross Profit: <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($grossProfit - $totalDiscounts - ($returnProfit - $returnDiscounts), 0) }}</span></span>
                                        <span
                                            style="margin-left: auto;">({{ 'Sales Profit: ' . number_format($grossProfit, 0) . ' Sales Discount: ' . number_format($totalDiscount, 0) . ' Return Profit: ' . number_format($returnProfit, 0) . ' Return Discount: ' . number_format($returnDiscounts, 0) }})
                                        </span>
                                    </li>
                                    <li class="border border-danger py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Net Profit(approx.): <span class="fw-bold"
                                                style="margin-left: 5px;">&#8358;{{ number_format($grossProfit - $totalDiscounts - ($returnProfit - $returnDiscounts) - $totalExpenses, 0) }}</span></span>
                                        <span
                                            style="margin-left: auto;">({{ 'Sales Profit: ' . number_format($grossProfit, 0) . ' Sales Discount: ' . number_format($totalDiscount, 0) . ' Return Profit: ' . number_format($returnProfit, 0) . ' Return Discount: ' . number_format($returnDiscounts, 0) . ' Expenses: ' . number_format($totalExpenses, 0)}})
                                        </span>
                                    </li>
                                    <li class="border border-success py-2 px-3 rounded mb-2"
                                        style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>Low Stock Counts: <span class="fw-bold"
                                                style="margin-left: 5px;">{{ $lows . ' of ' . $total_stock }}</span></span>
                                        <span style="margin-left: auto;"></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row col-mb-50 mb-0">
                        <div class="col-md-6">
                            <h5>Sales Over the last 7 Days</h5>
                            <canvas id="salesChart" width="400" height="230"></canvas>

                        </div>

                        <div class="col-md-6">
                            <h5>Today's Sales by the Time of the Day</h5>
                            <canvas id="salesByTimeChart" width="400" height="230"></canvas>

                        </div>
                    </div>

                    <div class="row col-mb-50 mb-0">
                        <div class="col-md-6">
                            <h5>Best Selling Items over the last 7 days</h5>
                            <canvas id="bestSellersChart" width="400" height="250"></canvas>
                        </div>

                        <div class="col-md-6">
                            <h5>Yesterday's Sales by Branches</h5>
                            <canvas id="salesByBranchChart" width="400" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row">
                      
                        <div class="col-md-8">
                            <form class="row" action="{{ route('change_date') }}" method="POST" id="date_form">
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
                        
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                var dateTypeSelect = document.getElementById('date_type');
                                var singleDateFields = document.getElementById('single_date_fields');
                                var rangeDateFields = document.getElementById('range_date_fields');
                        
                                // Function to show/hide fields based on selected date type
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
                        
                                // Initial invocation
                                toggleDateFields();
                        
                                // Event listener for date type change
                                dateTypeSelect.addEventListener('change', toggleDateFields);
                            });
                        </script>
                        
                        

                        <div class="col-md-4">
                            <form class="row" action="{{ route('change_branch') }}" method="POST">
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
                                    <button type="submit" class="btn btn-sm btn-info text-white col-12">Change
                                        Branch</button>
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
