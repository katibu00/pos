@extends('layouts.app')

@section('PageTitle', 'Expense Reports')

@section('content')
<section id="content" class="bg-light py-5">
    <div class="container">
        <h2 class="mb-4">Expense Reports</h2>

        <div class="card shadow-lg rounded-lg mb-5">
            <div class="card-body">
                <form id="reportForm" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="report_type" class="form-label">Report Type</label>
                        <select class="form-select" id="report_type" name="report_type" required>
                            <option value="">Select Report Type</option>
                            <option value="overview">Expense Overview</option>
                            <option value="by_category">Expenses by Category</option>
                            <option value="by_branch">Expenses by Branch</option>
                            <option value="top_expenses">Top Expenses</option>
                            <option value="trend_analysis">Expense Trend Analysis</option>
                            <option value="monthly_summary">Monthly Expense Summary</option>
                            <option value="yearly_summary">Yearly Expense Summary</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="time_frame" class="form-label">Time Frame</label>
                        <select class="form-select" id="time_frame" name="time_frame" required>
                            <option value="">Select Time Frame</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="last_7_days">Last 7 Days</option>
                            <option value="last_30_days">Last 30 Days</option>
                            <option value="single_date">Single Date</option>
                            <option value="date_range">Date Range</option>
                        </select>
                    </div>
                    <div class="col-md-2 date-picker" style="display: none;">
                        <label for="single_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="single_date" name="single_date">
                    </div>
                    <div class="col-md-2 date-range" style="display: none;">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date">
                    </div>
                    <div class="col-md-2 date-range" style="display: none;">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                    </div>
                    <div class="col-md-3">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select class="form-select" id="branch_id" name="branch_id">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="reportContent" style="display: none;">
            <div class="card shadow-lg rounded-lg">
                <div class="card-body">
                    <h3 id="reportTitle" class="mb-4"></h3>
                    <div class="row">
                        <div id="chartArea" class="col-md-6 mb-4"></div>
                        <div class="col-md-6">
                            <div id="reportAnalysis" class="mb-4"></div>
                            <div id="reportTable"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let chartInstances = [];

    $('#time_frame').change(function() {
        const selectedValue = $(this).val();
        $('.date-picker, .date-range').hide();
        if (selectedValue === 'single_date') {
            $('.date-picker').show();
        } else if (selectedValue === 'date_range') {
            $('.date-range').show();
        }
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#reportForm').submit(function(e) {
        e.preventDefault();
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...');

        $.ajax({
            url: '{{ route("expenses.generate_report") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Destroy existing chart instances
                chartInstances.forEach(chart => chart.destroy());
                chartInstances = [];

                $('#reportContent').show();
                $('#reportTitle').text(response.title);
                
                // Clear previous chart containers
                $('#chartArea').empty();

                // Render main chart
                const mainChartContainer = $('<div style="position: relative; height: 300px; width: 100%;"><canvas id="mainChart"></canvas></div>');
                $('#chartArea').append(mainChartContainer);
                chartInstances.push(renderChart(response.chartData, 'mainChart'));

                // Render additional charts if available
                if (response.trendChartData) {
                    const trendChartContainer = $('<div style="position: relative; height: 300px; width: 100%; margin-top: 20px;"><canvas id="trendChart"></canvas></div>');
                    $('#chartArea').append(trendChartContainer);
                    chartInstances.push(renderChart(response.trendChartData, 'trendChart'));
                }

                if (response.barChartData) {
                    const barChartContainer = $('<div style="position: relative; height: 300px; width: 100%; margin-top: 20px;"><canvas id="barChart"></canvas></div>');
                    $('#chartArea').append(barChartContainer);
                    chartInstances.push(renderChart(response.barChartData, 'barChart'));
                }

                $('#reportAnalysis').html(response.analysis);
                $('#reportTable').html(response.table);
            },
            error: function(xhr) {
                toastr.error('Error generating report: ' + xhr.responseJSON.message);
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    function renderChart(chartData, containerId) {
        const ctx = document.getElementById(containerId).getContext('2d');
        const isPieChart = chartData.type === 'pie' || chartData.type === 'doughnut';

        const config = {
            type: chartData.type,
            data: chartData.data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: isPieChart ? 'right' : 'bottom',
                    },
                    title: {
                        display: true,
                        text: chartData.title || '',
                        font: {
                            size: 16
                        }
                    }
                },
                ...chartData.options
            }
        };

        if (isPieChart) {
            config.options = {
                ...config.options,
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 0,
                        bottom: 0
                    }
                },
                plugins: {
                    ...config.options.plugins,
                    legend: {
                        position: 'right',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            };
        }

        return new Chart(ctx, config);
    }
});
</script>
@endsection