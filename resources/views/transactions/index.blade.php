@extends('layouts.app')
@section('PageTitle', 'Record a Sale')

@section('css')
    <style>
        #productSuggestions {
            list-style-type: none;
            padding: 0;
            margin-top: 5px;
        }

        #productSuggestions li {
            background-color: #f8f9fa;
            padding: 5px;
            cursor: pointer;
            border: 1px solid #ccc;
            border-radius: 3px;
            margin-bottom: 5px;
        }

        #productSuggestions li:hover {
            background-color: #e9ecef;
        }

        .total-amount {
            border: 1px solid #007BFF;
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            display: inline-block;
        }

        #totalAmount {
            font-size: 24px;
            margin: 0;
            color: #007BFF;
        }

        #productSearch {
            border: 2px solid #007BFF;
            border-radius: 5px;
            padding: 12px;
            font-size: 18px;
            width: 100%;
            outline: none;
            background-color: #f8f9fa;
        }

        #productSearch:focus {
            border-color: #1eaa08;
        }

        #productSearch::placeholder {
            opacity: 0.5;
        }

        #noMatchFound {
            color: #d9534f;
            font-weight: bold;
            display: none;
        }

        #balanceContainer {
            display: none;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        #balanceContainer p {
            margin: 0;
            line-height: 2.2;
        }

        #previousBalance,
        #newBalance {
            font-weight: bold;
            margin-left: 10px;

        }

        .thin-input {
            width: 100%;
        }

        .form-control.thin-input {
            padding: 0.375rem 0.75rem;
            font-size: 0.755rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .form-control.thin-input:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }



        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .quantity-control .btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 6px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .quantity-control .minus-btn {
            background: linear-gradient(145deg, #ff6b6b, #f03e3e);
            color: white;
        }

        .quantity-control .plus-btn {
            background: linear-gradient(145deg, #37b24d, #2f9e44);
            color: white;
        }

        .quantity-control .minus-btn:hover {
            background: linear-gradient(145deg, #f03e3e, #e03131);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .quantity-control .plus-btn:hover {
            background: linear-gradient(145deg, #2f9e44, #2b8a3e);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .quantity-control .btn:active {
            transform: translateY(1px);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .quantity-control .btn i {
            font-size: 0.875rem;
            line-height: 1;
        }

        .quantity-control .quantity {
            width: 70px !important;
            height: 32px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-weight: 500;
            background-color: #fff;
            -moz-appearance: textfield;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.075);
        }

        .quantity-control .quantity:focus {
            border-color: #4dabf7;
            box-shadow: 0 0 0 3px rgba(77, 171, 247, 0.25);
            outline: none;
        }

        .quantity-control .quantity::-webkit-outer-spin-button,
        .quantity-control .quantity::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Hover effect for the entire control group */
        .quantity-control:hover .quantity {
            border-color: #adb5bd;
        }
         /* Animation for button clicks */
         @keyframes buttonPress {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(0.95);
            }

            100% {
                transform: scale(1);
            }
        }

        .quantity-control .btn:active {
            animation: buttonPress 0.2s ease;
        }

        /* Make buttons more prominent on mobile */
        @media (max-width: 768px) {
            .quantity-control .btn {
                width: 36px;
                height: 36px;
            }

            .quantity-control .btn i {
                font-size: 1rem;
            }

            .quantity-control .quantity {
                height: 36px;
            }
        }
    </style>
@endsection

@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <form id="salesForm">
                    <div class="row">
                        <div class="col-md-8 col-12 mb-3">
                            <div class="card mb-3">
                                <div class="card-header bg-transparent">
                                    <marquee class="text-danger" behavior="scroll" direction="left"
                                        style="white-space: nowrap;">
                                        Welcome to El-Habib Plumbing Material and Services Ltd -
                                        {{ auth()->user()->branch->name }} Branch
                                    </marquee>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <input type="text" id="productSearch" class="form-control"
                                            placeholder="Search for products">
                                        <ul id="productSuggestions"></ul>
                                        <p id="noMatchFound" class="text-danger">No match found</p>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="sn-column">S/N</th>
                                                    <th>Item</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Discount</th>
                                                    <th>Total</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="productTable">
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </div>
                            @include('transactions.recent_sales_table')
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="total-amount mb-2">
                                        <p id="totalAmount" class="font-weight-bold">₦0</p>
                                    </div>

                                    <div class="row">
                                        <table class="table table-striped">
                                            <tr>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="customer_id">Customer Name</label>
                                                                <div class="input-group">
                                                                    <select class="form-control select2" name="customer" id="customer">
                                                                        <option value="0">Walk-in Customer</option>
                                                                        @foreach ($customers as $customer)
                                                                            <option value="{{ $customer->id }}">
                                                                                {{ $customer->first_name . ' ' . $customer->last_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <button type="button" class="btn btn-primary" id="addCustomerBtn" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="note">Note</label>
                                                            <input type="text" name="note" id="note"
                                                                class="form-control thin-input">
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                        </table>

                                        <div class="form-group">
                                            <label for="transactionType">Transaction Type</label><br>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="transaction_type"
                                                    id="sales" value="sales" required>
                                                <label class="form-check-label" for="sales">Sales</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="transaction_type"
                                                    id="return" value="return" required>
                                                <label class="form-check-label" for="return">Return</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="transaction_type"
                                                    id="estimate" value="estimate" required>
                                                <label class="form-check-label" for="estimate">Estimate</label>
                                            </div>
                                        </div>

                                        <div id="paymentMethodSection" class="col-12 form-group">
                                            <label>Payment Channel:</label><br>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input required" type="radio"
                                                    name="payment_method" id="cash" value="cash" required>
                                                <label class="form-check-label nott" for="cash"><i
                                                        class="fas fa-money-bill text-success"></i> Cash</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="payment_method"
                                                    id="pos" value="pos" required>
                                                <label class="form-check-label nott" for="pos"><i
                                                        class="fa fa-credit-card text-info"></i> POS</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="payment_method"
                                                    id="transfer" value="transfer" required>
                                                <label class="form-check-label nott" for="transfer"><i
                                                        class="fa fa-university text-danger"></i> Bank Transfer</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="payment_method"
                                                    id="credit" value="credit" required>
                                                <label class="form-check-label nott" for="credit"><i
                                                        class="fa fa-credit-card text-warning"></i> Credit</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="payment_method"
                                                    id="deposit" value="deposit" required>
                                                <label class="form-check-label nott" for="deposit"><i
                                                        class="fa fa-credit-card text-success"></i> Deposit</label>
                                            </div>
                                            <!-- Add the "Multiple" option -->
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="payment_method"
                                                    id="multiple" value="multiple" required>
                                                <label class="form-check-label nott" for="multiple"><i
                                                        class="fa fa-list text-primary"></i> Multiple</label>
                                            </div>

                                        </div>

                                        <div id="balanceContainer" class="mb-2"
                                            style="display: none; margin-top: 0px;">
                                            <p style="line-height: 1.5;">
                                                <span style="font-weight: bold;">Previous Balance:</span>
                                                <span id="previousBalance" style="margin-left: 10px;">0</span>
                                            </p>
                                            {{-- <p style="line-height: 1.5;">
                                                <span style="font-weight: bold;">New Balance:</span>
                                                <span id="newBalance" style="margin-left: 27px;">0</span>
                                            </p> --}}
                                        </div>

                                        <div class="form-group" id="addLaborCostField">
                                            <label for="toggleLabor">Add Labor Costs</label>
                                            <label class="switch">
                                                <input type="checkbox" name="toggleLabor" id="toggleLabor">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>

                                        <div id="laborCostField" style="display: none; padding: 0; margin: 0;">
                                            Labor Cost
                                            <input type="number" name="labor_cost" id="laborCost"
                                                class="form-control mb-2">
                                        </div>

                                        <style>
                                            .multiple-payment-field {
                                                border-color: red;
                                            }
                                        </style>

                                        <div id="multiplePaymentsSection" class="col-12 form-group"
                                            style="display: none;">
                                            <label>Multiple Payments:</label><br>
                                            <div class="form-group">
                                                <label for="cashAmount">Cash:</label>
                                                <input type="number" class="form-control" name="cashAmount"
                                                    id="cashAmount">
                                            </div>
                                            <div class="form-group">
                                                <label for="posAmount">POS:</label>
                                                <input type="number" class="form-control" name="posAmount"
                                                    id="posAmount">
                                            </div>
                                            <div class="form-group">
                                                <label for="transferAmount">Transfer:</label>
                                                <input type="number" class="form-control" name="transferAmount"
                                                    id="transferAmount">
                                            </div>
                                        </div>

                                        <div id="paidAmountField" style="padding: 0; margin: 0;">
                                            <span id="paid_amount_span"> Amount Paid</span>
                                            <input type="number" name="paid_amount" id="paid_amount"
                                                class="form-control mb-2">
                                        </div>
                                        <div id="partialAmountField" style="display: none;padding: 0; margin: 0;">
                                            <span>Partial Amount Payment Channel</span>
                                            <select class="form-select" name="partial_payment_method">
                                                <option value=""></option>
                                                <option value="cash">Cash</option>
                                                <option value="transfer">Transfer</option>
                                                <option value="pos">POS</option>
                                            </select>
                                        </div>

                                        <div id="changeField" style="display: none;">
                                            Returning Change:
                                            <span id="balance" class="font-weight-bold"></span>
                                        </div>
                                        <button type="submit" id="submitBtn"
                                            class="btn btn-primary btn-lg btn-block mt-2">Record Transaction</button>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal">
                    <div id="print">
                        @include('transactions.receipt')
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Modal HTML -->
<div id="whatsappModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Receipt via WhatsApp</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="whatsappForm">
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number</label>
                        <input type="text" class="form-control" id="phoneNumber" placeholder="Enter phone number">
                    </div>
                    <button type="button" class="btn btn-primary" id="sendWhatsAppButton">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>


    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCustomerForm">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveCustomerBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="customerSpinner"></span>
                        Save Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Ensure the select2 container takes full width */
        .select2-container {
            width: 100% !important;
        }
        
        /* Fix for mobile view */
        @media (max-width: 767.98px) {
            .input-group .select2-container {
                width: calc(100% - 38px) !important;
            }
        }
        
        /* Make sure the button stays with the select field */
        #addCustomerBtn {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            padding: 0;
        }
        
        /* Fix for Select2 container in Bootstrap 5 */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            display: flex;
            align-items: center;
        }
        
        /* Ensure the input group maintains proper display */
        .input-group {
            display: flex;
            flex-wrap: nowrap;
        }
        </style>

@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#customer_id').select2({
            theme: 'classic'
        });
    });
</script>




<!-- Scripts for Customer Management -->
<script>
$(document).ready(function() {
    // Initialize Select2
    if($.fn.select2) {
    $('.select2').select2({
        width: '100%',
        dropdownAutoWidth: true,
        dropdownParent: $('body') // This helps with modal compatibility
    }).on('select2:open', function() {
        // Fix dropdown width on open
        $('.select2-dropdown').css('width', $(this).parent().width());
    });
}
    
    // Open the modal when the add button is clicked
    $('#addCustomerBtn').click(function() {
        $('#addCustomerForm')[0].reset();
        $('#addCustomerModal').modal('show');
    });
    
    // Save customer when the save button is clicked
    $('#saveCustomerBtn').click(function() {
        // Validate form
        if (!$('#addCustomerForm')[0].checkValidity()) {
            $('#addCustomerForm')[0].reportValidity();
            return;
        }
        
        // Disable button and show spinner
        const btn = $(this);
        btn.prop('disabled', true);
        $('#customerSpinner').removeClass('d-none');
        
        // Prepare form data
        const formData = {
            name: $('#name').val(),
            phone: $('#phone').val(),
            email: $('#email').val() || null,
            address: $('#address').val() || null,
            _token: $('input[name="_token"]').val()
        };
        
        // Submit via AJAX
        $.ajax({
            url: "",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Update the customer dropdown
                    const customerSelect = $('#customer_id');
                    customerSelect.empty();
                    
                    // Add walk-in customer option
                    customerSelect.append(new Option('Walk-in Customer', '0'));
                    
                    // Add all customers with the new one at the top
                    const newOption = new Option(response.newCustomer.name, response.newCustomer.id, true, true);
                    $(newOption).addClass('new-customer-highlight');
                    customerSelect.append(newOption);
                    
                    // Add other customers
                    response.customers.forEach(function(customer) {
                        if (customer.id !== response.newCustomer.id) {
                            customerSelect.append(new Option(customer.name, customer.id));
                        }
                    });
                    
                    // If using Select2, refresh it
                    if($.fn.select2) {
                        customerSelect.trigger('change');
                    }
                    
                    // Show success message
                    const toast = new bootstrap.Toast(document.getElementById('customerSuccessToast'));
                    toast.show();
                    
                    // Close modal
                    $('#addCustomerModal').modal('hide');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while saving the customer.';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = '';
                    for (const key in xhr.responseJSON.errors) {
                        errorMessage += xhr.responseJSON.errors[key][0] + '\n';
                    }
                }
                
                alert(errorMessage);
            },
            complete: function() {
                // Re-enable button and hide spinner
                btn.prop('disabled', false);
                $('#customerSpinner').addClass('d-none');
            }
        });
    });
});
</script>
    <script>
        $(document).ready(function() {
            updateSections();

            $("input[name='transaction_type']").change(updateSections);
        });

        function updateSections() {
            var selectedTransactionType = $("input[name='transaction_type']:checked").val();
            var paymentMethodSection = $("#paymentMethodSection");
            var paymentMethodInputs = $("input[name='payment_method']");
            var paidAmountField = $("#paidAmountField");
            var partialAmountField = $("#partialAmountField");
            var addLaborCostField = $("#addLaborCostField");
            var laborCostField = $("#laborCostField");

            if (selectedTransactionType === "estimate") {
                paymentMethodSection.hide();
                paidAmountField.hide();
                partialAmountField.hide();
                $("#changeField").hide();
                $('#balanceContainer').hide();
                $('#multiplePaymentsSection').hide();
                addLaborCostField.show();
                paymentMethodInputs.removeAttr("required");
            } else if (selectedTransactionType === "return") {
                paymentMethodSection.show();
                $("#changeField").hide();
                paidAmountField.hide();
                partialAmountField.hide();
                $('#balanceContainer').hide();
                $('#multiplePaymentsSection').hide();
                addLaborCostField.hide();
                laborCostField.hide();
                paymentMethodInputs.attr("required", true);
            } else {
                paymentMethodSection.show();
                paidAmountField.show();
                partialAmountField.hide();
                addLaborCostField.show();

                paymentMethodInputs.attr("required", true);
            }
        }
    </script>



    <script>
        $(document).ready(function() {

            function updateTotalMultiplePayments() {
                var cashAmount = parseFloat($('#cashAmount').val()) || 0;
                var posAmount = parseFloat($('#posAmount').val()) || 0;
                var transferAmount = parseFloat($('#transferAmount').val()) || 0;
                var totalMultiplePayments = cashAmount + posAmount + transferAmount;

                // Create or update the totalAmountMultiplePayments element
                var totalAmountMultiplePayments = $('#totalAmountMultiplePayments');
                if (totalAmountMultiplePayments.length === 0) {
                    totalAmountMultiplePayments = $(
                        '<p id="totalAmountMultiplePayments" class="font-weight-bold"></p>');
                    $('#multiplePaymentsSection').append(totalAmountMultiplePayments);
                }

                totalAmountMultiplePayments.text('₦' + totalMultiplePayments.toLocaleString());
            }

            $('input[name="payment_method"]').change(function() {

                var selectedPaymentMethod = $('input[name="payment_method"]:checked').val();
                var selectedUserId = $('#customer').val();

                if (selectedPaymentMethod === 'credit' || selectedPaymentMethod === 'deposit') {
                    if (selectedUserId == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Please select a user before choosing the payment method.',
                        });
                        $('input[name="payment_method"]').prop('checked', false);
                        return;
                    }

                    $.ajax({
                        url: '/fetch-credit-balance',
                        method: 'GET',
                        data: {
                            payment_method: selectedPaymentMethod,
                            user_id: selectedUserId,
                        },
                        success: function(response) {

                            $('#balanceContainer').show();
                            if (selectedPaymentMethod === 'credit') {

                                $('#previousBalanceLabel').text('Previous Credit Balance:');
                                $('#previousBalance').text(response.balance_or_deposit);
                                var newCreditBalance = parseFloat(response.balance_or_deposit) +
                                    parseFloat($('#totalAmount').text().replace('₦', '')
                                        .replace(',', ''));
                                $('#newBalanceLabel').text('New Credit Balance:');
                                $('#newBalance').text(newCreditBalance.toLocaleString());

                                $("#paid_amount_span").text(
                                    'Partial Cash Amount Paid (if any)');
                                $("#paidAmountField").show();
                                $("#partialAmountField").show();
                                $("#changeField").hide();

                            } else if (selectedPaymentMethod === 'deposit') {

                                $('#previousBalanceLabel').text('Previous Deposit Balance:');
                                $('#previousBalance').text(response.balance_or_deposit);

                                var newDepositBalance = parseFloat(response
                                    .balance_or_deposit) - parseFloat($('#totalAmount')
                                    .text()
                                    .replace('₦', '').replace(',', ''));
                                $('#newBalanceLabel').text('New Deposit Balance:');
                                $('#newBalance').text(newDepositBalance.toLocaleString());
                            }
                        },
                        error: function(error) {
                            console.error('Error fetching balance:', error);
                        }
                    });
                } else {

                    $('#balanceContainer').hide();
                    $("#paid_amount_span").text('Cash Amount Paid');
                }
                if (selectedPaymentMethod === 'cash') {
                    $("#paidAmountField").show();
                    $("#partialAmountField").hide();

                } else {
                    $("#paidAmountField").hide();
                    $("#partialAmountField").hide();
                }

                if (selectedPaymentMethod === 'multiple') {
                    // Show additional form fields for multiple payments
                    $('#multiplePaymentsSection').show();
                    updateTotalMultiplePayments();

                } else {
                    $('#multiplePaymentsSection').hide();

                }

            });

            $('#multiplePaymentsSection input').on('input', function() {
                updateTotalMultiplePayments();
            });


        });
    </script>

    <script>
        var transactionType = $("input[name='transaction_type']:checked").val();

        $("input[name='transaction_type']").change(function() {
            transactionType = $(this).val();
        });

        $(document).ready(function() {


            var $productSearch = $('#productSearch');
            var $productSuggestions = $('#productSuggestions');
            var $productTable = $('#productTable');
            var $noMatchFound = $('#noMatchFound');

            function fetchProductSuggestions(query) {
                $.ajax({
                    url: '/get-product-suggestions',
                    type: 'GET',
                    data: {
                        query: query
                    },
                    success: function(suggestions) {
                        $productSuggestions.empty();
                        if (suggestions.length === 0) {
                            $noMatchFound.show();
                        } else {
                            $noMatchFound.hide();

                            suggestions.forEach(function(suggestion) {
                                var $li = $('<li>').text(suggestion.name);
                                $li.click(function() {

                                    var exists = false;
                                    $productTable.find('td:nth-child(2)').each(
                                        function() {
                                            if ($(this).text() === suggestion
                                                .name) {
                                                exists = true;
                                                return false;
                                            }
                                        });
                                    if (!exists) {
                                        appendProductToTable(suggestion);
                                    } else {
                                        alert('Product already exists in the table.');
                                    }
                                });
                                $productSuggestions.append($li);
                            });
                        }
                    }
                });
            }

            function calculateChange() {
                var totalAmount = parseFloat($('#totalAmount').text().replace('₦', '').replace(',',
                    ''));
                var paidAmount = parseFloat($('#paid_amount').val()) || 0;
                var paymentMethod = $("input[name='payment_method']:checked").val();
                var change = paidAmount - totalAmount;

                if (paymentMethod === "cash" || paymentMethod === "pos" || paymentMethod === "credit") {
                    if (change > 0) {
                        $('#changeField').show();
                        $('#balance').text('₦' + change
                            .toLocaleString());
                    } else {
                        $('#changeField').hide();
                    }
                } else {
                    $('#changeField').hide();
                }
            }

            $('#paid_amount').on('input', calculateChange);

            function calculateRowTotal($row) {
                var price = parseFloat($row.find('.price').text());
                var quantity = parseFloat($row.find('.quantity').val()) || 0;
                var discount = parseFloat($row.find('.discount').val()) || 0;
                var total = (price * quantity) - discount;
                return total.toFixed(0);
            }

            function updateTotalAmount() {
                var totalAmount = 0;
                $productTable.find('tr').each(function() {
                    totalAmount += parseFloat(calculateRowTotal($(this)));
                });
                var formattedTotal = '₦' + totalAmount.toLocaleString();
                $('#totalAmount').text(formattedTotal);
            }

            function appendProductToTable(product) {
                var newRow = "<tr>" +
                    "<td class='sn-column'></td>" +
                    "<td>" + product.name + "</td>" +
                    "<td class='price'>" + product.selling_price + "</td>" +
                    "<td>" +
                        "<div class='quantity-control d-flex align-items-center'>" +
                            "<button type='button' class='btn quantity-btn minus-btn'>" +
                                "<i class='fas fa-minus'></i>" +
                            "</button>" +
                            "<input type='number' class='form-control quantity text-center mx-2' " +
                                "step='any' name='quantity[]' " +
                                "style='width: 70px;'>" +
                            "<button type='button' class='btn quantity-btn plus-btn'>" +
                                "<i class='fas fa-plus'></i>" +
                            "</button>" +
                        "</div>" +
                    "</td>" +
                    "<td><input type='number' class='form-control discount' name='discount[]'></td>" +
                    "<td class='total'>0</td>" +
                    "<td>" +
                    "<input type='hidden' name='product_id[]' value='" + product.id + "'>" +
                    "<input type='hidden' name='price[]' value='" + product.selling_price +
                    "'><input type='hidden' name='buying_price[]' value='" + product.buying_price +
                    "'><input type='hidden' name='remaining_quantity[]' value='" + product.quantity + "'>" +
                    "<button class='btn btn-danger remove-btn'>X</button>" +
                    "</td>" +
                    "</tr>";

                var $newRow = $(newRow);
                $productTable.prepend($newRow);
                updateSerialNumbers();
                $productSearch.val('');
                $productSuggestions.empty();
            }


            // Listen for changes in the transaction type
            $("input[name='transaction_type']").change(function() {
                transactionType = $(this).val();
            });

             // Quantity button click handler
                $productTable.on('click', '.quantity-btn', function() {
                    var $input = $(this).closest('.quantity-control').find('.quantity');
                    var currentVal = parseFloat($input.val()) || 0;
                    var step = parseFloat($input.attr('step')) || 1;

                    // Add temporary highlight effect
                    $(this).addClass('active');
                    setTimeout(() => {
                        $(this).removeClass('active');
                    }, 200);

                    if ($(this).hasClass('plus-btn')) {
                        // Always increase without checking max
                        $input.val(currentVal + step).trigger('input');
                    } else if ($(this).hasClass('minus-btn')) {
                        if (currentVal > 0) {
                            $input.val(Math.max(currentVal - step, 0)).trigger('input');
                        }
                    }
                });
            // Function to check if the entered quantity exceeds available stock
            function checkAvailableQuantity($row) {
    var enteredQuantity = parseFloat($row.find('.quantity').val()) || 0;
    var availableQuantity = parseFloat($row.find('input[name="remaining_quantity[]"]').val()) || 0;
    
    if (enteredQuantity > availableQuantity) {
        // Just show a warning without resetting the quantity
        toastr.warning('Entered quantity exceeds available stock. Available: ' + availableQuantity);
        // No longer resetting the value - allow the higher quantity
    }
}

// Event listener for quantity input fields
$productTable.on('input', '.quantity', function() {
    var $row = $(this).closest('tr');

    if (transactionType === "sales" || !transactionType) {
        checkAvailableQuantity($row); // Show warning but don't reset
    }

    var rowTotal = calculateRowTotal($row);
    $row.find('.total').text(rowTotal);
    updateTotalAmount();
});

            function updateSerialNumbers() {
                $productTable.find('.sn-column').each(function(index) {
                    $(this).text(index + 1);
                });
            }

            $productSearch.on('input', function() {
                var query = $(this).val();
                if (query.length >= 3) {

                    fetchProductSuggestions(query);
                } else {

                    $productSuggestions.empty();
                }
            });

            $productTable.on('click', '.remove-btn', function() {
                $(this).closest('tr').remove();
                updateSerialNumbers();
                updateTotalAmount();
            });


            $productTable.on('input', '.quantity, .discount', function() {
                var $row = $(this).closest('tr');
                var rowTotal = calculateRowTotal($row);
                $row.find('.total').text(rowTotal);
                updateTotalAmount();
            });

        });
    </script>

    <script>
        const toggleLaborSwitch = document.getElementById('toggleLabor');
        const laborCostField = document.getElementById('laborCostField');
        const laborCostInput = document.getElementById('laborCost');

        toggleLaborSwitch.addEventListener('change', function() {
            if (this.checked) {
                laborCostField.style.display = 'block';
                laborCostInput.setAttribute('required', 'required');
            } else {
                laborCostField.style.display = 'none';
                laborCostInput.removeAttribute('required');
            }
        });


        
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


  
        $('#whatsappModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var transactionNo = button.data('transaction-no'); // Extract info from data-* attributes
            var transactionType = button.data('transaction-type');

            // Store transaction details in modal's data attributes
            var modal = $(this);
            modal.find('#sendWhatsAppButton').data('transaction-no', transactionNo);
            modal.find('#sendWhatsAppButton').data('transaction-type', transactionType);
        });

        $('#sendWhatsAppButton').click(function() {
            var phone = $('#phoneNumber').val();
            var transactionNo = $(this).data('transaction-no');
            var transactionType = $(this).data('transaction-type');

            if (phone.length <= 11) {
                phone = '234' + phone;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('refresh-receipt') }}",
                data: {
                    'receipt_no': transactionNo,
                    'transaction_type': transactionType
                },
                success: function(res) {
                    let businessName = "EL-Habib Plumbing Materials and Services - {{ auth()->user()->branch->name }} Branch";
                    let businessDetails = '';
                    @if (auth()->user()->branch->name == 'Azare')
                        businessDetails = "Address: Along Ali Kwara Hospital, Azare.\nPhone: 0916-844-3058\nEmail: support@elhabibplumbing.com\nWebsite: www.elhabibplumbing.com";
                    @endif
                    @if (auth()->user()->branch->name == 'Misau')
                        businessDetails = "Address: Kofar Yamma, Misau, Bauchi State\nPhone: 0901-782-0678\nEmail: support@elhabibplumbing.com\nWebsite: www.elhabibplumbing.com";
                    @endif

                    let itemsList = res.items.map(item => `${item.product.name} (Qty: ${item.quantity}, Price: ${item.price.toLocaleString()})`).join('\n');
                    let totalAmount = res.items.reduce((sum, item) => sum + (item.quantity * item.price), 0).toLocaleString();

                    let message = `Hello,\n\nHere is your receipt from ${businessName}.\n\nTransaction Type: ${transactionType}\nDate: ${res.transaction_date}\n\nItems:\n${itemsList}\n\nTotal: ₦${totalAmount}\n\n${businessDetails}\n\nThank you for your business!`;
                    let encodedMessage = encodeURIComponent(message);
                    let whatsappURL = `https://api.whatsapp.com/send?phone=${phone}&text=${encodedMessage}`;

                    window.open(whatsappURL, '_blank');

                    $('#whatsappModal').modal('hide'); // Hide the modal after sending the message
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    if (xhr.status === 419) {
                        toastr.error("Session expired. Please login again.");
                        setTimeout(function() {
                            window.location.replace('{{ route('login') }}');
                        }, 2000);
                    }
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        let isSubmitting = false;
        let currentTransactionId = null;
        
        // Generate a unique transaction ID when form is loaded/reset
        function generateTransactionId() {
            return 'txn_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }
        
        // Initialize transaction ID
        currentTransactionId = generateTransactionId();
        
        $('#salesForm').submit(function(event) {
            event.preventDefault();
            
            // Prevent multiple submissions
            if (isSubmitting) {
                console.log('Form submission already in progress');
                return false;
            }
            
            var selectedPaymentMethod = $('input[name="payment_method"]:checked').val();
            
            if (selectedPaymentMethod === 'multiple') {
                var totalAmountDisplayed = parseFloat($('#totalAmount').text().replace('₦', '').replace(',', ''));
                var totalAmountEntered = parseFloat($('#totalAmountMultiplePayments').text().replace('₦', '').replace(',', ''));
                
                if (totalAmountEntered !== totalAmountDisplayed) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Total amount entered does not match the displayed total. Please check the amounts again.'
                    });
                    return;
                }
            }
            
            // Set submission flag and disable submit button
            isSubmitting = true;
            const submitButton = $('button[type="submit"]');
            const originalButtonText = submitButton.html();
            submitButton.prop('disabled', true).html('Processing...');
            
            var formData = $(this).serialize();
            
            // Add transaction ID to form data
            formData += '&transaction_id=' + encodeURIComponent(currentTransactionId);
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.LoadingOverlay("show");
            
            $.ajax({
                url: '{{ route('transactions.store') }}',
                type: 'POST',
                data: formData,
                timeout: 30000, // 30 second timeout
                success: function(response) {
                    if (response.status === 201) {
                        toastr.success(response.message, 'Success');
                        
                        // Update the recent transactions table with the returned HTML
                        if (response.recentTransactionsHtml) {
                            $('.recent-table').html(response.recentTransactionsHtml);
                        }
                        
                        // Reset form and generate new transaction ID
                        resetForm();
                        currentTransactionId = generateTransactionId();
                        
                    } else if (response.status === 400) {
                        toastr.error(response.message, 'Error');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    
                    if (xhr.responseJSON && xhr.responseJSON.status === 400 && xhr.responseJSON.message) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: xhr.responseJSON.message,
                        });
                    } else if (status === 'timeout') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Timeout Error',
                            text: 'The request took too long to complete. Please try again.',
                        });
                    } else {
                        toastr.error('An error occurred while processing the request.', 'Error');
                    }
                },
                complete: function() {
                    // Always reset submission state and re-enable button
                    isSubmitting = false;
                    submitButton.prop('disabled', false).html(originalButtonText);
                    $.LoadingOverlay("hide");
                }
            });
        });
        
        function resetForm() {
            $('#productTable').empty();
            $('#customer').val('0').change();
            $('#balanceContainer').hide();
            $('#totalAmount').text('₦0');
            $('input[name="payment_method"]').prop('checked', false);
            $("#salesForm")[0].reset();
            $("input[name='transaction_type']").prop("checked", false);
            $("#changeField").hide();
            $("#laborCostField").hide();
            $("#partialAmountField").hide();
            $("#paid_amount_span").text('Cash Amount Paid');
        }
        
        // Reset transaction ID when form is manually reset
        $('#salesForm').on('reset', function() {
            currentTransactionId = generateTransactionId();
            isSubmitting = false;
        });
        
        // Prevent browser back/forward button during submission
        $(window).on('beforeunload', function() {
            if (isSubmitting) {
                return 'A transaction is being processed. Are you sure you want to leave?';
            }
        });
        
        // Handle page visibility changes (user switches tabs during submission)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && isSubmitting) {
                console.warn('User switched away during transaction processing');
            }
        });
    });
    </script>
@endsection
