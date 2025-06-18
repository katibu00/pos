@extends('layouts.app')
@section('PageTitle', 'Complete Restock Order')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <h2>Complete Restock Order #{{ $restock->restock_number }}</h2>
            @if($restock->items->isEmpty())
                <div class="alert alert-warning">
                    This restock order has no items. Please add items before completing the order.
                </div>
            @else

            {{-- Display validation errors --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Display custom error message --}}
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif


                <form action="{{ route('restock.complete', $restock->id) }}" method="POST" id="completeRestockForm">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4 mb-3">
                            <label for="storage_location">Storage Location:</label>
                            <select name="storage_location" id="storage_location" class="form-select" required>
                                <option value="">Select Storage Type</option>
                                <option value="shop">Shop</option>
                                <option value="warehouse">Warehouse</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 mb-3" id="branch_selection" style="display: none;">
                            <label for="branch_id">Branch:</label>
                            <select name="branch_id" id="branch_id" class="form-select">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 mb-3" id="warehouse_selection" style="display: none;">
                            <label for="warehouse_id">Warehouse:</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-select">
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }} - {{ $warehouse->location }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr/>
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Ordered Quantity</th>
                                <th>Different Quantity?</th>
                                <th>Received Quantity</th>
                                <th>Price Change?</th>
                                <th>New Buying Price</th>
                                <th>New Selling Price</th>
                                <th>Not Supplied?</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($restock->items as $item)
                                <tr class="restock-item-row" data-item-id="{{ $item->id }}">
                                    <td>{{ $item->stock->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="ordered-quantity">{{ $item->ordered_quantity }}</span>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="different-quantity-check" name="different_quantity[{{ $item->id }}]" value="1">
                                    </td>
                                    <td>
                                        <input type="number" name="received_quantity[{{ $item->id }}]" 
                                               value="{{ $item->ordered_quantity }}" class="form-control received-quantity" required readonly>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="price-change-check" name="price_changed[{{ $item->id }}]" value="1">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="new_buying_price[{{ $item->id }}]" 
                                               value="{{ $item->new_buying_price }}" class="form-control new-buying-price" required readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="new_selling_price[{{ $item->id }}]" 
                                               value="{{ $item->new_selling_price }}" class="form-control new-selling-price" required readonly>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="not-supplied-check" name="not_supplied[{{ $item->id }}]" value="1">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary">Complete Restock</button>
                </form>
            @endif
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Handle storage location change
        $('#storage_location').on('change', function() {
            var selectedLocation = $(this).val();
            
            if (selectedLocation === 'shop') {
                $('#branch_selection').show();
                $('#warehouse_selection').hide();
                $('#branch_id').prop('required', true);
                $('#warehouse_id').prop('required', false);
            } else if (selectedLocation === 'warehouse') {
                $('#warehouse_selection').show();
                $('#branch_selection').hide();
                $('#warehouse_id').prop('required', true);
                $('#branch_id').prop('required', false);
            } else {
                $('#branch_selection').hide();
                $('#warehouse_selection').hide();
                $('#branch_id').prop('required', false);
                $('#warehouse_id').prop('required', false);
            }
        });

        // Existing item row functionality
        $('.restock-item-row').each(function() {
            var row = $(this);
            var itemId = row.data('item-id');
            var orderedQuantity = parseInt(row.find('.ordered-quantity').text());
            var originalBuyingPrice = parseFloat(row.find('.new-buying-price').val());
            var originalSellingPrice = parseFloat(row.find('.new-selling-price').val());

            row.find('.different-quantity-check').on('change', function() {
                var receivedQuantityInput = row.find('.received-quantity');
                if (this.checked) {
                    receivedQuantityInput.prop('readonly', false);
                } else {
                    receivedQuantityInput.prop('readonly', true).val(orderedQuantity);
                }
                updateRowColor(row);
            });

            row.find('.price-change-check').on('change', function() {
                var buyingPriceInput = row.find('.new-buying-price');
                var sellingPriceInput = row.find('.new-selling-price');
                if (this.checked) {
                    buyingPriceInput.prop('readonly', false);
                    sellingPriceInput.prop('readonly', false);
                } else {
                    buyingPriceInput.prop('readonly', true).val(originalBuyingPrice);
                    sellingPriceInput.prop('readonly', true).val(originalSellingPrice);
                }
                updateRowColor(row);
            });

            row.find('.not-supplied-check').on('change', function() {
                var receivedQuantityInput = row.find('.received-quantity');
                var differentQuantityCheck = row.find('.different-quantity-check');
                var priceChangeCheck = row.find('.price-change-check');
                
                if (this.checked) {
                    receivedQuantityInput.prop('readonly', true).val(0);
                    differentQuantityCheck.prop('checked', false).prop('disabled', true);
                    priceChangeCheck.prop('checked', false).prop('disabled', true);
                    row.find('.new-buying-price').prop('readonly', true).val(originalBuyingPrice);
                    row.find('.new-selling-price').prop('readonly', true).val(originalSellingPrice);
                } else {
                    receivedQuantityInput.val(orderedQuantity);
                    differentQuantityCheck.prop('disabled', false);
                    priceChangeCheck.prop('disabled', false);
                }
                updateRowColor(row);
            });
        });

        function updateRowColor(row) {
            row.removeClass('bg-warning bg-info bg-danger');
            if (row.find('.not-supplied-check').is(':checked')) {
                row.addClass('bg-danger');
            } else if (row.find('.price-change-check').is(':checked')) {
                row.addClass('bg-info');
            } else if (row.find('.different-quantity-check').is(':checked')) {
                row.addClass('bg-warning');
            }
        }

        // Form validation
        $('#completeRestockForm').on('submit', function(e) {
            var storageLocation = $('#storage_location').val();
            
            if (!storageLocation) {
                e.preventDefault();
                alert('Please select a storage location.');
                return false;
            }
            
            if (storageLocation === 'shop' && !$('#branch_id').val()) {
                e.preventDefault();
                alert('Please select a branch for shop storage.');
                return false;
            }
            
            if (storageLocation === 'warehouse' && !$('#warehouse_id').val()) {
                e.preventDefault();
                alert('Please select a warehouse.');
                return false;
            }
        });
    });
</script>
@endsection