@extends('layouts.app')
@section('PageTitle', 'Users')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <div class="card">

                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <h3 class="text-bgold fs-20">Cash Credit ({{ auth()->user()->branch->name }} Branch)</h3>
                        </div>
                       

                        <div class="col-12 col-md-2 mb-md-0">
                            <button class="btn btn-lsm btn-primary text-white" data-bs-toggle="modal"
                                data-bs-target=".addModal">+
                                New Cash Credit</button>
                        </div>
                    </div>

                    <div class="card-body">

                        @include('cash_credits.table')

                    </div>

                </div>
            </div>
        </div>
    </section><!-- #content end -->

    <!-- Large Modal -->
    <div class="modal fade addModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Add New Cash Credit</h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('cash_credits.index') }}" method="POST">
                    @csrf
                    <div class="modal-body">


                        <div class="form-group">
                            <label for="first_name" class="col-form-label">Customer:</label>
                            <select class="form-select" name="customer_id" required>
                                <option value=""></option>
                                @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->first_name.' '. $customer->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-form-label">Amount:</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ml-2">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="modal fade" id="creditsHistoryModal" tabindex="-1" aria-labelledby="creditsHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="creditsHistoryModalLabel">Credits History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="creditsHistoryContent">
                        <!-- Credits history content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>



<div class="modal fade" id="creditPaymentModal" tabindex="-1" aria-labelledby="creditPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditPaymentModalLabel">Credit Payment</h5>
                <h6 class="modal-title" id="creditBalanceHeader">Credit Balance: <span id="modalCreditBalance"></span></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center" id="loadingSpinner">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="creditPaymentContent" style="display: none;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Amount</th>
                                <th>Amount Paid</th>
                                <th>Payment Type</th>
                                <th>Partial Amount</th>
                            </tr>
                        </thead>
                        <tbody id="creditPaymentTableBody">
                            <!-- Table rows will be dynamically added here -->
                        </tbody>
                    </table>
                    <div class="form-group">
                        <label for="paymentMethod">Payment Method</label>
                        <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                            <option value=""></option>
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                            <option value="pos">POS</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="processCreditPaymentBtn">Process Payment</button>
            </div>
        </div>
    </div>
</div>

    
    
    

@endsection

@section('js')



<script>
    $(document).ready(function () {
       
        $('#processCreditPaymentBtn').on('click', function() {
                var paymentData = [];

                $('input[type="radio"]:checked').each(function() {
                    var paymentType = $(this).val();
                    var creditId = $(this).attr('name').split('_')[1];
                    var partialAmountInput = $('input[name="partial_amount_' + creditId + '"]');
                    var partialAmount = partialAmountInput.val();

                    paymentData.push({
                        creditId: creditId,
                        paymentType: paymentType,
                        partialAmount: partialAmount
                    });
                });

                // Check if payment method is selected
                var paymentMethod = $('#paymentMethod').val();
                if (!paymentMethod) {
                    toastr.error("Please select a payment method.");
                    return;
                }

                // Disable the button and show loading spinner
                var processBtn = $(this);
                processBtn.prop('disabled', true);
                processBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Send payment data to the server
                $.ajax({
                    url: '/process-credit-payment',
                    method: 'POST',
                    data: { paymentData: paymentData, paymentMethod: paymentMethod },
                    success: function (response) {
                        if (response.message) {
                            toastr.success(response.message);
                            $('.creditTable').load(location.href+' .creditTable');
                            // Hide the modal upon success
                            $('#creditPaymentModal').modal('hide');
                        }
                    },
                    error: function (error) {
                        toastr.error("An error occurred while processing the payment.");
                    },
                    complete: function() {
                        // Re-enable the button and revert its text
                        processBtn.prop('disabled', false);
                        processBtn.html('Process Payment');
                    }
                });
            });

    });
</script>




<script>
    $(document).ready(function () {
        $('.credits-payment').on('click', function(event) {
            event.preventDefault();

            var modal = $('#creditPaymentModal');
            var customerId = $(this).data('customer-id');

            modal.find('#loadingSpinner').show();
            modal.find('#creditPaymentContent').hide();

            $.ajax({
                url: '/fetch-credit-records/' + customerId,
                method: 'GET',
                success: function (response) {
                    modal.find('#creditPaymentTableBody').empty();
                    var totalCreditBalance = 0;

                    $.each(response.creditRecords, function (index, credit) {
                        
                        totalCreditBalance += credit.amount - credit.amount_paid;

                        var row = '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + credit.amount + '</td>' +
                            '<td>' + credit.amount_paid + '</td>' +
                            '<td>' +
                            '<label><input type="radio" name="payment_' + credit.id + '" value="no_payment" checked> No Payment</label><br>' +
                            '<label><input type="radio" name="payment_' + credit.id + '" value="full_payment"> Full Payment</label><br>' +
                            '<label><input type="radio" name="payment_' + credit.id + '" value="partial_payment"> Partial Payment</label>' +
                            '</td>' +
                            '<td><input type="number" name="partial_amount_' + credit.id + '" style="display: none;" placeholder="Partial Amount"></td>' +
                            '</tr>';

                        modal.find('#creditPaymentTableBody').append(row);
                    });

                    modal.find('#modalCreditBalance').text(totalCreditBalance);

                    modal.find('#loadingSpinner').hide();
                    modal.find('#creditPaymentContent').show();
                },
                error: function (error) {
                    // Handle error response
                }
            });
        });

        // Show/hide partial amount input field based on radio button selection
        $(document).on('change', 'input[name^="payment_"]', function () {
            var partialInput = $(this).closest('tr').find('input[name^="partial_amount_"]');
            if ($(this).val() === 'partial_payment') {
                partialInput.show();
            } else {
                partialInput.hide();
            }
        });

        $(document).on('input', 'input[name^="partial_amount_"]', function () {
            var partialInput = $(this);
            var row = partialInput.closest('tr');
            var amountPaid = parseFloat(row.find('td:eq(2)').text()); 
            var amountCollected = parseFloat(row.find('td:eq(1)').text()); 
            var remainingBalance = amountCollected - amountPaid;

            var enteredAmount = parseFloat(partialInput.val());
            if (enteredAmount > remainingBalance) {
                partialInput.val('');
                toastr.warning("Entered Amount Cannot Exceed Remaining Balance");
            }
        });
    });
</script>



<script>
    $(document).ready(function() {
        $('.credits-history').on('click', function(event) {
            event.preventDefault();

            var customerId = $(this).data('customer-id');
            var modal = $('#creditsHistoryModal');
            var content = $('#creditsHistoryContent');

            // Show loading spinner while fetching data
            content.html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');

            // Fetch and display credits history via AJAX
            $.ajax({
                url: '/credits-history/' + customerId,
                type: 'GET',
                success: function(data) {
                    content.html(data);
                },
                error: function() {
                    content.html('<div class="alert alert-danger">Failed to fetch credits history.</div>');
                }
            });

            // Show the modal
            modal.modal('show');
        });
    });
</script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>


@endsection
