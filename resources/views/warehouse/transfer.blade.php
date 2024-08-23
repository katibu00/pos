@extends('layouts.app')
@section('PageTitle', 'Transfer from Store')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <h2 class="mb-4">Transfer from Store to Warehouse</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Search Items</h5>
                            <div class="form-group">
                                <label for="branch_id">Branch:</label>
                                <select id="branch_id" class="form-control" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="search">Search Stock:</label>
                                <input type="text" id="search" class="form-control" placeholder="Type to search...">
                            </div>
                            <div id="search-results" class="mt-3"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Selected Items</h5>
                            <div class="form-group">
                                <label for="warehouse_id">Warehouse:</label>
                                <select id="warehouse_id" class="form-control" required>
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <table id="selected-items" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Available</th>
                                        <th>Quantity to Transfer</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <button id="save-transfer" class="btn btn-primary mt-3">Save Transfer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
<style>
    .search-item {
        padding: 10px;
        border: 1px solid #ddd;
        margin-bottom: 5px;
        cursor: pointer;
    }
    .search-item:hover {
        background-color: #f5f5f5;
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

    let searchTimeout;

    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val();
        const branchId = $('#branch_id').val();

        if (query.length < 2 || !branchId) {
            $('#search-results').html('');
            return;
        }

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: "{{ route('admin.warehouse.search-stocks') }}",
                method: 'GET',
                data: { query: query, branch_id: branchId },
                success: function(data) {
                    let html = '';
                    data.forEach(function(item) {
                        html += `<div class="search-item" data-id="${item.id}" data-name="${item.name}" data-quantity="${item.quantity}">
                            ${item.name} (Available: ${item.quantity})
                        </div>`;
                    });
                    $('#search-results').html(html);
                }
            });
        }, 300);
    });

    $(document).on('click', '.search-item', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const available = $(this).data('quantity');

        if ($(`#selected-items tr[data-id="${id}"]`).length === 0) {
            $('#selected-items tbody').append(`
                <tr data-id="${id}">
                    <td>${name}</td>
                    <td>${available}</td>
                    <td><input type="number" class="form-control transfer-quantity" min="1" max="${available}" value="1"></td>
                    <td><button class="btn btn-sm btn-danger remove-item">Remove</button></td>
                </tr>
            `);
        }

        // Clear the search results
        $('#search-results').html('');
        // Clear the search input to require a new search
        $('#search').val('');
    });

    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
    });

    $('#save-transfer').on('click', function() {
        const warehouseId = $('#warehouse_id').val();
        if (!warehouseId) {
            Swal.fire('Error', 'Please select a warehouse', 'error');
            return;
        }

        const items = [];
        $('#selected-items tbody tr').each(function() {
            const stockId = $(this).data('id');
            const quantity = $(this).find('.transfer-quantity').val();
            items.push({ stock_id: stockId, quantity: quantity });
        });

        if (items.length === 0) {
            Swal.fire('Error', 'Please select items to transfer', 'error');
            return;
        }

        $.ajax({
            url: "{{ route('admin.warehouse.transfer.post') }}",
            method: 'POST',
            data: { warehouse_id: warehouseId, items: items },
            success: function(response) {
                Swal.fire('Success', response.message, 'success').then(function() {
                    location.reload();
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    for (let key in errors) {
                        errorMessage += errors[key].join('\n') + '\n';
                    }
                    Swal.fire('Validation Error', errorMessage, 'error');
                } else {
                    Swal.fire('Error', xhr.responseJSON.error || 'An error occurred', 'error');
                }
            }
        });
    });
});
</script>
@endsection
