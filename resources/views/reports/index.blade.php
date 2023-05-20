@extends('layouts.app')
@section('PageTitle', 'Business Report')

@section('css')
    <link rel="stylesheet" href="/assets/styles/vendor/pickadate/classic.css">
    <link rel="stylesheet" href="/assets/styles/vendor/pickadate/classic.date.css">
@endsection

@section('content')

    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <div class="card text-left">
                            <div class="card-header ">
                                <h5>Sales Report</h5>
                            </div>
                            <div class="card-body">

                                <form action="{{ route('report.generate') }}" method="post">
                                    @csrf
                                    <div class="row row-xs">
                                        <div class="col-md-3" id="branch_div">
                                            <label>Branch</label>
                                            <select class="form-select mb-2" id="branch_id" name="branch_id">
                                                <option value=""></option>
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}"
                                                        @if (@$branch_id == $branch->id) selected @endif>
                                                        {{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Report Type</label>
                                            <select class="form-select mb-2" id="report" name="report" required>
                                                <option></option>
                                                <option value="today" @if (@$report == 'today') selected @endif>
                                                    Today's Report
                                                </option>
                                                <option value="general" @if (@$report == 'general') selected @endif>
                                                    General Report</option>
                                                <option value="best_selling"
                                                    @if (@$report == 'best_selling') selected @endif>Best Selling Items
                                                </option>
                                                <option value="worst_selling"
                                                    @if (@$report == 'worst_selling') selected @endif>Worst Selling Items
                                                </option>
                                                <option value="inventory" @if (@$report == 'inventory') selected @endif>
                                                    By Inventory
                                                </option>
                                                <option value="compare_graphs"
                                                    @if (@$report == 'compare_graphs') selected @endif>
                                                    Compare Branches (Graphically)
                                                </option>
                                                <option value="compare_branches"
                                                    @if (@$report == 'compare_branches') selected @endif>
                                                    Compare Branches (Tabular)
                                                </option>
                                                <option value="best_customers"
                                                    @if (@$report == 'best_customers') selected @endif>
                                                    Top Customers
                                                </option>
                                                <option value="best_debtors"
                                                    @if (@$report == 'best_debtors') selected @endif>
                                                    Top Debtors
                                                </option>


                                            </select>
                                        </div>
                                        <div class="col-md-3 d-none" id="inventory_div">
                                            <label>Inventory</label>
                                            <select class="form-select mb-2" multiple id="inventory_id"
                                                name="inventory_id[]">
                                                <option>Loading...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 d-none" id="duration_div">
                                            <label>Duration</label>
                                            <select class="form-select mb-2" id="duration" name="duration">
                                                <option value=""></option>
                                                <option value="5" @if (@$duration == 5) selected @endif>
                                                    Last 5 Days{{ @$duration }}</option>
                                                <option value="10" @if (@$duration == 10) selected @endif>
                                                    Last 10 Days</option>
                                                <option value="30" @if (@$duration == 30) selected @endif>
                                                    Last 30 Days</option>
                                                <option value="50" @if (@$duration == 50) selected @endif>
                                                    Last 50 Days</option>
                                                <option value="100" @if (@$duration == 100) selected @endif>
                                                    Last 100 Days</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3" id="time_div">
                                            <label>Time</label>
                                            <select class="form-select mb-2" id="date" name="date">
                                                <option></option>
                                                <option value="today" @if (@$date == 'today') selected @endif>
                                                    Today</option>
                                                <option value="week" @if (@$date == 'week') selected @endif>
                                                    This Week
                                                </option>
                                                <option value="month" @if (@$date == 'month') selected @endif>
                                                    This Month
                                                </option>
                                                <option value="range" @if (@$date == 'range') selected @endif>
                                                    Date Range
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 d-none" id="amount_div">
                                            <label>Number</label>
                                            <select class="form-select mb-2" id="amount" name="amount">
                                                <option></option>
                                                <option value="10" @if (@$amount == 10) selected @endif>
                                                    10
                                                </option>
                                                <option value="20" @if (@$amount == 20) selected @endif>
                                                    20
                                                </option>
                                                <option value="50" @if (@$amount == 50) selected @endif>
                                                    50</option>
                                                <option value="100" @if (@$amount == 100) selected @endif>
                                                    100</option>

                                            </select>
                                        </div>

                                        <div class="col-md-5 form-group mb-3 d-none" id="date1">
                                            <label>Start Date</label>
                                            <input type="date" class="form-control" value="{{ @$start_date }}"
                                                name="start_date">
                                        </div>

                                        <div class="col-md-5 form-group mb-3  d-none" id="date2">
                                            <label>End Date</label>
                                            <input type="date" class="form-control" value="{{ @$end_date }}"
                                                name="end_date">
                                        </div>

                                        <div class="col-md-2 mt-4">
                                            <button class="btn btn-primary btn-block">Get Report</button>
                                        </div>
                                    </div>
                                </form>

                                @if (isset($sales))

                                    <div class="table-responsive my-5">

                                        <table class="table table-striped table-bordered text-left">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th scope="col" class="text-center">S/N</th>
                                                    <th scope="col">Ref ID</th>
                                                    <th scope="col">Item</th>
                                                    <th scope="col">Price (&#8358;)</th>
                                                    <th scope="col">Qty Sold</th>
                                                    <th scope="col">Amount (&#8358;)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total = 0;
                                                    $items_sold = 0;
                                                    $gross_margin = 0;
                                                @endphp
                                                @foreach ($sales as $key => $sale)
                                                    @php
                                                        
                                                        $spending = 0;
                                                        $margin = 0;
                                                        
                                                        $items = App\Models\Sale::where('receipt_no', $sale->receipt_no)->get();
                                                        foreach ($items as $item) {
                                                            $spending += $item['product']['selling_price'] * $item->quantity;
                                                            $margin += $item['product']['buying_price'] * $item->quantity;
                                                        }
                                                        $total += $spending;
                                                        $gross_margin += $margin;
                                                        // $items_sold += $item->quantity;
                                                    @endphp

                                                    @foreach ($items as $key2 => $item)
                                                        <tr>

                                                            @if ($loop->first)
                                                                <td class="text-center">{{ $key + 1 }}</td>
                                                                <td>{{ $sale->receipt_no }}</td>
                                                            @else
                                                                <td></td>
                                                                <td></td>
                                                            @endif

                                                            <td>{{ $key2 + 1 }}. {{ $item['product']['name'] }}</td>
                                                            <td>{{ number_format($item['product']['selling_price'], 0) }}
                                                            </td>
                                                            <td>{{ number_format($item->quantity, 0) }}</td>
                                                            <td>{{ number_format($item['product']['selling_price'] * $item->quantity, 0) }}
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $items_sold += $item->quantity;
                                                        @endphp
                                                    @endforeach
                                                    <tr>

                                                        <td colspan="4"></td>
                                                        <td class="text-right">Sub Total</td>
                                                        <td>&#8358;{{ number_format($spending, 0) }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>

                                                    <td colspan="4"></td>
                                                    <td class="text-right"><strong>Grand Total</strong></td>
                                                    <td><strong>&#8358;{{ number_format($total, 0) }}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>


                                    <div class="table-responsive my-5">
                                        <h4>Summary</h4>
                                        <table class="table table-striped table-bordered text-left col-md-4">
                                            <thead class="thead-dalrk">

                                                <tr>
                                                    <th scope="col">No. of Transactions</th>
                                                    <td>{{ number_format($sales->count(), 0) }}</th>
                                                </tr>
                                                <tr>
                                                    <th scope="col">No. of Items Sold</th>
                                                    <td>{{ number_format($items_sold, 0) }}</th>
                                                </tr>
                                                <tr>
                                                    <th scope="col">Gross Revenue</th>
                                                    <td>&#8358;{{ number_format($total, 0) }}</th>
                                                </tr>
                                                <tr>
                                                    <th scope="col">Margin</th>
                                                    <td>&#8358;{{ number_format($total - $gross_margin, 0) }}</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                @endif



                                @if (@$report == 'general')
                                    <h3>General Report</h3>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <tbody class="thead-dark">
                                                                <tr>
                                                                    <th>Gross Revenue</th>
                                                                    <td class="text-center">
                                                                        &#8358;{{ number_format($total_sales_value, 0) }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Sales Discount</th>
                                                                    <td class="text-center">
                                                                        &#8358;{{ number_format($total_discount, 0) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total Expenses</th>
                                                                    <td class="text-center">
                                                                        &#8358;{{ number_format($total_expenses_value, 0) }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total Expense Count</th>
                                                                    <td class="text-center">
                                                                        {{ number_format($total_expenses_count, 0) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total Returns</th>
                                                                    <td class="text-center">
                                                                        &#8358;{{ number_format($total_returns_value, 0) }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total Returns Discount</th>
                                                                    <td class="text-center">
                                                                        &#8358;{{ number_format($total_returns_discount, 0) }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total Returns Profit</th>
                                                                    <td class="text-center">
                                                                        &#8358;{{ number_format($returns_profit, 0) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total Payments</th>
                                                                    <td class="text-center">
                                                                        &#8358;{{ number_format($total_payments_value, 0) }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Stock Value</th>
                                                                    <td class="text-center">
                                                                        &#8358;{{ number_format($stock_value, 0) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Gross Sales Profit</th>
                                                                    <td class="text-center">
                                                                        &#8358;{{ number_format($gross_sales_profit, 0) }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Net Sales Profit</th>
                                                                    <td class="text-center">
                                                                        &#8358;{{ number_format($gross_sales_profit - $returns_profit - $total_discount + $total_returns_discount - $total_expenses_count, 0) }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total Credit Owed</th>
                                                                    <td class="text-center">
                                                                        &#8358;{{ number_format($totalCreditsOwed, 0) }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="card">
                                                <div class="card-body">
                                                    <canvas id="salesChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif



                                @if (@$report == 'best_selling')
                                    <h3>Best Selling Items Report</h3>
                                    <div>
                                        <canvas id="best-selling-chart"></canvas>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody class="thead-dark">
                                                <thead>
                                                    <tr>
                                                        <th>S/N</th>
                                                        <th>Item Name</th>
                                                        <th>Quantity Sold</th>
                                                        <th>Revenue Generated (&#8358;)</th>
                                                        <th>Percentage of Total Sales</th>
                                                    </tr>
                                                </thead>
                                            <tbody>
                                                @foreach ($bestSellingItems as $key => $item)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $item->product->name }}</td>
                                                        <td>{{ number_format($item->total_quantity, 0) }}</td>
                                                        <td>{{ number_format($item->total_quantity * $item->product->selling_price, 0) }}
                                                        </td>
                                                        <td>{{ number_format($item->percentage_of_total_sales, 2) }}%</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                @endif


                                @if (@$report == 'inventory')

                                    <h3>Inventory Items</h3>
                                    <div>
                                        <canvas id="chart-inventory" width="400" height="300"></canvas>
                                    </div>

                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Inventory Item Name</th>
                                                <th>Ending Inventory Quantity</th>
                                                <th>Total Quantity Sold</th>
                                                <th>Sales Revenue (&#8358;)</th>
                                                <th>Average Selling Price (&#8358;)</th>
                                                <th>Gross Profit (&#8358;)</th>
                                                <th>Gross Profit Margin</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($inventoryItems as $key => $item)
                                                <tr class="text-center">
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $item->name }}</td>
                                                    <td>{{ number_format($item->quantity, 0) }}</td>
                                                    <td>{{ number_format($item->total_quantity_sold, 0) }}</td>
                                                    <td>{{ number_format($item->sales_revenue, 0) }}</td>
                                                    <td>{{ $item->sales_revenue != 0 ? $item->sales_revenue / $item->total_quantity_sold : 0 }}
                                                    </td>
                                                    <td>{{ number_format($item->gross_profit, 0) }}</td>
                                                    <td>{{ number_format($item->profit_margin, 2) }}%</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                @endif


                                @if (@$report == 'worst_selling')
                                    <h3>Worst Selling Items</h3>
                                    <div>
                                        <canvas id="chart-worst-selling" width="400" height="400"></canvas>
                                    </div>
                                    <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Item Name</th>
                                                <th>Total Quantity Sold (&#8358;)</th>
                                                <th>Percentage of Total Sales (&#8358;)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($worstSellingItems as $key => $item)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ @$item->product->name }}</td>
                                                    <td>{{ number_format(@$item->total_quantity, 0) }}</td>
                                                    <td>{{ number_format(@$item->percentage_of_total_sales, 2) }}%</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                @endif


                                @if (@$report == 'compare_branches')


                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Branch</th>
                                                    <th>Gross Sales (&#8358;)</th>
                                                    <th>Expenses (&#8358;)</th>
                                                    <th>Returns (&#8358;)</th>
                                                    <th>Credits Owed (&#8358;)</th>
                                                    <th>Discounts (&#8358;)</th>
                                                    <th>Net Profit (&#8358;)</th>
                                                    <th>Avg Transaction Value (&#8358;)</th>
                                                    <th>Inventory Turnover (&#8358;)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($branches as $branch)
                                                    <tr>
                                                        <td>{{ $branch->name }}</td>
                                                        <td>{{ number_format($grossSales[$branch->id], 0) }}</td>
                                                        <td>{{ number_format($expenses[$branch->id], 0) }}</td>
                                                        <td>{{ number_format($returns[$branch->id], 0) }}</td>
                                                        <td>{{ number_format($creditsOwed[$branch->id], 0) }}</td>
                                                        <td>{{ number_format($discounts[$branch->id], 0) }}</td>
                                                        <td>{{ number_format($netProfit[$branch->id], 0) }}</td>
                                                        <td>{{ number_format($avgTransactionValue[$branch->id], 0) }}</td>
                                                        <td>{{ number_format($inventoryTurnover[$branch->id], 0) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                @endif


                                @if (@$report == 'compare_graphs')
                                    <div class="container">
                                        <h1>Branch Metrics Comparison</h1>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3>Gross Sales:</h3>
                                                <canvas id="grossSalesChart" width="400" height="200"></canvas>
                                            </div>
                                            <div class="col-md-6">
                                                <h3>Net Profit:</h3>
                                                <canvas id="netProfitChart" width="400" height="200"></canvas>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3>Expenses:</h3>
                                                <div style="width: 100%; height: 400px;">
                                                    <canvas id="expensesChart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h3>Credits Owed:</h3>
                                                <div style="width: 100%; height: 400px;">
                                                    <canvas id="creditsOwedChart"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3>Discounts:</h3>
                                                <div style="width: 100%; height: 400px;">
                                                    <canvas id="discountsChart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h3>Stocks Value Left:</h3>
                                                <div style="width: 100%; height: 400px;">
                                                    <canvas id="stock-chart"></canvas>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                @endif


                                @if (@$report == 'best_customers')

                                    <h1>Top Customers</h1>
                                    <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Customer</th>
                                                <th>Total Purchases (&#8358;)</th>
                                                <th>Total Payments (&#8358;)</th>
                                                <th>Total Discounts (&#8358;)</th>
                                                <th>Balance (&#8358;)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rankedCustomers as $key => $rankedCustomer)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $rankedCustomer['customer']->first_name }}
                                                        {{ $rankedCustomer['customer']->last_name }}</td>
                                                    <td>{{ number_format($rankedCustomer['total_purchases'], 0) }}</td>
                                                    <td>{{ number_format($rankedCustomer['total_payments'], 0) }}</td>
                                                    <td>{{ number_format($rankedCustomer['total_discounts'], 0) }}</td>
                                                    <td>{{ number_format($rankedCustomer['balance'], 0) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>

                                @endif


                                @if (@$report == 'best_debtors')

                                    <h1>Top Debtors</h1>
                                    <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Customer</th>
                                                <th>Balance (&#8358;)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($debtors as $key => $debtor)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $debtor->first_name }} {{ $debtor->last_name }}</td>
                                                    <td>{{ number_format($debtor->balance,0) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ============ Body content End ============= -->
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script type="text/javascript">
        $(function() {
            $(document).on('change', '#date', function() {

                var date = $('#date').val();

                if (date === 'range') {
                    $('#date1').removeClass('d-none');
                    $('#date2').removeClass('d-none');
                } else {
                    $('#date1').addClass('d-none');
                    $('#date2').addClass('d-none');
                }

            });
            $(document).on('change', '#report', function() {

                var report = $('#report').val();

                $('#date1').addClass('d-none');
                $('#date2').addClass('d-none');

                if (report === 'today') {
                    $('#time_div').addClass('d-none');
                    $('#date').removeAttr('required');
                    $('#amount_div').addClass('d-none');
                    $('#inventory_div').addClass('d-none');
                    $('#duration_div').addClass('d-none');
                    $('#branch_div').removeClass('d-none');
                    $('#branch_id').addAttr('required');
                }
                if (report === 'general' || report === 'best_customers') {
                    $('#time_div').removeClass('d-none');
                    $('#date').removeAttr('required');
                    $('#amount_div').addClass('d-none');
                    $('#inventory_div').addClass('d-none');
                    $('#inventory_div').addClass('d-none');
                    $('#time_div').removeClass('d-none');
                    $('#date').addAttr('required');
                    $('#duration_div').addClass('d-none');
                    $('#branch_div').removeClass('d-none');
                    $('#branch_id').addAttr('required');

                }
                if (report === 'compare_graphs' || report === 'compare_branches') {
                    $('#time_div').addClass('d-none');
                    $('#date').removeAttr('required');
                    $('#amount_div').addClass('d-none');
                    $('#inventory_div').addClass('d-none');
                    $('#inventory_div').addClass('d-none');
                    $('#branch_div').addClass('d-none');
                    $('#duration_div').removeClass('d-none');
                    $('#duration').addAttr('required');
                }

                if (report === 'best_selling' || report === 'worst_selling') {
                    $('#branch_div').removeClass('d-none');
                    $('#amount_div').removeClass('d-none');
                    $('#inventory_div').addClass('d-none');
                    $('#time_div').removeClass('d-none');
                    $('#duration_div').addClass('d-none');



                }
                if (report === 'inventory') {
                    $('#branch_div').removeClass('d-none');
                    $('#time_div').removeClass('d-none');
                    $('#amount_div').addClass('d-none');
                    $('#inventory_div').removeClass('d-none');
                    $('#time_div').removeClass('d-none');
                    $('#duration_div').addClass('d-none');

                    ////

                    var branchId = $('select[name="branch_id"]').val();
                    if (branchId === '') {
                        alert('Please Select a branch and Try again.');
                        return;
                    }

                    $.ajax({
                        url: '/fetch_stocks',
                        type: 'GET',
                        data: {
                            branch_id: branchId
                        },
                        success: function(response) {

                            var select = $('#inventory_id');
                            select.append('<option>Loading...</option>');
                            select.empty();
                            $.each(response, function(index, stock) {
                                select.append('<option value="' + stock.id + '">' +
                                    stock.name + '</option>');
                            });;
                        }
                    });

                    ////
                }

            });
        });
    </script>
    @if (@$date == 'range')
        <script type="text/javascript">
            $('#date1').removeClass('d-none');
            $('#date2').removeClass('d-none');
        </script>
    @endif
    @if (@$report == 'best_selling' || @$report == 'worst_selling')
        <script type="text/javascript">
            $('#amount_div').removeClass('d-none');
        </script>
    @endif
    @if (@$report == 'inventory')
        <script type="text/javascript">
            $('#inventory_div').removeClass('d-none');
        </script>
    @endif
    @if (@$report == 'compare_graphs' || @$report == 'compare_branches')
        <script type="text/javascript">
            $('#time_div').addClass('d-none');
            $('#date').removeAttr('required');
            $('#amount_div').addClass('d-none');
            $('#inventory_div').addClass('d-none');
            $('#inventory_div').addClass('d-none');
            $('#branch_div').addClass('d-none');
            $('#branch_id').removeAttr('required');
            $('#duration_div').removeClass('d-none');
            $('#duration').addAttr('required');
        </script>
    @endif

    @if (@$report == 'best_selling')
        <script>
            // Data passed from the controller
            var itemNames = {!! json_encode($itemNames) !!};
            var quantitiesSold = {!! json_encode($quantitiesSold) !!};

            // Chart configuration
            var ctx = document.getElementById('best-selling-chart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: itemNames,
                    datasets: [{
                        label: 'Quantity Sold',
                        data: quantitiesSold,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            maxRotation: 90,
                            minRotation: 90
                        }
                    }
                }
            });
        </script>
    @endif


    @if (@$report == 'general')
        <script>
            var salesChart = new Chart(document.getElementById('salesChart'), {
                type: 'bar',
                data: {
                    labels: ['Gross Revenue', 'Sales Discount', 'Total Returns', 'Total Payments'],
                    datasets: [{
                        label: 'Amount',
                        data: [
                            {{ $total_sales_value }},
                            {{ $total_discount }},
                            {{ $total_returns_value }},
                            {{ $total_payments_value }}
                        ],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Sales Metrics',
                            font: {
                                size: 16
                            }
                        }
                    }
                }
            });
        </script>
    @endif


    @if (@$report == 'inventory')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var ctx = document.getElementById('chart-inventory').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [
                            @for ($month = 1; $month <= 12; $month++)
                                '{{ \Carbon\Carbon::parse("{$year}-{$month}")->format('F') }}',
                            @endfor
                        ],
                        datasets: [
                            @foreach ($datas as $item)
                                {
                                    label: '{{ $item['inventoryName'] }}',
                                    data: [
                                        @for ($month = 1; $month <= 12; $month++)
                                            {{ $item['inventoryData'][$month] ?? 0 }},
                                        @endfor
                                    ],
                                    borderColor: getRandomColor(),
                                    borderWidth: 1,
                                    fill: false,
                                },
                            @endforeach
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                precision: 0,
                            }
                        }
                    }
                });
            });

            function getRandomColor() {
                var letters = '0123456789ABCDEF';
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }
        </script>
    @endif

    @if (@$report == 'worst_selling')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var ctx = document.getElementById('chart-worst-selling').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! $itemNames !!},
                        datasets: [{
                            label: 'Total Quantity Sold',
                            data: {!! $quantitiesSold !!},
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                precision: 0
                            }
                        }
                    }
                });
            });
        </script>
    @endif


    @if (@$report == 'compare_graphs')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Retrieve the metrics data from the PHP variable
                var metrics = @json($metrics);

                // Prepare the data for the charts
                var branchLabels = metrics.map(function(metric) {
                    return metric.branch.name;
                });

                var grossSalesData = metrics.map(function(metric) {
                    return metric.grossSales;
                });

                var netProfitData = metrics.map(function(metric) {
                    return metric.netProfit;
                });

                var expensesData = metrics.map(function(metric) {
                    return metric.expenses;
                });

                var creditsOwedData = metrics.map(function(metric) {
                    return metric.creditsOwed;
                });

                var discountsData = metrics.map(function(metric) {
                    return metric.discounts;
                });

                // Create the charts
                var grossSalesChart = new Chart(document.getElementById('grossSalesChart'), {
                    type: 'line',
                    data: {
                        labels: branchLabels,
                        datasets: [{
                            label: 'Gross Sales',
                            data: grossSalesData,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                var netProfitChart = new Chart(document.getElementById('netProfitChart'), {
                    type: 'line',
                    data: {
                        labels: branchLabels,
                        datasets: [{
                            label: 'Net Profit',
                            data: netProfitData,
                            backgroundColor: 'rgba(255, 159, 64, 0.2)',
                            borderColor: 'rgba(255, 159, 64, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                var expensesChart = new Chart(document.getElementById('expensesChart'), {
                    type: 'pie',
                    data: {
                        labels: branchLabels,
                        datasets: [{
                            label: 'Expenses',
                            data: expensesData,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {}
                });

                var creditsOwedChart = new Chart(document.getElementById('creditsOwedChart'), {
                    type: 'pie',
                    data: {
                        labels: branchLabels,
                        datasets: [{
                            label: 'Credits Owed',
                            data: creditsOwedData,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {}
                });

                var discountsChart = new Chart(document.getElementById('discountsChart'), {
                    type: 'pie',
                    data: {
                        labels: branchLabels,
                        datasets: [{
                            label: 'Discounts',
                            data: discountsData,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {}
                });

                // Add more charts here for additional metrics

            });
        </script>



        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var data = [
                    @foreach ($metrics as $metric)
                        {
                            label: "{{ $metric['branch']->name }}",
                            value: {{ $metric['stockValueLeft'] }}
                        },
                    @endforeach
                ];

                var ctx = document.getElementById('stock-chart').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.map(item => item.label),
                        datasets: [{
                            data: data.map(item => item.value),
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: 'Stock Value Left in Each Branch'
                        }
                    }
                });
            });
        </script>
    @endif

@endsection
