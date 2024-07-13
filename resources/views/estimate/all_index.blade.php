@extends('layouts.app')
@section('PageTitle', 'All Estimates')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <h2 class="text-bold fs-20">All Estimates</h2>
                        </div>
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <div class="form-group">
                                <input type="text" class="form-control" id="searchInput"
                                    placeholder="Search by Customer Name or Note">
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

                    </div>
                    <div class="card-body">
                        <div class="table-data">
                            @include('estimate.all_table')
                        </div>
                    </div>
                </div>

                <div class="modal fade addModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="">Mark As Sold </h4>
                                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"
                                    aria-hidden="true"></button>
                            </div>
                            <form action="{{ route('estimate.all.store') }}" method="POST">
                                @csrf
                                <div class="modal-body">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-1">
                                                <select class="form-select form-select-sm" id="payment_method"
                                                    name="payment_method" required>
                                                    <option value="">--Payment Method--</option>
                                                    <option value="cash">Cash</option>
                                                    <option value="transfer">Transfer</option>
                                                    <option value="pos">POS</option>
                                                    <option value="credit">Credit</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 customer_div d-none">
                                            <div class="mb-1">
                                                <select class="form-select form-select-sm" id="customer" name="customer"
                                                    required>
                                                    <option value="">-- Customer --</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ @$customer->id }}">{{ @$customer->first_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <div class="mb-1">
                                                Estimate ID:<span id="estimate_no_span"></span>
                                            </div>
                                            <div class="mb-1">
                                                Amount Payable:<span id="payable"></span>
                                            </div>
                                            <div class="mb-1">
                                                Customer Name:<span id="name"></span>
                                            </div>
                                            <div class="mb-1">
                                                Note:<span id="note"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="estimate_no" name="estimate_no">
                                    <input type="hidden" id="total_amount" name="total_amount">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary ml-2">Mark as Sold</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="modal">
                    <div id="print">
                        @include('estimate.receipt')
                    </div>
                </div>
                <style>
                    #productSuggestions ul {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                    }

                    .product-suggestion {
                        cursor: pointer;
                        padding: 8px;
                        border: 1px solid #ddd;
                        background-color: #f9f9f9;
                        transition: background-color 0.3s ease;
                    }

                    .product-suggestion:hover {
                        background-color: #e0e0e0;
                    }

                    .price-change {
                        background-color: #ffe6e6;
                        /* Light red background color */
                        color: #ff0000;
                        /* Red text color */
                    }
                </style>


                <div class="modal fade" id="editEstimateModal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Edit Estimate</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Discount</th>
                                            <th>Total Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="estimateTableBody">
                                        <!-- Rows will be dynamically added here -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="updateEstimateBtn">Update
                                    Estimate</button>
                                <div>Total Price: <span id="totalPrice">0</span></div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="sendWhatsAppModal" tabindex="-1" aria-labelledby="sendWhatsAppModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="sendWhatsAppModalLabel">Send Estimate via WhatsApp</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="sendWhatsAppForm">
                                    <div class="mb-3">
                                        <label for="phoneNumber" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phoneNumber" required>
                                    </div>
                                    <input type="hidden" id="modalEstimateNo">
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                

            </div>
        </div>
    </section><!-- #content end -->

@endsection

@section('js')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />


    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Popper.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"
        integrity="sha256-ugq4/dTj2B5O5p9l0IqgExPvPeMx7F/DvFAE7g8KuqI=" crossorigin="anonymous"></script>

    <!-- Bootstrap CSS and JS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>



<script>
    var branchDetails = {
        'Azare': {
            'address': 'Along Ali Kwara Hospital, Azare.',
            'phone': '0916-844-3058',
            'email': 'support@elhabibplumbing.com',
            'website': 'www.elhabibplumbing.com'
        },
        'Misau': {
            'address': 'Kofar Yamma, Misau, Bauchi State',
            'phone': '0901-782-0678',
            'email': 'support@elhabibplumbing.com',
            'website': 'www.elhabibplumbing.com'
        }
    };

    var currentBranch = "{{ auth()->user()->branch->name }}";
</script>


<script>
    $('#sendWhatsAppModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var estimateNo = button.data('estimate_no');
        var modal = $(this);
        modal.find('#modalEstimateNo').val(estimateNo);
    });

    $('#sendWhatsAppForm').submit(function(event) {
        event.preventDefault();
        var phoneNumber = $('#phoneNumber').val();
        var estimateNo = $('#modalEstimateNo').val();

        const data = {
            'estimate_no': estimateNo
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "{{ route('refresh-receipt-estimate') }}",
            data: data,
            success: function(res) {
                let message = "Estimate Details:\n";
                const currentBranch = "{{ auth()->user()->branch->name }}";
                const branchDetails = {
                    'Azare': {
                        'address': "Along Ali Kwara Hospital, Azare.",
                        'phone': "0916-844-3058",
                        'email': "support@elhabibplumbing.com",
                        'website': "www.elhabibplumbing.com"
                    },
                    'Misau': {
                        'address': "Kofar Yamma, Misau, Bauchi State",
                        'phone': "0901-782-0678",
                        'email': "support@elhabibplumbing.com",
                        'website': "www.elhabibplumbing.com"
                    }
                };

                message += "Business Name: EL-Habib Plumbing Materials and Services - " + currentBranch + " Branch\n";
                message += "Address: " + branchDetails[currentBranch]['address'] + "\n";
                message += "Phone: " + branchDetails[currentBranch]['phone'] + "\n";
                message += "Email: " + branchDetails[currentBranch]['email'] + "\n";
                message += "Website: " + branchDetails[currentBranch]['website'] + "\n\n";

                // Add account details
                const accountDetails = [
                    {
                        'account_number': '8255115541',
                        'account_name': 'Alhabib Plumbing Materials',
                        'bank_name': 'Moni Point'
                    },
                    {
                        'account_number': '8905855014',
                        'account_name': 'Elhabib Plumbing Materials and Services',
                        'bank_name': 'FCMB'
                    }
                ];

                accountDetails.forEach(function(account) {
                    message += "Account Number: " + account.account_number + "\n";
                    message += "Account Name: " + account.account_name + "\n";
                    message += "Bank Name: " + account.bank_name + "\n\n";
                });

                let total = 0;
                let serialNumber = 1;

                $.each(res.items, function(key, item) {
                    total += item.quantity * item.price;
                    message += serialNumber + ". " + item.product.name + ": " + item.quantity + " x " + item.price.toLocaleString() + " = " + (item.quantity * item.price).toLocaleString() + "\n";
                    serialNumber++;
                });

                if (res.items[0].labor_cost !== null) {
                    const laborCost = parseInt(res.items[0].labor_cost);
                    message += "Labor Cost: " + laborCost.toLocaleString() + "\n";
                    total += laborCost;
                }

                message += "Total: " + total.toLocaleString();

                const whatsappUrl = "https://api.whatsapp.com/send?phone=" + phoneNumber + "&text=" + encodeURIComponent(message);

                window.open(whatsappUrl, '_blank');

                $('#sendWhatsAppModal').modal('hide');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                if (xhr.status === 419) {
                    Command: toastr["error"]("Session expired. please login again.");
                    setTimeout(() => {
                        window.location.replace('{{ route('login') }}');
                    }, 2000);
                }
            }
        });
    });
</script>



    <script>
        $(document).on('click', '.editEstimate', function() {
            var estimateNo = $(this).data('estimate_no');
            var modal = $('#editEstimateModal');

            var encodedEstimateNo = encodeURIComponent(estimateNo);

            modal.modal('show');
            modal.find('.modal-body').html(
                '<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i></div>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('estimate.edit') }}',
                method: 'POST',
                data: {
                    encoded_estimate_no: encodedEstimateNo,
                },
                success: function(response) {
                    if (response.error) {
                        console.error(response.error);
                    } else {
                        estimates = response.estimates;
                        products = response.products;
                        priceChanges = response.price_changes;
                        oldPrices = response.old_prices;

                        var formHtml = '<form id="editEstimateForm">';
                        formHtml += '<div class="mb-3">';
                        formHtml += '<label for="productSearch">Search Products:</label>';
                        formHtml +=
                            '<input type="text" class="form-control" id="productSearch" placeholder="Enter product name">';
                        formHtml += '<div id="productSuggestions"></div>';
                        formHtml += '</div>';
                        formHtml += '<input type="hidden" name="estimate_no" value="' + estimates[0].estimate_no + '">';
                        formHtml += '<table class="table">';
                        formHtml +=
                            '<thead><tr><th>Product</th><th>Old Price</th><th>Price</th><th>Quantity</th><th>Discount</th><th>Total Price</th><th>Action</th></tr></thead>';
                        formHtml += '<tbody class="modaltbody">';

                        for (var i = 0; i < estimates.length; i++) {
                            var estimate = estimates[i];
                            var priceChangeClass = '';
                            var oldPriceHtml = '';

                            // Check if the product's price has changed
                            if (priceChanges[estimate.id]) {
                                priceChangeClass = 'price-change';
                                oldPriceHtml = '<td>' + oldPrices[estimate.id] + '</td>';
                                estimate.price = priceChanges[estimate.id]; // Update the price with the new selling price
                            } else {
                                oldPriceHtml = '<td></td>'; // Empty cell if no price change
                            }

                            formHtml += '<tr class="' + priceChangeClass + '">';
                            formHtml += '<td>' + estimate.product.name +
                                '<input type="hidden" name="product[]" value="' + estimate.product.id +
                                '"></td>';
                            formHtml += oldPriceHtml;
                            formHtml +=
                                '<td><input type="number" class="form-control price-field" name="price[]" value="' +
                                estimate.price + '" readonly></td>';
                            formHtml +=
                                '<td><input type="text" class="form-control" name="quantity[]" value="' +
                                estimate.quantity + '"></td>';
                            formHtml +=
                                '<td><input type="text" class="form-control" name="discount[]" value="' +
                                estimate.discount + '"></td>';
                            formHtml +=
                                '<td><input type="text" class="form-control total-price-field" name="total_price[]" value="' +
                                (estimate.price * estimate.quantity) + '" readonly></td>';
                            formHtml +=
                                '<td><button class="btn btn-danger removeEstimate" data-estimate_id="' +
                                estimate.id + '">X</button></td>';
                            formHtml += '</tr>';
                        }

                        formHtml += '<tr>';
                        formHtml += '<td colspan="4">Labor Cost: <input type="text" class="form-control" name="labor_cost" value="' + (estimates[0].labor_cost ? estimates[0].labor_cost : '') + '"></td>';
                        formHtml += '<td colspan="3">Note: <textarea class="form-control" name="note">' + (estimates[0].note ? estimates[0].note : '') + '</textarea></td>';
                        formHtml += '</tr>';

                        formHtml += '</tbody></table>';
                        formHtml += '</form>';

                        modal.find('.modal-body').html(formHtml)

                        var productSearchInput = $('#productSearch');
                        var productSuggestionsDiv = $('#productSuggestions');

                        productSearchInput.on('input', function() {
                            var searchTerm = $(this).val().trim();
                            productSuggestionsDiv.empty();

                            if (searchTerm.length > 0) {
                                $.ajax({
                                    url: '/get-product-suggestions',
                                    method: 'GET',
                                    data: {
                                        query: searchTerm,
                                    },
                                    success: function(response) {
                                        if (response.length > 0) {
                                            var suggestionList = '<ul>';
                                            for (var i = 0; i < response.length; i++) {
                                                suggestionList +=
                                                    '<li class="product-suggestion" data-product-id="' +
                                                    response[i].id + '">' +
                                                    response[i].name + '</li>';
                                            }
                                            suggestionList += '</ul>';
                                            productSuggestionsDiv.html(suggestionList);

                                            $('.product-suggestion').on('click',
                                                function() {
                                                    var productId = $(this).data('product-id');
                                                    addProductToTable(productId);
                                                    productSearchInput.val('');
                                                    productSuggestionsDiv.empty();
                                                });
                                        } else {
                                            productSuggestionsDiv.html('<p>No matching products found</p>');
                                        }
                                    },
                                    error: function(xhr) {
                                        console.error(xhr.responseText);
                                    }
                                });
                            }
                        });

                        $('.addRow').on('click', function() {
                            $('.removeRow').on('click', function() {
                                $(this).closest('tr').remove();
                                updateTotalPrice();
                            });

                            $('[name="quantity[]"], [name="discount[]"]').on('input',
                                function() {
                                    updateTotalPrice();
                                });
                        });

                        $('.removeEstimate').on('click', function() {
                            var estimateId = $(this).data('estimate_id');
                            $(this).closest('tr').remove();
                            updateTotalPrice();
                        });

                        $('[name="quantity[]"], [name="discount[]"]').on('input', function() {
                            updateTotalPrice();
                        });

                        $('#editEstimateForm').on('submit', function(event) {
                            event.preventDefault();
                            console.log(123);
                        });

                        updateTotalPrice();
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        function addProductToTable(productId) {
            var selectedProduct = products.find(product => product.id == productId);

            var newRow = '<tr>';
            newRow +=
                '<td>' + selectedProduct.name +
                '<input type="hidden" name="product[]" value="' + selectedProduct.id +
                '"><input type="hidden" name="estimate_no" value="' + estimates[0].estimate_no + '"></td>';
            newRow +=
                '<td><input type="number" class="form-control price-field" name="price[]" value="' + selectedProduct
                .selling_price + '" readonly></td>';
            newRow +=
                '<td><input type="text" class="form-control" name="quantity[]"></td>';
            newRow +=
                '<td><input type="text" class="form-control" name="discount[]"></td>';
            newRow +=
                '<td><input type="text" class="form-control total-price-field" name="total_price[]" readonly></td>';
            newRow +=
                '<td><button class="btn btn-danger removeRow">X</button></td>';
            newRow += '</tr>';

            $('.modaltbody').prepend(newRow);

            $('.removeRow').on('click', function() {
                $(this).closest('tr').remove();
                updateTotalPrice();
            });

            $('.product-select').on('change', function() {
                updatePriceField($(this));
            });

            $('[name="quantity[]"], [name="discount[]"]').on('input', function() {
                updateTotalPrice();
            });

            updateTotalPrice();
        }


        // Add the following functions for updating price fields and total price
        function updatePriceField(selectElement) {
            // Update the price field based on the selected product
            // ...
        }

        function updateTotalPrice() {
            // Update the total price based on quantity, discount, and price fields
            // ...
        }




        function updatePriceField(productSelect) {
            var selectedProduct = products.find(product => product.id == productSelect.val());
            if (selectedProduct) {
                productSelect.closest('tr').find('.price-field').val(selectedProduct.selling_price);
                updateTotalPrice();
            }
        }

        function updateTotalPrice() {
            var total = 0;
            $('.modaltbody tr').each(function() {
                var quantity = parseFloat($(this).find('[name="quantity[]"]').val()) || 0;
                var price = parseFloat($(this).find('.price-field').val()) || 0;
                var discount = parseFloat($(this).find('[name="discount[]"]').val()) || 0;
                var totalPerRow = (price * quantity) - discount;
                total += totalPerRow;
                $(this).find('.total-price-field').val(totalPerRow.toFixed(2));
            });
            $('#totalPrice').text(total.toFixed(2));
        }

        // Add logic to update estimate data when the "Update Estimate" button is clicked
        $(document).on('click', '#updateEstimateBtn', function() {
            var modal = $('#editEstimateModal');
            var formData = modal.find('form').serialize();

            // Use AJAX to send updated data to the server
            $.ajax({
                url: '/estimate/update',
                method: 'POST',
                data: formData,
                success: function(response) {
                    // Handle success response
                    modal.modal('hide');
                    $('.maintable').load(location.href + ' .maintable');
                    toastr.success('Estimates updated successfully');
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    // Handle error response
                    var errors = JSON.parse(xhr.responseText);

                    if (errors && errors.errors) {
                        var errorMessage = 'Error:';
                        $.each(errors.errors, function(key, value) {
                            errorMessage += '\n' + value;
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage,
                        });
                    } else {
                        toastr.error('An error occurred while updating estimates.');
                    }
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


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
                url: '{{ route('estimate.all.search') }}',
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
            $('#cashier_id').on('change', function() {

                var cashierId = $('#cashier_id').val();
                $.LoadingOverlay("show")
                $('.pagination').hide();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route('estimate.all.sort') }}',
                    method: 'POST',
                    data: {
                        cashier_id: cashierId,
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
        $(document).on('change', '#payment_method', function() {

            var payment_method = $('#payment_method').val();

            if (payment_method == 'credit') {
                $('.customer_div').removeClass('d-none');
                $('#customer').attr('required', true);
            } else {
                $('.customer_div').addClass('d-none');
                $('#customer').attr('required', false);
            }

        });
        $(document).on('click', '.saleItem', function(e) {
            e.preventDefault();

            $('#estimate_no').html();
            $('#payable').html();
            $('#customer').html();
            $('#note').html();

            let estimate_no = $(this).data('estimate_no');
            let payable = $(this).data('payable');
            let customer = $(this).data('customer');
            let note = $(this).data('note');
            let total_amount = $(this).data('payable');

            $('#estimate_no_span').html(estimate_no);
            $('#estimate_no').val(estimate_no);
            $('#total_amount').val(total_amount);
            $('#payable').html(payable);
            $('#customer').html(customer);
            $('#note').html(note);


        });
    </script>


<script>
    function PrintReceiptContent(estimate_no) {
        const data = {
            'estimate_no': estimate_no,
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "{{ route('refresh-receipt-estimate') }}",
            data: data,
            success: function(res) {
                let html = '';
                let total = 0;

                $.each(res.items, function(key, item) {
                    total += item.quantity * item.price;

                    html +=
                        '<tr style="text-align: center">' +
                        '<td style="text-align: left"><span style="font-size: 12px;">' + item.product.name + '</span></td>' +
                        '<td style="font-size: 12px;">' + item.quantity + '</td>' +
                        '<td style="font-size: 12px;">' + item.price.toLocaleString() + '</td>' +
                        '<td style="font-size: 12px;">' + (item.quantity * item.price).toLocaleString() + '</td>' +
                        '</tr>';
                });

                if (res.items[0].labor_cost !== null) {
                    const laborCost = parseInt(res.items[0].labor_cost);

                    html +=
                        '<tr style="text-align: center">' +
                        '<td></td>' +
                        '<td colspan="2"><b>Sub-total</b></td>' +
                        '<td><b>&#8358;' + total.toLocaleString() + '</b></td>' +
                        '</tr>';

                    html +=
                        '<tr style="text-align: center">' +
                        '<td></td>' +
                        '<td colspan="2"><b>Labor Cost</b></td>' +
                        '<td><b>&#8358;' + laborCost.toLocaleString() + '</b></td>' +
                        '</tr>';

                    total += laborCost;

                    html +=
                        '<tr style="text-align: center">' +
                        '<td></td>' +
                        '<td colspan="2"><b>Total</b></td>' +
                        '<td><b>&#8358;' + total.toLocaleString() + '</b></td>' +
                        '</tr>';

                    html +=
                        '<tr style="text-align: center">' +
                        '<td colspan="4"><i>Labor cost is separate, not related to the above company.</i></td>' +
                        '</tr>';
                } else {
                    html +=
                        '<tr style="text-align: center">' +
                        '<td></td>' +
                        '<td colspan="2"><b>Total Amount</b></td>' +
                        '<td><b>&#8358;' + total.toLocaleString() + '</b></td>' +
                        '</tr>';
                }

                $('#receipt_body').html(html);
                $('#transaction_date').html('Sale Date & Time: ' + res.transaction_date);

                let accountDetailsHtml = '';
                res.account_details.forEach(function(account) {
                    accountDetailsHtml +=
                        'Account Number: ' + account.account_number + '<br>' +
                        'Account Name: ' + account.account_name + '<br>' +
                        'Bank Name: ' + account.bank_name + '<br><br>';
                });

                $('#account_details').html(accountDetailsHtml);

                const data = document.getElementById('print').innerHTML;

                const myReceipt = window.open("", "myWin", "left=150, top=130, width=300, height=400");
                myReceipt.document.write(data);
                myReceipt.document.title = "Print Estimate Certificate";
                myReceipt.focus();
                myReceipt.print();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                if (xhr.status === 419) {
                    toastr["error"]("Session expired. please login again.");
                    setTimeout(() => {
                        window.location.replace('{{ route('login') }}');
                    }, 2000);
                }
            }
        });

        setTimeout(() => {
            // myReceipt.close();
        }, 8000);
    }
</script>




    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@endsection
