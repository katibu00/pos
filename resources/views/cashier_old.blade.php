@extends('layouts.app')
@section('PageTitle', 'Home')
@section('content')
    <section id="content" style="background: rgb(240, 240, 240)">
        <div class="content-wrap">


            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Cash Balance</h5>
                                <?php
                                $cashResult = $cashSales - ($cashExpenses + $cashReturns) + $cashCreditPayments + $cashDepositPayments - $cashCreditToday + $CreditPaymentSummary['cash'];
                                $formattedCashResult = number_format($cashResult);
                                ?>

                                <p class="card-text">&#8358;{{ $formattedCashResult }}</p>

                                <h6 class="card-subtitle mb-2 text-muted">
                                    {{ 'Sales: ' . $cashSales . ' Returns: ' . $cashReturns . ' Expenses: ' . $cashExpenses . ' Repayments: ' . $cashCreditPayments . ' Deposit ' . $cashDepositPayments . ' Cash Credit: ' . $cashCreditToday . ' CCP: ' . $CreditPaymentSummary['cash'] }}
                                </h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Transfer Balance</h5>
                                <?php
                                $transferResult = $transferSales - ($transferExpenses + $transferReturns) + $transferCreditPayments + $transferDepositPayments + $CreditPaymentSummary['transfer'];
                                $formattedTransferResult = number_format($transferResult);
                                ?>

                                <p class="card-text">&#8358;{{ $formattedTransferResult }}</p>

                                <h6 class="card-subtitle mb-2 text-muted">
                                    {{ 'Sales: ' . $transferSales . ' Returns: ' . $transferReturns . ' Expenses: ' . $transferExpenses . ' Repayments: ' . $transferCreditPayments . ' Deposit ' . $transferDepositPayments . ' CCP: ' . $CreditPaymentSummary['transfer'] }}
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">POS Balance</h5>
                                <?php
                                $posResult = $posSales - ($posExpenses + $posReturns) + $posCreditPayments + $posDepositPayments + $CreditPaymentSummary['pos'];
                                $formattedPosResult = number_format($posResult);
                                ?>

                                <p class="card-text">&#8358;{{ $formattedPosResult }}</p>

                                <h6 class="card-subtitle mb-2 text-muted">
                                    {{ 'Sales: ' . $posSales . ' Returns: ' . $posReturns . ' Expenses: ' . $posExpenses . ' Repayments: ' . $posCreditPayments . ' Deposit ' . $posDepositPayments . ' CCP: ' . $CreditPaymentSummary['pos'] }}
                                </h6>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <div class="container mt-2">

                <div class="row col-mb-50 mb-0">
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
                                <span>Credit Payments: <span class="fw-bold"
                                        style="margin-left: 5px;">&#8358;{{ number_format($totalCreditPayments, 0) }}</span></span>
                                <span style="margin-left: auto;">
                                    ({{ 'Cash: ' . number_format($cashCreditPayments, 0) . ' POS: ' . number_format($posCreditPayments, 0) . ' Trans: ' . number_format($transferCreditPayments, 0) }})</span>
                            </li>

                            <li class="border border-success py-2 px-3 rounded mb-2"
                                style="display: flex; justify-content: space-between; align-items: center;">
                                <span>Estimates: <span class="fw-bold"
                                        style="margin-left: 5px;">&#8358;{{ number_format($totalEstimate, 0) }}</span></span>
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
                                <span>Today's Deposit: <span class="fw-bold"
                                        style="margin-left: 5px;">&#8358;{{ number_format($totalDepositPayments, 0) }}</span></span>
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
                            <li class="border border-success py-2 px-3 rounded mb-2"
                                style="display: flex; justify-content: space-between; align-items: center;">
                                <span>Credit Payments: <span class="fw-bold"
                                        style="margin-left: 5px;">&#8358;{{ number_format($totalCreditPayments, 0) }}</span></span>
                                <span style="margin-left: auto;">
                                    ({{ 'Cash: ' . number_format($cashCreditPayments, 0) . ' POS: ' . number_format($posCreditPayments, 0) . ' Trans: ' . number_format($transferCreditPayments, 0) }})</span>
                            </li>
                            <li class="border border-danger py-2 px-3 rounded mb-2"
                                style="display: flex; justify-content: space-between; align-items: center;">
                                <span>Net Sales: <span class="fw-bold"
                                        style="margin-left: 5px;">&#8358;{{ number_format($grossSales - $totalDiscount - $totalReturn - $returnDiscounts, 0) }}</span></span>
                                <span
                                    style="margin-left: auto;">({{ 'Gross Sale: ' . number_format($grossSales, 0) . ' Sales Discount: ' . number_format($totalDiscount, 0) . ' Total Return: ' . number_format($totalReturn, 0) . ' Return Discount: ' . number_format($returnDiscounts, 0) }})
                                </span>
                            </li>
                            <li class="border border-danger py-2 px-3 rounded mb-2"
                                style="display: flex; justify-content: space-between; align-items: center;">
                                <span>Cash Sales: <span class="fw-bold" style="margin-left: 5px;">
                                        &#8358;{{ number_format($cashSales - ($cashExpenses + $cashReturns), 0) }}</span></span>
                                <span
                                    style="margin-left: auto;">({{ 'Sales: ' . number_format($cashSales, 0) . ' Returns: ' . number_format($cashReturns, 0) }})
                                </span>
                            </li>

                            <li class="border border-danger py-2 px-3 rounded mb-2"
                                style="display: flex; justify-content: space-between; align-items: center;">
                                <span>POS Sales: <span class="fw-bold"
                                        style="margin-left: 5px;">&#8358;{{ number_format($posSales - ($posExpenses + $posReturns), 0) }}</span></span>
                                <span
                                    style="margin-left: auto;">({{ 'Sales: ' . number_format($posSales, 0) . ' Returns: ' . number_format($posReturns, 0) }})
                                </span>
                            </li>

                            <li class="border border-danger py-2 px-3 rounded mb-2"
                                style="display: flex; justify-content: space-between; align-items: center;">
                                <span>Transfer Sales: <span class="fw-bold"
                                        style="margin-left: 5px;">&#8358;{{ number_format($transferSales - ($transferExpenses + $transferReturns), 0) }}
                                    </span></span>
                                <span
                                    style="margin-left: auto;">({{ 'Sales: ' . number_format($transferSales, 0) . ' Returns: ' . number_format($transferReturns, 0) }})
                                </span>
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
                            <li class="border border-success py-2 px-3 rounded mb-2"
                                style="display: flex; justify-content: space-between; align-items: center;">
                                <span>Low Stock Counts: <span class="fw-bold"
                                        style="margin-left: 5px;">{{ $lows . ' of ' . $total_stock }}</span></span>
                                <span style="margin-left: auto;"></span>
                            </li>
                        </ul>
                    </div>
                </div>


                <div class="row col-mb-50 mb-0">
                    <div class="col-md-6">
                        <h5>Today's Sales by the Time of the Day</h5>
                        <canvas id="salesByTimeChart" width="400" height="250"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h5>Best Selling Items over the last 7 days</h5>
                        <canvas id="bestSellersChart" width="400" height="250"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection


@section('js')

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>


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





@endsection
