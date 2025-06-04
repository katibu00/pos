@extends('layouts.app')
@section('PageTitle', 'Warehouse Transactions')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-history text-primary me-2"></i>Transaction History
                            </h2>
                            <p class="text-muted mb-0">Complete record of all warehouse movements</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success" id="exportBtn">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                            <button class="btn btn-outline-primary" id="refreshBtn">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4" id="statisticsCards">
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm stats-card">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-primary">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <h4 class="stats-number" id="totalTransactions">{{ number_format($stats['total_transactions']) }}</h4>
                            <p class="stats-label">Total Transactions</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm stats-card">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-success">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <h4 class="stats-number" id="totalMoveIn">{{ number_format($stats['total_move_in']) }}</h4>
                            <p class="stats-label">Items Moved In</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm stats-card">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <h4 class="stats-number" id="totalMoveOut">{{ number_format($stats['total_move_out']) }}</h4>
                            <p class="stats-label">Items Moved Out</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm stats-card">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-info">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                            <h4 class="stats-number" id="netMovement">{{ number_format($stats['net_movement']) }}</h4>
                            <p class="stats-label">Net Movement</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm stats-card">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-secondary">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <h4 class="stats-number" id="todayTransactions">{{ number_format($stats['today_transactions']) }}</h4>
                            <p class="stats-label">Today's Activity</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm stats-card">
                        <div class="card-body text-center">
                            <div class="stats-icon bg-dark">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <h6 class="stats-number small" id="mostActiveWarehouse">{{ $stats['most_active_warehouse'] }}</h6>
                            <p class="stats-label">Most Active</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-filter text-primary me-2"></i>Filters & Search
                                </h6>
                                <button class="btn btn-sm btn-outline-secondary" id="clearFilters">
                                    <i class="fas fa-times me-1"></i>Clear All
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="filtersForm">
                                <div class="row">
                                    <!-- Search -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <label class="form-label">Search</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text" class="form-control" id="searchInput" name="search" 
                                                placeholder="Batch, item, warehouse..." value="{{ request('search') }}">
                                        </div>
                                    </div>

                                    <!-- Warehouse Filter -->
                                    <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
                                        <label class="form-label">Warehouse</label>
                                        <select class="form-select" id="warehouseFilter" name="warehouse_id">
                                            <option value="">All Warehouses</option>
                                            @foreach($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                    {{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Branch Filter -->
                                    <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
                                        <label class="form-label">Branch</label>
                                        <select class="form-select" id="branchFilter" name="branch_id">
                                            <option value="">All Branches</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Type Filter -->
                                    <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
                                        <label class="form-label">Movement Type</label>
                                        <select class="form-select" id="typeFilter" name="type">
                                            <option value="">All Types</option>
                                            <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Move In</option>
                                            <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Move Out</option>
                                        </select>
                                    </div>

                                    <!-- Source Filter -->
                                    <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
                                        <label class="form-label">Source</label>
                                        <select class="form-select" id="sourceFilter" name="source">
                                            <option value="">All Sources</option>
                                            @foreach($sources as $source)
                                                <option value="{{ $source }}" {{ request('source') == $source ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $source)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Date Range -->
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <label class="form-label">Date From</label>
                                        <input type="date" class="form-control" id="dateFrom" name="date_from" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                        <label class="form-label">Date To</label>
                                        <input type="date" class="form-control" id="dateTo" name="date_to" value="{{ request('date_to') }}">
                                    </div>

                                    <!-- Quick Date Filters -->
                                    <div class="col-xl-6 col-lg-8 col-md-12 mb-3">
                                        <label class="form-label">Quick Filters</label>
                                        <div class="btn-group w-100" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm quick-date" data-days="0">Today</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm quick-date" data-days="7">Last 7 Days</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm quick-date" data-days="30">Last 30 Days</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm quick-date" data-days="90">Last 90 Days</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-list text-primary me-2"></i>Transaction Records
                                </h6>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-muted small" id="recordsCount">
                                        Showing {{ $transactions->count() }} of {{ $transactions->total() }} records
                                    </span>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary sort-btn active" data-sort="created_at" data-direction="desc">
                                            <i class="fas fa-clock me-1"></i>Latest
                                        </button>
                                        <button class="btn btn-outline-secondary sort-btn" data-sort="quantity" data-direction="desc">
                                            <i class="fas fa-sort-amount-down me-1"></i>Quantity
                                        </button>
                                        <button class="btn btn-outline-secondary sort-btn" data-sort="type" data-direction="asc">
                                            <i class="fas fa-sort-alpha-down me-1"></i>Type
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <div id="transactionsTable">
                                    @include('warehouse.partials.transactions_table')
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0">
                            <div id="paginationContainer">
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel">
                    <i class="fas fa-info-circle me-2"></i>Transaction Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transactionDetails">
                <div class="text-center py-3">
                    <div class="loading-spinner"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .stats-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 12px;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        color: white;
        font-size: 1.2em;
    }

    .stats-number {
        font-size: 1.8em;
        font-weight: 700;
        margin-bottom: 5px;
        color: #2c3e50;
    }

    .stats-label {
        font-size: 0.85em;
        color: #6c757d;
        margin-bottom: 0;
        font-weight: 500;
    }

    .transaction-row {
        transition: background-color 0.3s ease;
        cursor: pointer;
    }

    .transaction-row:hover {
        background-color: #f8f9fa;
    }

    .batch-number {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #495057;
        font-size: 0.9em;
        background: #e9ecef;
        padding: 2px 8px;
        border-radius: 4px;
        display: inline-block;
    }

    .type-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75em;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .type-in {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
    }

    .type-out {
        background: linear-gradient(45deg, #dc3545, #fd7e14);
        color: white;
    }

    .quantity-display {
        font-weight: 600;
        font-size: 1.1em;
    }

    .source-badge {
        background: #6f42c1;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75em;
        font-weight: 500;
    }

    .card {
        border-radius: 12px;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        padding: 10px 12px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
    }

    .quick-date.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .sort-btn.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        font-size: 0.9em;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 15px 12px;
        background-color: #f8f9fa;
    }

    .table td {
        padding: 12px;
        vertical-align: middle;
        border-top: 1px solid #f1f3f4;
    }

    .pagination {
        margin: 0;
    }

    .pagination .page-link {
        border: none;
        color: #007bff;
        padding: 8px 12px;
        border-radius: 6px;
        margin: 0 2px;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        color: white;
    }

    .pagination .page-link:hover {
        background-color: #e9ecef;
        color: #0056b3;
    }

    .item-name {
        font-weight: 500;
        color: #2c3e50;
    }

    .warehouse-name {
        color: #6c757d;
        font-size: 0.9em;
    }

    .branch-name {
        color: #28a745;
        font-weight: 500;
        font-size: 0.9em;
    }

    .timestamp {
        color: #6c757d;
        font-size: 0.85em;
    }

    .no-data {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .no-data i {
        font-size: 4em;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    @media (max-width: 768px) {
        .stats-number {
            font-size: 1.4em;
        }
        
        .btn-group {
            flex-direction: column;
        }
        
        .d-flex.gap-3 {
            flex-direction: column;
            gap: 1rem !important;
        }
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    let currentSort = 'created_at';
    let currentDirection = 'desc';
    
    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Filter change handlers
    $('#filtersForm input, #filtersForm select').on('change input', function() {
        if ($(this).attr('type') === 'text') {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(loadTransactions, 500);
        } else {
            loadTransactions();
        }
    });

    // Quick date filters
    $('.quick-date').on('click', function() {
        const days = $(this).data('days');
        const today = new Date();
        const fromDate = new Date(today);
        
        $('.quick-date').removeClass('active');
        $(this).addClass('active');
        
        if (days === 0) {
            $('#dateFrom').val(today.toISOString().split('T')[0]);
            $('#dateTo').val(today.toISOString().split('T')[0]);
        } else {
            fromDate.setDate(today.getDate() - days);
            $('#dateFrom').val(fromDate.toISOString().split('T')[0]);
            $('#dateTo').val(today.toISOString().split('T')[0]);
        }
        
        loadTransactions();
    });

    // Sort buttons
    $('.sort-btn').on('click', function() {
        const sortField = $(this).data('sort');
        const direction = $(this).data('direction');
        
        currentSort = sortField;
        currentDirection = direction;
        
        $('.sort-btn').removeClass('active');
        $(this).addClass('active');
        
        loadTransactions();
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#filtersForm')[0].reset();
        $('.quick-date').removeClass('active');
        $('.sort-btn').removeClass('active');
        $('.sort-btn[data-sort="created_at"]').addClass('active');
        currentSort = 'created_at';
        currentDirection = 'desc';
        loadTransactions();
    });

    // Refresh button
    $('#refreshBtn').on('click', function() {
        loadTransactions();
        Swal.fire({
            icon: 'success',
            title: 'Refreshed!',
            text: 'Transaction data has been refreshed.',
            timer: 1500,
            showConfirmButton: false
        });
    });

    // Export button
    $('#exportBtn').on('click', function() {
        const formData = $('#filtersForm').serialize();
        window.location.href = `{{ route('warehouse.transactions.export') }}?${formData}`;
    });

    // Transaction row click for details
    $(document).on('click', '.transaction-row', function() {
        const batchNumber = $(this).data('batch');
        showTransactionDetails(batchNumber);
    });

    // Pagination click handler
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url) {
            loadTransactionsFromUrl(url);
        }
    });

    // Load transactions function
    function loadTransactions(page = 1) {
        const formData = $('#filtersForm').serialize();
        const url = `{{ route('admin.warehouse.transactions') }}?${formData}&sort=${currentSort}&direction=${currentDirection}&page=${page}`;
        loadTransactionsFromUrl(url);
    }

    function loadTransactionsFromUrl(url) {
        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#transactionsTable').html('<div class="text-center py-4"><div class="loading-spinner"></div></div>');
            },
            success: function(response) {
                $('#transactionsTable').html(response.html);
                $('#paginationContainer').html(response.pagination);
                updateStats(response.stats);
                updateRecordsCount();
            },
            error: function(xhr) {
                console.error('Error loading transactions:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load transactions. Please try again.'
                });
            }
        });
    }

    // Update statistics
    function updateStats(stats) {
        $('#totalTransactions').text(numberFormat(stats.total_transactions));
        $('#totalMoveIn').text(numberFormat(stats.total_move_in));
        $('#totalMoveOut').text(numberFormat(stats.total_move_out));
        $('#netMovement').text(numberFormat(stats.net_movement));
        $('#todayTransactions').text(numberFormat(stats.today_transactions));
        $('#mostActiveWarehouse').text(stats.most_active_warehouse);
    }

    // Show transaction details modal
    function showTransactionDetails(batchNumber) {
        $('#transactionModal').modal('show');
        $('#transactionDetails').html('<div class="text-center py-3"><div class="loading-spinner"></div></div>');
        
        $.ajax({
            url: `{{ url('admin/warehouse/transactions/details') }}/${batchNumber}`,
            type: 'GET',
            success: function(response) {
                const html = buildTransactionDetailsHtml(response);
                $('#transactionDetails').html(html);
            },
            error: function(xhr) {
                $('#transactionDetails').html('<div class="alert alert-danger">Failed to load transaction details.</div>');
            }
        });
    }

    // Build transaction details HTML
    function buildTransactionDetailsHtml(data) {
        const summary = data.summary;
        const transactions = data.transactions;
        
        let html = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Batch Summary</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Batch Number:</strong></td><td><span class="batch-number">${summary.batch_number}</span></td></tr>
                        <tr><td><strong>Type:</strong></td><td><span class="type-badge type-${summary.type}">${summary.type.toUpperCase()}</span></td></tr>
                        <tr><td><strong>Source:</strong></td><td><span class="source-badge">${summary.source}</span></td></tr>
                        <tr><td><strong>Warehouse:</strong></td><td>${summary.warehouse}</td></tr>
                        <tr><td><strong>Branch:</strong></td><td>${summary.branch}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Statistics</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Total Items:</strong></td><td>${summary.total_items}</td></tr>
                        <tr><td><strong>Total Quantity:</strong></td><td>${numberFormat(summary.total_quantity)}</td></tr>
                        <tr><td><strong>Created:</strong></td><td>${formatDateTime(summary.created_at)}</td></tr>
                    </table>
                </div>
            </div>
            
            <h6 class="text-muted mb-3">Individual Transactions</h6>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        transactions.forEach(transaction => {
            html += `
                <tr>
                    <td class="item-name">${transaction.stock ? transaction.stock.name : 'N/A'}</td>
                    <td class="quantity-display">${numberFormat(transaction.quantity)}</td>
                    <td>₦${numberFormat(transaction.unit_price || 0, 2)}</td>
                    <td>₦${numberFormat((transaction.quantity * (transaction.unit_price || 0)), 2)}</td>
                    <td class="timestamp">${formatDateTime(transaction.created_at)}</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        return html;
    }

    // Update records count
    function updateRecordsCount() {
        const tableRows = $('#transactionsTable table tbody tr').length;
        const paginationText = $('.pagination').parent().find('.page-item.active .page-link').text();
        // This would need to be updated based on actual pagination info from server
    }

    // Utility functions
    function numberFormat(number, decimals = 0) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    }

    function formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
});
</script>
@endsection