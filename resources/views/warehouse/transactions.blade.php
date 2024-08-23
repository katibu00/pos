@extends('layouts.app')
@section('PageTitle', 'Warehouse Transactions')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <h2 class="mb-4">Warehouse Transactions</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Filters</h5>
                            <form id="filterForm">
                                <div class="form-group">
                                    <label for="warehouse">Warehouse:</label>
                                    <select name="warehouse" id="warehouse" class="form-control">
                                        <option value="">All Warehouses</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="type">Type:</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="">All Types</option>
                                        <option value="in">In</option>
                                        <option value="out">Out</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="source">Source:</label>
                                    <select name="source" id="source" class="form-control">
                                        <option value="">All Sources</option>
                                        @foreach($sources as $source)
                                            <option value="{{ $source }}">{{ ucfirst($source) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="date_from">Date From:</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="date_to">Date To:</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="table-responsive" id="transactions-table">
                        @include('warehouse.partials.transactions_table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionDetailsModalLabel">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center" id="loading-spinner">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="transaction-details-content"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<style>
    .sort-icon {
        display: inline-block;
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        margin-left: 5px;
    }
    .sort-icon.asc {
        border-bottom: 5px solid #000;
    }
    .sort-icon.desc {
        border-top: 5px solid #000;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function fetchTransactions(page = 1) {
        var url = '{{ route("admin.warehouse.transactions") }}?page=' + page;
        var formData = $('#filterForm').serialize();
        url += '&' + formData;

        $.ajax({
            url: url,
            success: function(data) {
                $('#transactions-table').html(data);
            }
        });
    }

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        fetchTransactions(page);
    });

    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        fetchTransactions();
    });

    $(document).on('click', '.sortable', function() {
        var sort = $(this).data('sort');
        var direction = $(this).find('.sort-icon').hasClass('asc') ? 'desc' : 'asc';
        $('.sort-icon').removeClass('asc desc');
        $(this).find('.sort-icon').addClass(direction);
        fetchTransactions(1, sort, direction);
    });

    $(document).on('click', '.view-details', function() {
        var batchNumber = $(this).data('batch');
        $('#transactionDetailsModal').modal('show');
        $('#loading-spinner').show();
        $('#transaction-details-content').hide();

        $.ajax({
            url: '{{ url("warehouse/transaction-details") }}/' + batchNumber,
            success: function(data) {
                $('#loading-spinner').hide();
                $('#transaction-details-content').html(renderTransactionDetails(data)).show();
            },
            error: function() {
                $('#loading-spinner').hide();
                $('#transaction-details-content').html('<div class="alert alert-danger">Error loading transaction details.</div>').show();
            }
        });
    });

    function renderTransactionDetails(transactions) {
        var html = '<table class="table">';
        html += '<thead><tr><th>Item</th><th>Quantity</th><th>Warehouse</th><th>Branch</th></tr></thead>';
        html += '<tbody>';
        transactions.forEach(function(transaction) {
            html += '<tr>';
            html += '<td>' + transaction.stock.name + '</td>';
            html += '<td>' + transaction.quantity + '</td>';
            html += '<td>' + transaction.warehouse.name + '</td>';
            html += '<td>' + (transaction.branch ? transaction.branch.name : 'N/A') + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table>';
        return html;
    }
});
</script>
@endsection