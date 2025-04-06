@extends('layouts.app')
@section('PageTitle', 'All Sales')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <h2 class="text-bold fs-20">All Sales</h2>
                        </div>
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <div class="form-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search By Customer name or Note">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <div class="form-group">
                                <select class="form-select" id="cashier_id">
                                    <option value="">Sort by Cashier</option>
                                    <option value="all">All</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">
                                            {{ $staff->first_name . ' ' . $staff->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <select class="form-select" id="transaction_type">
                                    <option value="">Filter by Transaction Type</option>
                                    <option value="all">All</option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="pos">POS</option>
                                    <option value="credit">Credit</option>
                                    <option value="awaiting_pickup">Awaiting Pickup</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-data">
                            @include('sales.all_table')
                        </div>
                    </div>
                </div>

                <div class="modal">
                    <div id="print">
                        @include('transactions.receipt')
                    </div>
                </div>

            </div>
        </div>
    </section>



<!-- Awaiting Pickup Modal -->
<div class="modal fade" id="awaitingPickupModal" tabindex="-1" aria-labelledby="awaitingPickupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="awaitingPickupModalLabel">Mark Items as Awaiting Pickup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loadingSpinner" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading sale details...</p>
                </div>
                
                <div id="saleDetailsContent" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Receipt No:</strong> <span id="receipt_no"></span></p>
                            <p><strong>Customer:</strong> <span id="customer_name"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date:</strong> <span id="sale_date"></span></p>
                            <p><strong>Cashier:</strong> <span id="cashier_name"></span></p>
                        </div>
                    </div>
                    
                    <form id="awaitingPickupForm">
                        <input type="hidden" name="receipt_no" id="form_receipt_no">
                        <div class="form-group mb-3">
                            <label for="pickup_note">Note (Optional)</label>
                            <textarea class="form-control" id="pickup_note" name="note" rows="2" placeholder="Add any notes about this pickup..."></textarea>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Price (&#8358;)</th>
                                        <th class="text-center">Sold Qty</th>
                                        <th class="text-center">Awaiting Pickup</th>
                                        <th class="text-center">Select</th>
                                    </tr>
                                </thead>
                                <tbody id="saleItemsTableBody"></tbody>
                            </table>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="selectAllItems">
                            <label class="form-check-label" for="selectAllItems">Mark all items as awaiting pickup</label>
                        </div>
                    </form>
                </div>
                
                <div id="noSaleFound" style="display: none;">
                    <div class="alert alert-warning">
                        <p>No sale found with this receipt number.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveAwaitingPickup">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Deliver Items Modal -->
<div class="modal fade" id="deliverItemsModal" tabindex="-1" aria-labelledby="deliverItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deliverItemsModalLabel">Deliver Items to Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="deliveryLoadingSpinner" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading awaiting pickup details...</p>
                </div>
                
                <div id="deliveryDetailsContent" style="display: none;">
                    <div class="mb-3">
                        <p><strong>Receipt No:</strong> <span id="delivery_receipt_no"></span></p>
                    </div>
                    
                    <form id="deliverItemsForm">
                        <input type="hidden" name="receipt_no" id="delivery_form_receipt_no">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Price (&#8358;)</th>
                                        <th class="text-center">Awaiting Qty</th>
                                        <th class="text-center">Available Stock</th>
                                        <th class="text-center">Deliver Qty</th>
                                        <th class="text-center">Select</th>
                                    </tr>
                                </thead>
                                <tbody id="deliveryItemsTableBody"></tbody>
                            </table>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="selectAllDeliveryItems">
                            <label class="form-check-label" for="selectAllDeliveryItems">Deliver all awaiting items</label>
                        </div>
                    </form>
                </div>
                
                <div id="noPickupsFound" style="display: none;">
                    <div class="alert alert-warning">
                        <p>No awaiting pickups found for this receipt number.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveDelivery">Deliver Items</button>
            </div>
        </div>
    </div>
</div>


@endsection



@section('js')



    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.3/dist/sweetalert2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>


    <script>
              
        function PrintReceiptContent(receipt_no, transaction_type) {
            var data = {
                'receipt_no': receipt_no,
                'transaction_type': transaction_type,
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('refresh-receipt') }}",
                data: data,
                success: function(res) {
                    var html = '';
                    var total = 0;
                    var paidAmount = res.paid_amount || 0;

                    $.each(res.items, function(key, item) {
                        var productName = item.product.name;
                        var quantity = item.quantity;
                        var price = item.price;
                        var totalPrice = quantity * price;

                        html += '<tr style="text-align: center">' +
                            '<td style="text-align: left"><span style="font-size: 12px;">' +
                            productName + '</span></td>' +
                            '<td style="font-size: 12px;">' + quantity + '</td>' +
                            '<td style="font-size: 12px;">' + price.toLocaleString() + '</td>' +
                            '<td style="font-size: 12px;">' + totalPrice.toLocaleString() + '</td>' +
                            '</tr>';
                        total += totalPrice;
                    });

                    var laborCost = res.items[0].labor_cost ? parseInt(res.items[0].labor_cost) : 0;
                    if (laborCost) {
                        var subTotal = total;
                        total += laborCost;

                        html += '<tr style="text-align: center">' +
                            '<td></td>' +
                            '<td colspan="2"><b>Sub-total</b></td>' +
                            '<td><b>&#8358;' + subTotal.toLocaleString() + '</b></td>' +
                            '</tr>';

                        html += '<tr style="text-align: center">' +
                            '<td></td>' +
                            '<td colspan="2"><b>Labor Cost</b></td>' +
                            '<td><b>&#8358;' + laborCost.toLocaleString() + '</b></td>' +
                            '</tr>';
                    }

                    html += '<tr style="text-align: center">' +
                        '<td></td>' +
                        '<td colspan="2"><b>Total Amount</b></td>' +
                        '<td><b>&#8358;' + total.toLocaleString() + '</b></td>' +
                        '</tr>';

                    if (paidAmount > 0) {
                        var balance = total - paidAmount;

                        html += '<tr style="text-align: center">' +
                            '<td></td>' +
                            '<td colspan="2"><b>Amount Paid</b></td>' +
                            '<td><b>&#8358;' + paidAmount.toLocaleString() + '</b></td>' +
                            '</tr>';

                        html += '<tr style="text-align: center">' +
                            '<td></td>' +
                            '<td colspan="2"><b>Balance</b></td>' +
                            '<td><b>&#8358;' + balance.toLocaleString() + '</b></td>' +
                            '</tr>';
                    }

                    $('#receipt_body').html(html);
                    $('#transaction_type_span').html('<u>' + transaction_type + ' Receipt</u>');
                    $('#transaction_date_span').text(res.transaction_date);

                    var printableContent = document.getElementById('print').innerHTML;

                    var printWindow = window.open("", "myWin", "left=150, top=130, width=300, height=400");
                    printWindow.document.write(printableContent);
                    printWindow.document.title = "Print Estimate Certificate";
                    printWindow.focus();
                    printWindow.print();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    if (xhr.status === 419) {
                        toastr.error("Session expired. Please login again.");
                        setTimeout(function() {
                            window.location.replace('{{ route('login') }}');
                        }, 2000);
                    }
                },
            });
        }



        // Add this to your script section in index.blade.php

// Function to open awaiting pickup modal
function markAsAwaitingPickup(receiptNo) {
    // Reset the modal
    $('#saleDetailsContent').hide();
    $('#noSaleFound').hide();
    $('#loadingSpinner').show();
    $('#awaitingPickupForm')[0].reset();
    $('#saleItemsTableBody').empty();
    
    // Show the modal
    $('#awaitingPickupModal').modal('show');
    
    // Fetch sale details
    $.ajax({
        url: `/get-sale-details/${receiptNo}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#loadingSpinner').hide();
            
            if (response.status === 200) {
                $('#saleDetailsContent').show();
                
                // Set receipt info
                $('#receipt_no').text(receiptNo);
                $('#form_receipt_no').val(receiptNo);
                
                // Set customer and date info
                const firstSale = response.sales[0];
                $('#sale_date').text(moment(firstSale.created_at).format('MMMM Do YYYY, h:mm:ss a'));
                
                if (firstSale.customer === null) {
                    $('#customer_name').text('Walk-in Customer');
                } else if (firstSale.buyer) {
                    $('#customer_name').text(firstSale.buyer.first_name + ' ' + (firstSale.buyer.last_name || ''));
                }
                
                $('#cashier_name').text(firstSale.user.first_name + ' ' + firstSale.user.last_name);
                
                // Create a map of existing pickups for reference
                const existingPickups = {};
                if (response.existingPickups) {
                    response.existingPickups.forEach(pickup => {
                        if (!existingPickups[pickup.sale_id]) {
                            existingPickups[pickup.sale_id] = 0;
                        }
                        existingPickups[pickup.sale_id] += parseFloat(pickup.quantity);
                    });
                }
                
                // Populate the items table
                response.sales.forEach(sale => {
                    const stockItem = sale.stock;
                    const alreadyAwaitingPickup = existingPickups[sale.id] || 0;
                    const remainingQuantity = sale.quantity - alreadyAwaitingPickup;
                    
                    if (remainingQuantity > 0) {
                        const row = `
                            <tr>
                                <td>${stockItem.name}</td>
                                <td class="text-center">${formatNumber(sale.price)}</td>
                                <td class="text-center">${sale.quantity}</td>
                                <td class="text-center">
                                    <input type="number" class="form-control quantity-input" 
                                        name="items[${sale.id}][quantity]" 
                                        min="0" max="${remainingQuantity}" step="0.01" 
                                        value="0" 
                                        data-max="${remainingQuantity}"
                                        data-sale-id="${sale.id}">
                                    <input type="hidden" name="items[${sale.id}][sale_id]" value="${sale.id}">
                                    <input type="hidden" name="items[${sale.id}][stock_id]" value="${sale.stock_id}">
                                    <input type="hidden" name="items[${sale.id}][price]" value="${sale.price}">
                                </td>
                                <td class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input item-checkbox" type="checkbox" value="" 
                                            data-sale-id="${sale.id}" 
                                            data-quantity="${remainingQuantity}">
                                    </div>
                                </td>
                            </tr>
                        `;
                        $('#saleItemsTableBody').append(row);
                    }
                });
                
                // If no items left to mark for pickup
                if ($('#saleItemsTableBody').children().length === 0) {
                    $('#saleItemsTableBody').html(`
                        <tr>
                            <td colspan="5" class="text-center">
                                All items from this sale are already marked as awaiting pickup.
                            </td>
                        </tr>
                    `);
                    $('#saveAwaitingPickup').prop('disabled', true);
                    $('#selectAllItems').prop('disabled', true);
                } else {
                    $('#saveAwaitingPickup').prop('disabled', false);
                    $('#selectAllItems').prop('disabled', false);
                }
            } else {
                $('#noSaleFound').show();
            }
        },
        error: function() {
            $('#loadingSpinner').hide();
            $('#noSaleFound').show();
        }
    });
}

// Function to open delivery modal
function deliverItems(receiptNo) {
    // Reset the modal
    $('#deliveryDetailsContent').hide();
    $('#noPickupsFound').hide();
    $('#deliveryLoadingSpinner').show();
    $('#deliverItemsForm')[0].reset();
    $('#deliveryItemsTableBody').empty();
    
    // Show the modal
    $('#deliverItemsModal').modal('show');
    
    // Fetch awaiting pickup details
    $.ajax({
        url: `/get-awaiting-pickup/${receiptNo}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#deliveryLoadingSpinner').hide();
            
            if (response.status === 200) {
                $('#deliveryDetailsContent').show();
                
                // Set receipt info
                $('#delivery_receipt_no').text(receiptNo);
                $('#delivery_form_receipt_no').val(receiptNo);
                
                // Populate the items table
                response.awaitingPickups.forEach(pickup => {
                    const stockItem = pickup.stock;
                    
                    const row = `
                        <tr>
                            <td>${stockItem.name}</td>
                            <td class="text-center">${formatNumber(pickup.price)}</td>
                            <td class="text-center">${pickup.quantity}</td>
                            <td class="text-center">${stockItem.quantity}</td>
                            <td class="text-center">
                                <input type="number" class="form-control delivery-quantity-input" 
                                    name="items[${pickup.id}][quantity]" 
                                    min="0" max="${pickup.quantity}" step="0.01" 
                                    value="0" 
                                    data-max="${pickup.quantity}"
                                    data-stock="${stockItem.quantity}"
                                    data-pickup-id="${pickup.id}">
                                <input type="hidden" name="items[${pickup.id}][pickup_id]" value="${pickup.id}">
                            </td>
                            <td class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input delivery-item-checkbox" type="checkbox" value="" 
                                        data-pickup-id="${pickup.id}" 
                                        data-quantity="${pickup.quantity}"
                                        data-stock="${stockItem.quantity}">
                                </div>
                            </td>
                        </tr>
                    `;
                    $('#deliveryItemsTableBody').append(row);
                });
                
                // If no items to deliver
                if ($('#deliveryItemsTableBody').children().length === 0) {
                    $('#deliveryItemsTableBody').html(`
                        <tr>
                            <td colspan="6" class="text-center">
                                No items awaiting pickup for this receipt.
                            </td>
                        </tr>
                    `);
                    $('#saveDelivery').prop('disabled', true);
                    $('#selectAllDeliveryItems').prop('disabled', true);
                } else {
                    $('#saveDelivery').prop('disabled', false);
                    $('#selectAllDeliveryItems').prop('disabled', false);
                }
            } else {
                $('#noPickupsFound').show();
            }
        },
        error: function() {
            $('#deliveryLoadingSpinner').hide();
            $('#noPickupsFound').show();
        }
    });
}

// Helper function to format number with commas
function formatNumber(num) {
    return parseFloat(num).toLocaleString('en-NG');
}

// Add the actions to your existing dropdown menu
// This function can modify your existing table's action dropdown
function updateActionDropdowns() {
    // Add the awaiting pickup and delivery options to your action dropdowns
    $('.dropdown-menu').each(function() {
        const receiptNo = $(this).find('a:first').attr('onclick')?.match(/'([^']+)'/)?.[1];
        
        if (receiptNo) {
            const awaitingPickupOption = `
                <a class="dropdown-item" href="#" onclick="markAsAwaitingPickup('${receiptNo}')">
                    <i class="fa fa-box"></i> Mark for Pickup
                </a>
            `;
            
            const deliverOption = `
                <a class="dropdown-item" href="#" onclick="deliverItems('${receiptNo}')">
                    <i class="fa fa-truck"></i> Deliver Items
                </a>
            `;
            
            // Add options if they don't already exist
            if (!$(this).find('a:contains("Mark for Pickup")').length) {
                $(this).append(awaitingPickupOption);
            }
            
            if (!$(this).find('a:contains("Deliver Items")').length) {
                $(this).append(deliverOption);
            }
        }
    });
}

// Document ready function
$(document).ready(function() {
    // Update action dropdowns when page loads
    updateActionDropdowns();
    
    // Handle "Select All" checkbox for awaiting pickup
    $('#selectAllItems').change(function() {
        const isChecked = $(this).prop('checked');
        
        $('.item-checkbox').each(function() {
            $(this).prop('checked', isChecked);
            
            const saleId = $(this).data('sale-id');
            const maxQuantity = $(this).data('quantity');
            
            // Update the quantity input
            $(`input[name="items[${saleId}][quantity]"]`).val(isChecked ? maxQuantity : 0);
        });
    });
    
    // Handle individual item checkboxes for awaiting pickup
    $(document).on('change', '.item-checkbox', function() {
        const isChecked = $(this).prop('checked');
        const saleId = $(this).data('sale-id');
        const maxQuantity = $(this).data('quantity');
        
        // Update the quantity input
        $(`input[name="items[${saleId}][quantity]"]`).val(isChecked ? maxQuantity : 0);
        
        // Update "Select All" checkbox
        updateSelectAllCheckbox();
    });
    
    // Handle "Select All" checkbox for delivery
    $('#selectAllDeliveryItems').change(function() {
        const isChecked = $(this).prop('checked');
        
        $('.delivery-item-checkbox').each(function() {
            const pickupId = $(this).data('pickup-id');
            const maxQuantity = $(this).data('quantity');
            const stockQuantity = $(this).data('stock');
            
            // Only check if there's enough stock
            const canCheck = stockQuantity >= maxQuantity;
            
            if (isChecked) {
                $(this).prop('checked', canCheck);
                
                // Update the quantity input
                $(`input[name="items[${pickupId}][quantity]"]`).val(canCheck ? maxQuantity : 0);
                
                if (!canCheck) {
                    alert(`Not enough stock for one or more items. Please adjust quantities manually.`);
                }
            } else {
                $(this).prop('checked', false);
                $(`input[name="items[${pickupId}][quantity]"]`).val(0);
            }
        });
    });
    
    // Handle individual item checkboxes for delivery
    $(document).on('change', '.delivery-item-checkbox', function() {
        const isChecked = $(this).prop('checked');
        const pickupId = $(this).data('pickup-id');
        const maxQuantity = $(this).data('quantity');
        const stockQuantity = $(this).data('stock');
        // Check if there's enough stock
        if (isChecked && stockQuantity < maxQuantity) {
            alert(`Not enough stock (${stockQuantity}) to deliver the requested quantity (${maxQuantity}). Please adjust manually.`);
            $(this).prop('checked', false);
            $(`input[name="items[${pickupId}][quantity]"]`).val(0);
        } else {
            // Update the quantity input
            $(`input[name="items[${pickupId}][quantity]"]`).val(isChecked ? maxQuantity : 0);
        }
        
        // Update "Select All" checkbox
        updateDeliverySelectAllCheckbox();
    });
    
    // Function to update "Select All" checkbox state
    function updateSelectAllCheckbox() {
        const totalCheckboxes = $('.item-checkbox').length;
        const checkedCheckboxes = $('.item-checkbox:checked').length;
        
        $('#selectAllItems').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    }
    
    // Function to update "Select All" checkbox state for delivery
    function updateDeliverySelectAllCheckbox() {
        const totalCheckboxes = $('.delivery-item-checkbox').length;
        const checkedCheckboxes = $('.delivery-item-checkbox:checked').length;
        
        $('#selectAllDeliveryItems').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    }
    
    // Save awaiting pickup
    $('#saveAwaitingPickup').click(function() {
        // Prepare the data
        const formData = {
            receipt_no: $('#form_receipt_no').val(),
            note: $('#pickup_note').val(),
            items: []
        };
        
        // Get items with quantity > 0
        $('.quantity-input').each(function() {
            const quantity = parseFloat($(this).val());
            if (quantity > 0) {
                const saleId = $(this).data('sale-id');
                
                formData.items.push({
                    sale_id: $(`input[name="items[${saleId}][sale_id]"]`).val(),
                    stock_id: $(`input[name="items[${saleId}][stock_id]"]`).val(),
                    price: $(`input[name="items[${saleId}][price]"]`).val(),
                    quantity: quantity
                });
            }
        });
        
        // Validate that at least one item is selected
        if (formData.items.length === 0) {
            alert('Please select at least one item to mark for pickup.');
            return;
        }
        
        // Send the request
        $.ajax({
            url: '/mark-awaiting-pickup',
            type: 'POST',
            dataType: 'json',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#saveAwaitingPickup').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );
            },
            success: function(response) {
                if (response.status === 201) {
                    alert(response.message);
                    $('#awaitingPickupModal').modal('hide');
                    
                    // Reload the page to reflect changes
                    location.reload();
                } else {
                    alert(response.message || 'An error occurred while marking items for pickup.');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'An error occurred while marking items for pickup.');
            },
            complete: function() {
                $('#saveAwaitingPickup').prop('disabled', false).text('Save');
            }
        });
    });
    
    // Save delivery
    $('#saveDelivery').click(function() {
        // Prepare the data
        const formData = {
            receipt_no: $('#delivery_form_receipt_no').val(),
            items: []
        };
        
        // Get items with quantity > 0
        $('.delivery-quantity-input').each(function() {
            const quantity = parseFloat($(this).val());
            if (quantity > 0) {
                const pickupId = $(this).data('pickup-id');
                
                formData.items.push({
                    pickup_id: $(`input[name="items[${pickupId}][pickup_id]"]`).val(),
                    quantity: quantity
                });
            }
        });
        
        // Validate that at least one item is selected
        if (formData.items.length === 0) {
            alert('Please select at least one item to deliver.');
            return;
        }
        
        // Send the request
        $.ajax({
            url: '/deliver-items',
            type: 'POST',
            dataType: 'json',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#saveDelivery').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Delivering...'
                );
            },
            success: function(response) {
                if (response.status === 200) {
                    alert(response.message);
                    $('#deliverItemsModal').modal('hide');
                    
                    // Reload the page to reflect changes
                    location.reload();
                } else {
                    alert(response.message || 'An error occurred while delivering items.');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'An error occurred while delivering items.');
            },
            complete: function() {
                $('#saveDelivery').prop('disabled', false).text('Deliver Items');
            }
        });
    });
    
    // Input validation for quantities
    $(document).on('input', '.quantity-input', function() {
        const max = parseFloat($(this).data('max'));
        const val = parseFloat($(this).val());
        
        if (val > max) {
            $(this).val(max);
        }
    });
    
    // Input validation for delivery quantities
    $(document).on('input', '.delivery-quantity-input', function() {
        const max = parseFloat($(this).data('max'));
        const stock = parseFloat($(this).data('stock'));
        const val = parseFloat($(this).val());
        
        const actualMax = Math.min(max, stock);
        
        if (val > actualMax) {
            $(this).val(actualMax);
            
            if (stock < max) {
                alert(`Available stock (${stock}) is less than awaiting pickup quantity (${max}). Adjusted to maximum available.`);
            }
        }
    });
});

    </script>

    <script>
        function handleSearch() {
            var query = $('#searchInput').val();

            $('.pagination').hide();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('sales.all.search') }}',
                method: 'POST',
                data: {
                    query: query
                },
                success: function(response) {
                    // Empty the table
                    $('.table').empty();

                    // Check if the response is empty
                    if ($(response).find('tbody tr').length > 0) {
                        $('.table').html(response);
                    } else {
                        // Display a message if no rows are found
                        $('.table tbody').empty().append(
                            '<tr><td colspan="9" class="text-center">No results found.</td></tr>');
                        toastr.warning('No results found.');
                    }

                },

                error: function(xhr) {
                    // Handle the error response here
                    console.log(xhr.responseText);
                }
            });
        }
        $('#searchInput').on('input', handleSearch);
    </script>


    <script>
        $(document).ready(function() {
            $('#cashier_id, #transaction_type').on('change', function() {

                var cashierId = $('#cashier_id').val();
                var transactionType = $('#transaction_type').val();
                $.LoadingOverlay("show")
                $('.pagination').hide();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route('sales.all.sort') }}',
                    method: 'POST',
                    data: {
                        cashier_id: cashierId,
                        transaction_type: transactionType
                    },
                    success: function(response) {
                        $('.table').empty();
                        $.LoadingOverlay("hide")
                        $('.table').html(response);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>



    <script>
        function confirmDeliver(receiptNo) {
            Swal.fire({
                title: "Confirm Delivery",
                text: 'Are you sure you want to mark sale ' + receiptNo + ' as Delivered?',
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, items Delivered!",
                cancelButtonText: "Cancel",
                dangerMode: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    markAsDeivered(receiptNo);
                }
            });
        }

        function markAsDeivered(receiptNo) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('sales.deliver') }}',
                method: 'POST',
                data: {
                    receiptNo: receiptNo
                },
                success: function(response) {
                    // Handle the success response from the backend
                    Swal.fire('Success', response.message);
                    $('.table').load(location.href + ' .table');

                },
                error: function(xhr, status, error) {
                    // Handle the error response from the backend
                    Swal.fire('Error', 'Failed to confirm pickup. Please try again.', 'error');
                }
            });
        }
    </script>

    <script>
        function confirmPickup(receiptNo) {
            Swal.fire({
                title: "Confirm Awaiting Pickup",
                text: 'Are you sure you want to mark sale ' + receiptNo + ' as Awaiting Pickup?',
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, items Not received!",
                cancelButtonText: "Cancel",
                dangerMode: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    sendReceiptNumber(receiptNo);
                }
            });
        }

        function sendReceiptNumber(receiptNo) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('sales.awaiting_pickup') }}',
                method: 'POST',
                data: {
                    receiptNo: receiptNo
                },
                success: function(response) {
                    // Handle the success response from the backend
                    Swal.fire('Success', 'Sale ' + receiptNo +' marked as awaiting pickup');
                    $('.table').load(location.href + ' .table');

                },
                error: function(xhr, status, error) {
                    // Handle the error response from the backend
                    Swal.fire('Error', 'Failed to confirm pickup. Please try again.', 'error');
                }
            });
        }
    </script>




@endsection
