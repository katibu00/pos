@extends('layouts.app')
@section('PageTitle', 'Restock Management')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <h1 class="mb-4">Restock Management</h1>

            <!-- Success Message -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif


            <!-- Branch Restock Value Cards -->
            <div class="row mb-4" id="branchRestockValues">
                @foreach($branchRestockValues as $branchValue)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $branchValue->name }}</h5>
                            <p class="card-text">Last 30 days: ${{ number_format($branchValue->total_value, 2) }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Create New Restock Buttons -->
            <div class="mb-4">
                <a href="{{ route('restock.create.planned') }}" class="btn btn-primary me-2">+ New Planned Reorder</a>
                <a href="{{ route('restock.create.direct') }}" class="btn btn-secondary">+ New Direct Restock</a>
            </div>

            <!-- Filters and Sorting -->
            <form id="filterForm" class="mb-4">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <select name="branch_id" class="form-select" id="branch_id">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <select name="type" class="form-select" id="type">
                            <option value="">All Types</option>
                            <option value="planned">Planned</option>
                            <option value="direct">Direct</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <select name="status" class="form-select" id="status">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="received">Received</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </div>
            </form>

            <style>
                .loader-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(255, 255, 255, 0.8); 
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 1050;
                }
            </style>
            
            <!-- Loader -->
            <div id="loader" style="display:none;" class="loader-overlay">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>            

            <!-- Restocks Table -->
            <div class="table-responsive" id="restockTable">
                @include('restock.partials.restock_table', ['restocks' => $restocks])
            </div>

            <!-- Recent Restocks -->
            <h2 class="mt-5 mb-3">Recent Restocks</h2>
            <ul class="list-group" id="recentRestocks">
                @foreach($recentRestocks as $recentRestock)
                <li class="list-group-item">
                    {{ $recentRestock->restock_number }} - 
                    {{ ucfirst($recentRestock->type) }} - 
                    {{ $recentRestock->branchRestocks->first()->branch->name ?? 'N/A' }}
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>


<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Restock Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detailsContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Expenses Modal -->
<div class="modal fade" id="expensesModal" tabindex="-1" aria-labelledby="expensesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expensesModalLabel">Manage Expenses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="expensesContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Damages Modal -->
<div class="modal fade" id="damagesModal" tabindex="-1" aria-labelledby="damagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="damagesModalLabel">Manage Damages</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="damagesContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')


<script>
    $(document).ready(function() {
    
        // View Details
        $(document).on('click', '.view-details', function(e) {
            e.preventDefault();
            var restockId = $(this).data('restock-id');
            $('#detailsModal').modal('show');
            fetchDetails(restockId);
        });

        // Manage Expenses
        $(document).on('click', '.manage-expenses', function(e) {
            e.preventDefault();
            var restockId = $(this).data('restock-id');
            $('#expensesModal').modal('show');
            fetchExpenses(restockId);
        });

        // Manage Damages
        $(document).on('click', '.manage-damages', function(e) {
            e.preventDefault();
            var restockId = $(this).data('restock-id');
            $('#damagesModal').modal('show');
            fetchDamages(restockId);
        });

        // Download PDF
        $(document).on('click', '.download-pdf', function(e) {
            e.preventDefault();
            var restockId = $(this).data('restock-id');
            window.location.href = '/restock/' + restockId + '/download-pdf';
        });

        function fetchDetails(restockId) {
            $.ajax({
                url: '/restock/' + restockId + '/details',
                type: 'GET',
                success: function(response) {
                    $('#detailsContent').html(response);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        function fetchExpenses(restockId) {
            $.ajax({
                url: '/restock/' + restockId + '/expenses',
                type: 'GET',
                success: function(response) {
                    $('#expensesContent').html(response);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        function fetchDamages(restockId) {
            $.ajax({
                url: '/restock/' + restockId + '/damages',
                type: 'GET',
                success: function(response) {
                    $('#damagesContent').html(response);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    });
</script>


<script>
    $(document).ready(function() {
        function fetchRestocks(url = null) {
            var data = {
                branch_id: $('#branch_id').val(),
                type: $('#type').val(),
                status: $('#status').val(),
                _token: $('input[name="_token"]').val()
            };

            // Show loader
            $('#loader').show();

            $.ajax({
                url: url || '{{ route("restock.index") }}',
                type: 'GET',
                data: data,
                success: function(response) {
                    $('#restockTable').html(response.table);
                    $('#recentRestocks').html(response.recentRestocks);
                    $('#branchRestockValues').html(response.branchRestockValues);
                },
                complete: function() {
                    // Hide loader
                    $('#loader').hide();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            fetchRestocks();
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            fetchRestocks(url);
        });
    });
</script>
@endsection
