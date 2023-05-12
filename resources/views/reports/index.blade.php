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
                                        <div class="col-md-3">
                                            <label>Branch</label>
                                            <select class="form-select mb-2" name="branch_id" required>
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
                                                <option value="best_selling" @if (@$report == 'best_selling') selected @endif>Best Selling Items
                                                </option>
                                                {{-- <option value="gross" @if (@$report == 'gross') selected @endif>By
                                                    Gross Sales
                                                </option> --}}
                                                {{-- <option value="inventory" @if (@$report == 'inventory') selected @endif>
                                                    By Inventory
                                                </option> --}}
                                                {{-- <option value="returns" @if (@$report == 'returns') selected @endif>
                                                    Returns
                                                </option> --}}
                                            </select>
                                        </div>
                                        <div class="col-md-3" id="time_div">
                                            <label>Time</label>
                                            <select class="form-select mb-2" id="date" name="date" required>
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
                                            <label>Amount</label>
                                            <select class="form-select mb-2" id="amount" name="amount">
                                                <option></option>
                                                <option value="10" @if (@$amount == 10) selected @endif>10</option>
                                                <option value="20" @if (@$amount == 20) selected @endif>20</option>
                                                <option value="50" @if (@$amount == 50) selected @endif>50</option>
                                                <option value="100" @if (@$amount == 100) selected @endif>100</option>
                                               
                                            </select>
                                        </div>


                                        <div class="col-md-5 form-group mb-3 d-none" id="date1">
                                            <label>Start Date</label>
                                            <input type="date" class="form-control" value="{{ @$start_date }}" name="start_date">
                                        </div>

                                        <div class="col-md-5 form-group mb-3  d-none" id="date2">
                                            <label>End Date</label>
                                            <input type="date" class="form-control" value="{{ @$end_date }}" name="end_date">
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

                                @if (isset($inventories))

                                    <div class="table-responsive my-5">

                                        <table class="table table-striped table-bordered text-left">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th scope="col" class="text-center">S/N</th>
                                                    <th scope="col">Item</th>
                                                    <th scope="col">Retail Price (&#8358;)</th>
                                                    <th scope="col">Qty Sold </th>
                                                    <th scope="col">Amount (&#8358;)</th>
                                                    <th scope="col">Margin (&#8358;)</th>
                                                    <th scope="col">Qty Remaining</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total_amount = 0;
                                                    $total_margin = 0;
                                                    $total_quantity = 0;
                                                @endphp


                                                @foreach ($inventories as $key => $inventory)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $inventory['product']['name'] }}</td>
                                                        <td>{{ number_format($inventory['product']['selling_price'], 0) }}
                                                        </td>
                                                        @php
                                                            if ($frame == 'today') {
                                                                (float) ($sold = App\Models\Sale::where('stock_id', $inventory->stock_id)
                                                                    ->whereDate('created_at', Carbon\Carbon::today())
                                                                    ->sum('quantity'));
                                                            }
                                                            if ($frame == 'week') {
                                                                (float) ($sold = App\Models\Sale::where('stock_id', $inventory->stock_id)
                                                                    ->whereDate('created_at', [Carbon\Carbon::now()->startOfWeek(), Carbon\Carbon::now()->endOfWeek()])
                                                                    ->sum('quantity'));
                                                            }
                                                            if ($frame == 'month') {
                                                                (float) ($sold = App\Models\Sale::where('stock_id', $inventory->stock_id)
                                                                    ->whereMonth('created_at', Carbon\Carbon::now()->month)
                                                                    ->sum('quantity'));
                                                            }
                                                            if ($frame == 'range') {
                                                                (float) ($sold = App\Models\Sale::where('stock_id', $inventory->stock_id)
                                                                    ->whereBetween('created_at', [$start, $end])
                                                                    ->sum('quantity'));
                                                            }
                                                        @endphp
                                                        <td>{{ $quantity = number_format($sold, 0) }}</td>
                                                        <td>{{ $amount = $sold * $inventory['product']['selling_price'] }}
                                                        </td>
                                                        <td>{{ $margin = $sold * ($inventory['product']['selling_price'] - $inventory['product']['buying_price']) }}
                                                        </td>


                                                        @php
                                                            $total_quantity += (int) $quantity;
                                                            $total_margin += (int) $margin;
                                                            $total_amount += $amount;
                                                            
                                                            @$remain = App\Models\Stock::select('quantity')
                                                                ->where('id', $inventory->stock_id)
                                                                ->first();
                                                        @endphp
                                                        <td>{{ number_format(@$remain->quantity, 0) }}</td>
                                                    </tr>
                                                @endforeach

                                                <tr>
                                                    <td colspan="3" class="text-center"><strong>TOTALS</strong></td>
                                                    <td><strong>{{ number_format($total_quantity, 0) }}</strong></td>
                                                    <td><strong>{{ $total_amount }}</strong></td>
                                                    <td><strong>{{ number_format($total_margin, 0) }}</strong></td>
                                                    <td></td>
                                                </tr>


                                            </tbody>
                                        </table>


                                        <div class="table-responsive my-5">
                                            <h4>Summary</h4>
                                            <table class="table table-striped table-bordered text-left col-md-4">
                                                <thead class="thead-dalrk">

                                                    <tr>
                                                        <th scope="col">Total Qty of Items Sold: </th>
                                                        <td>{{ number_format($total_quantity, 0) }}</th>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col">Gross Revenue: </th>
                                                        </th>
                                                        <td>&#8358;{{ number_format($total_amount, 0) }}</th>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col">Margin</th>
                                                        <td>&#8358;{{ number_format($total_margin, 0) }}</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>


                                    </div>

                                @endif


                                @if (isset($returns))

                                    <div class="table-responsive my-5">

                                        <table class="table table-striped table-bordered text-left">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th scope="col" class="text-center">S/N</th>
                                                    <th scope="col">Receipt No.</th>
                                                    <th scope="col">Item</th>
                                                    <th scope="col">Qty Sold </th>
                                                    <th scope="col">Qty Returned</th>
                                                    <th scope="col">Money Returned (&#8358;)</th>
                                                </tr>
                                            </thead>
                                            @php
                                                $total_money = 0;
                                                $total_items = 0;
                                            @endphp
                                            <tbody>
                                                @foreach ($returns as $key => $return)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $return->receipt_no }}</td>
                                                        <td>{{ $return['product']['name'] }}</td>
                                                        <td>{{ $return->sold_qty }}</td>
                                                        <td>{{ $return->returned_qty }}</td>
                                                        <td>{{ $return->money_returned }}</td>
                                                    </tr>
                                                    @php
                                                        $total_money += (int) $return->money_returned;
                                                        $total_items += (int) $return->returned_qty;
                                                    @endphp
                                                @endforeach

                                                <tr>
                                                    <td colspan="4" class="text-center"><strong>TOTALS</strong></td>
                                                    <td><strong>{{ number_format($total_items, 0) }}</strong></td>
                                                    <td><strong>&#8358;{{ number_format($total_money, 0) }}</strong></td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                @endif


                                @if (isset($gross))
                                    <div class="table-responsive col-md-5">
                                        <table class="table table-striped table-bordered col-md-5">
                                            <tbody class="thead-dark">
                                                <tr>
                                                    <th>Gross Revenue</th>
                                                    <td class="text-center">&#8358;{{ number_format($gross, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Discounts Amount</th>
                                                    <td class="text-center">&#8358;{{ number_format($discount, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Sales Count</th>
                                                    <td class="text-center">{{ number_format($sales_count, 0) }}</td>
                                                </tr>
                                                {{-- <tr>
                                                <th>Discounts Counts</th>
                                                <td class="text-center">{{ number_format($sales_count,0) }}</td>
                                            </tr> --}}
                                                <tr>
                                                    <th>Total Items Sold</th>
                                                    <td class="text-center">{{ number_format($items_sold, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Expenses</th>
                                                    <td class="text-center">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Purchases Amount</th>
                                                    <td class="text-center">
                                                        &#8358;{{ number_format($todays_purchases, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Estimates Amount</th>
                                                    <td class="text-center">&#8358;{{ number_format($todays_estimate, 0) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Estimates Count</th>
                                                    <td class="text-center">{{ number_format($estimate_count, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Returns Amount</th>
                                                    <td class="text-center">&#8358;{{ number_format($todays_returns, 0) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Returns Count</th>
                                                    <td class="text-center">{{ number_format($returns_count, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Low Stock Counts</th>
                                                    <td class="text-center">{{ number_format($lows, 0) }} of
                                                        {{ $total_stock }}</td>
                                                </tr>
                                            </tbody>
                                            <table>
                                    </div>
                                @endif


                                @if (@$report == 'general')
                                    <div class="table-responsive col-md-5">
                                        <table class="table table-striped table-bordered col-md-5">
                                            <tbody class="thead-dark">
                                                <tr>
                                                    <th>Gross Revenue</th>
                                                    <td class="text-center">
                                                        &#8358;{{ number_format($total_sales_value, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Sales Discount</th>
                                                    <td class="text-center">
                                                        &#8358;{{ number_format($total_discount, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Expenses</th>
                                                    <td class="text-center">
                                                        &#8358;{{ number_format($total_expenses_value, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Expense Count</th>
                                                    <td class="text-center">
                                                        {{ number_format($total_expenses_count, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Returns</th>
                                                    <td class="text-center">
                                                        &#8358;{{ number_format($total_returns_value, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Returns Discount</th>
                                                    <td class="text-center">
                                                        &#8358;{{ number_format($total_returns_discount, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Returns Profit</th>
                                                    <td class="text-center">
                                                        &#8358;{{ number_format($returns_profit, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Payments</th>
                                                    <td class="text-center">
                                                        &#8358;{{ number_format($total_payments_value, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Stock Value</th>
                                                    <td class="text-center">
                                                        &#8358;{{ number_format($stock_value, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Gross Sales Profit</th>
                                                    <td class="text-center">
                                                        &#8358;{{ number_format($gross_sales_profit, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Net Sales Profit</th>
                                                    <td class="text-center">
                                                        &#8358;{{ number_format($gross_sales_profit - $returns_profit - $total_discount + $total_returns_discount - $total_expenses_count, 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Credit Owed</th>
                                                    <td class="text-center">
                                                        &#8358;{{ number_format($totalCreditsOwed, 0) }}</td>
                                                </tr>
                                            </tbody>
                                        <table>
                                    </div>
                                @endif


                                @if (@$report == 'best_selling')

                                <div class="table-responsive col-md-5">
                                    <table class="table">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Product Name</th>
                                            <th>Average Daily Sales</th>
                                            <th>Total Sold This Year</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $key => $product)
                                        <tr>
                                            <td>{{ $key +1 }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ round($product->avg_daily_sales, 2) }}</td>
                                            <td>{{ $product->sales_count }}</td>
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
    <script src="/assets/js/vendor/pickadate/picker.js"></script>
    <script src="/assets/js/vendor/pickadate/picker.date.js"></script>
    <script src="/assets/js/form.basic.script.js"></script>

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

                if (report === 'today') {
                    $('#time_div').addClass('d-none');
                    $('#date').removeAttr('required');
                    $('#amount_div').addClass('d-none');
                }
                if (report === 'general') {
                    $('#time_div').removeClass('d-none');
                    $('#date').removeAttr('required');
                    $('#amount_div').addClass('d-none');
                }
                if (report === 'best_selling') {
                    $('#time_div').addClass('d-none');
                    $('#date').removeAttr('required');
                    $('#amount_div').removeClass('d-none');
                }

            });
        });
    </script>
    @if(@$date == 'range')
    <script type="text/javascript">
        $('#date1').removeClass('d-none');
        $('#date2').removeClass('d-none');</script>
    @endif
    @if(@$report == 'best_selling')
    <script type="text/javascript">
         $('#time_div').addClass('d-none');
        $('#amount_div').removeClass('d-none');
        $('#date').removeAttr('required');
    </script>
    @endif
@endsection
