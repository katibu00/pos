@extends('layouts.app')
@section('PageTitle', 'Expense Deposits')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <h2 class="mb-4">Expense Deposits</h2>

            <style>
                .bg-gradient-primary {
                    background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%) !important;
                }

                .shadow-lg {
                    box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
                }

                .rounded-lg {
                    border-radius: 0.5rem !important;
                }
            </style>
            
            <div id="balanceCards" class="row mb-4">
                @include('expense.balance_cards')
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0">Recent Deposits</h3>
                    <button id="newDepositBtn" class="btn btn-primary">New Deposit</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Branch</th>
                                    <th>Amount</th>
                                    <th>Note</th>
                                    <th>Admin</th>
                                </tr>
                            </thead>
                            <tbody id="recentDepositsBody">
                                @include('expense.deposits_table')
                            </tbody>
                        </table>
                    </div>
                    <div id="pagination" class="d-flex justify-content-center mt-4">
                        {{ $recentDeposits->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Deposit Modal -->
<div class="modal fade" id="newDepositModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Deposit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newDepositForm">
                    <div class="mb-3">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select class="form-select" id="branch_id" name="branch_id" required>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
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
                <button type="button" class="btn btn-primary" id="submitDeposit">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    const modal = new bootstrap.Modal(document.getElementById('newDepositModal'));

    $('#newDepositBtn').click(function() {
        modal.show();
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#submitDeposit').click(function() {
        $.ajax({
            url: '{{ route("expenses.deposits.store") }}',
            method: 'POST',
            data: $('#newDepositForm').serialize(),
            success: function(response) {
                if (response.success) {
                    modal.hide();
                    refreshTable();
                    refreshBalanceCards();
                }
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    });

    function refreshTable(url = '{{ route("expenses.deposits") }}') {
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                $('#recentDepositsBody').html($(response).find('#recentDepositsBody').html());
                $('#pagination').html($(response).find('#pagination').html());
                initPagination();
            }
        });
    }

    function refreshBalanceCards() {
        $.ajax({
            url: '{{ route("expenses.balance_cards") }}',
            method: 'GET',
            success: function(response) {
                $('#balanceCards').html(response);
            }
        });
    }

    function initPagination() {
        $('#pagination a').click(function(e) {
            e.preventDefault();
            refreshTable($(this).attr('href'));
        });
    }

    initPagination();
});
</script>
@endsection