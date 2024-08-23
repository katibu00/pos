@extends('layouts.app')
@section('PageTitle', 'Warehouse Store')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <h2>Warehouse Store</h2>
            <div id="items-table">
                @include('warehouse.partials.items_table')
            </div>
        </div>
    </div>
</section>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Transfer to Store</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transferForm">
                    <input type="hidden" id="warehouse_item_id" name="warehouse_item_id">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity to Transfer:</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveTransfer">Save Transfer</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
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

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        fetchItems(page);
    });

    function fetchItems(page) {
        $.ajax({
            url: '{{ route("admin.warehouse.index") }}?page=' + page,
            success: function(data) {
                $('#items-table').html(data);
            }
        });
    }

    $(document).on('click', '.transfer-btn', function() {
        var warehouseItemId = $(this).data('id');
        var maxQuantity = $(this).data('quantity');
        $('#warehouse_item_id').val(warehouseItemId);
        $('#quantity').attr('max', maxQuantity);
        $('#transferModal').modal('show');
    });

    $('#saveTransfer').click(function() {
        var warehouseItemId = $('#warehouse_item_id').val();
        var quantity = $('#quantity').val();

        $.ajax({
            url: '{{ route("admin.warehouse.transfer-to-store") }}',
            method: 'POST',
            data: {
                warehouse_item_id: warehouseItemId,
                quantity: quantity
            },
            success: function(response) {
                $('#transferModal').modal('hide');
                Swal.fire('Success', response.message, 'success');
                $(`[data-id="${warehouseItemId}"]`).data('quantity', response.new_quantity);
                $(`#quantity-${warehouseItemId}`).text(response.new_quantity);
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