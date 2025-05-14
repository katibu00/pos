@extends('layouts.app')
@section('PageTitle', 'Expense Records')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <h2 class="mb-4">Expense Records</h2>

            <style>
                .shadow-lg {
                    box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
                }

                .rounded-lg {
                    border-radius: 0.5rem !important;
                }
                .spinner-border {
                    display: inline-block;
                    width: 1rem;
                    height: 1rem;
                    vertical-align: text-bottom;
                    border: .25em solid currentColor;
                    border-right-color: transparent;
                    border-radius: 50%;
                    animation: spinner-border .75s linear infinite;
                }

                @keyframes spinner-border {
                    to { transform: rotate(360deg); }
                }
            </style>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white shadow-lg rounded-lg">
                        <div class="card-body">
                            <h5 class="card-title">Available Balance</h5>
                            <h3 class="mb-0">₦{{ number_format($availableBalance, 2) }}</h3>
                            <small>{{ auth()->user()->branch->name }}</small> <!-- Display the user's branch name -->
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white shadow-lg rounded-lg">
                        <div class="card-body">
                            <h5 class="card-title">Today's Expenses ({{ auth()->user()->branch->name }})</h5> <!-- Include branch name in title -->
                            <h3 class="mb-0">₦{{ number_format($todayExpenses, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white shadow-lg rounded-lg">
                        <div class="card-body">
                            <h5 class="card-title">Last 30 Days Expenses ({{ auth()->user()->branch->name }})</h5> <!-- Include branch name in title -->
                            <h3 class="mb-0">₦{{ number_format($last30DaysExpenses, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card shadow-lg rounded-lg">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0">Recent Expenses</h3>
                    <button id="newExpenseBtn" class="btn btn-primary btn-lg"><i class="fas fa-plus-circle me-2"></i>Record New Expense</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Note</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody id="recentExpensesBody">
                                @include('expense.records_table')
                            </tbody>
                        </table>
                    </div>
                    <div id="pagination" class="d-flex justify-content-center mt-4">
                        {{ $recentExpenses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Expense Modal -->
<div class="modal fade" id="newExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Record New Expense</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newExpenseForm">
                    <div class="mb-3">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select class="form-select" id="branch_id" name="branch_id" required>
                            <option value=""></option>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value=""></option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea class="form-control" id="note" name="note"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitExpense">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<script>
$(document).ready(function() {
    const modal = new bootstrap.Modal(document.getElementById('newExpenseModal'));

    $('#newExpenseBtn').click(function() {
        modal.show();
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#submitExpense').click(function() {
        const $submitBtn = $(this);
        const originalText = $submitBtn.text();
        
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');

        $.ajax({
            url: '{{ route("expenses.records.store") }}',
            method: 'POST',
            data: $('#newExpenseForm').serialize(),
            success: function(response) {
                if (response.success) {
                    modal.hide();
                    refreshPage();
                    clearModalFields();
                    toastr.success('Expense recorded successfully!');
                }
            },
            error: function(xhr) {
                toastr.error('Error: ' + xhr.responseJSON.message);
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    function refreshPage() {
        $.ajax({
            url: '{{ route("expenses.records") }}',
            method: 'GET',
            success: function(response) {
                $('#recentExpensesBody').html($(response).find('#recentExpensesBody').html());
                $('#pagination').html($(response).find('#pagination').html());
                $('.card-body h3').each(function(index) {
                    $(this).html($(response).find('.card-body h3').eq(index).html());
                });
                initPagination();
            }
        });
    }

    function clearModalFields() {
        $('#newExpenseForm')[0].reset();
    }

    function initPagination() {
        $('#pagination a').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('href'),
                method: 'GET',
                success: function(response) {
                    $('#recentExpensesBody').html($(response).find('#recentExpensesBody').html());
                    $('#pagination').html($(response).find('#pagination').html());
                    initPagination();
                }
            });
        });
    }

    initPagination();

    // Toastr configuration
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
});
</script>
@endsection