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

    <style>
        /* Make the payment modal smaller */
        #paymentModal .modal-dialog {
            max-width: 300px; /* Adjust the width as needed */
        }
       
    #paymentModal .modal-content {
        border: 2px solid #007bff; /* Use your desired outline color */
        background-color: #f8f9fa; /* Use your desired background color */
    }

    </style>
    

<!-- Update the payment modal HTML -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment Options</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Choose payment type:</p>
                <button class="btn btn-primary" id="partialPaymentBtn">Partial Payment</button>
                <button class="btn btn-success" id="completePaymentBtn">Complete Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Update the partial payment modal HTML -->
<div class="modal fade" id="partialPaymentModal" tabindex="-1" aria-labelledby="partialPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="partialPaymentModalLabel">Partial Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Enter the partial payment amount:</p>
                <input type="number" id="partialPaymentAmount" class="form-control" placeholder="Amount">
                <button class="btn btn-primary mt-3" id="confirmPartialPaymentBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

    
    
    

@endsection

@section('js')


<script>
    $(document).ready(function () {
        var selectedCreditId;
        var selectedCreditAmount;

        $('.settle-btn').on('click', function () {
            selectedCreditId = $(this).data('credit-id');
            selectedCreditAmount = $(this).data('credit-amount');
            var clickedButton = $(this);
            var paymentModal = $('#paymentModal');
            
            // Calculate modal position relative to the clicked button
            var modalOffset = clickedButton.offset();
            modalOffset.top += clickedButton.outerHeight();

            // Set modal position and show
            paymentModal.css({top: modalOffset.top, left: modalOffset.left});
            paymentModal.modal('show');
        });

        // When the "Complete Payment" button is clicked
        $('#completePaymentBtn').on('click', function () {
            sendPaymentInformation(selectedCreditId, 'complete');
        });

        $('#partialPaymentBtn').on('click', function () {
            var partialPaymentModal = $('#partialPaymentModal');
            partialPaymentModal.find('#partialPaymentAmount').val(selectedCreditAmount);
            partialPaymentModal.modal('show');
        });

        
    });

    function sendPaymentInformation(creditId, paymentType) {
        $.ajax({
            url: '/process-payment',
            type: 'POST',
            data: {
                creditId: creditId,
                paymentType: paymentType
            },
            success: function (response) {
                // Handle success response
            },
            error: function (error) {
                // Handle error response
            }
        });
    }
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
                url: '{{ route('users.search') }}',
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


    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>

    <script>
        $(document).on('click', '.deleteItem', function(e) {
            e.preventDefault();

            let id = $(this).data('id');
            let name = $(this).data('name');

            swal({
                    title: "Delete " + name + "?",
                    text: "Once deleted, all Payments by the user will also be deleted and you will no able to restore it!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {

                        var data = {
                            'id': id,
                        }

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "POST",
                            url: "{{ route('customers.delete') }}",
                            data: data,
                            dataType: "json",
                            success: function(res) {

                                if (res.status == 200) {
                                    Command: toastr["success"](
                                        "User deleted Successfully."
                                    );
                                   
                                    window.location.replace('{{ route('customers.index') }}');

                                }
                                else {

                                    Command: toastr["error"](
                                        "Error Occured"
                                    );
                                   
                                }


                            }
                        });

                    }
                });
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
