@extends('layouts.app')
@section('PageTitle', 'All Reorders')
@section('content')

    <!-- ============ Body content start ============= -->
    <section id="content">
        <div class="content-wraap mt-3">
            <div class="container clearfix">
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">

                        <div class="card mb-2">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="branch">Branch:</label>
                                        <select id="branch" class="form-select">
                                            <option value=""></option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="reorder-type">Reorder Type:</label>
                                        <select id="reorder-type" class="form-select">
                                            <option value=""></option>
                                            <option value="all">All</option>
                                            <option value="pending">Pending</option>
                                            <option value="fulfilled">Fulfilled</option>
                                            <option value="completed">Completed</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">
                                                    {{ $supplier->first_name . ' ' . $supplier->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body sales-table">
                                <div class="table-responsive container">
                                    <table class="table table-bordered" id="reorder-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="text-left">Date</th>
                                                <th>Supplier</th>
                                                <th>Reorder Total (₦)</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reorderGroups as $index => $reorderGroup)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $reorderGroup['date'] }}</td>
                                                    <td>{{ $reorderGroup['supplier'] }}</td>
                                                    <td>{{ number_format($reorderGroup['total'], 0) }}</td>
                                                    @php
                                                        $statusClass = '';
                                                        switch ($reorderGroup['status']) {
                                                            case 'pending':
                                                                $statusClass = 'bg-warning';
                                                                break;
                                                            case 'fulfilled':
                                                                $statusClass = 'bg-info';
                                                                break;
                                                            case 'completed':
                                                                $statusClass = 'bg-success';
                                                                break;
                                                            default:
                                                                $statusClass = 'bg-secondary';
                                                        }
                                                    @endphp
                                                    <td>
                                                        <span
                                                            class="badge {{ $statusClass }}">{{ $reorderGroup['status'] }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-secondary dropdown-toggle"
                                                                type="button" id="actionDropdown" data-bs-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                                Actions
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="actionDropdown">
                                                                <a class="dropdown-item view-details" href="#"
                                                                    data-reorder-no="{{ $reorderGroup['reorder_no'] }}">
                                                                    <i class="fa fa-info-circle"></i> Details
                                                                </a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('complete.reorder', ['reorder_no' => $reorderGroup['reorder_no']]) }}">
                                                                    <i class="fa fa-check-circle"></i> Complete
                                                                </a>
                                                                <a class="dropdown-item add-expenses" href="#"
                                                                    data-reorder-no="{{ $reorderGroup['reorder_no'] }}">
                                                                    <i class="fa fa-plus-circle"></i> Add Expenses
                                                                </a>
                                                                <a class="dropdown-item profitability-forecast"
                                                                    href="#"
                                                                    data-reorder-no="{{ $reorderGroup['reorder_no'] }}">
                                                                    <i class="fa fa-line-chart"></i> Profitability Forecast
                                                                </a>
                                                                <a class="dropdown-item change-supplier" href="#"
                                                                    data-supplier-id="{{ $supplier }}"
                                                                    data-reorder-no="{{ $reorderGroup['reorder_no'] }}">
                                                                    <i class="fa fa-exchange"></i> Change Supplier
                                                                </a>
                                                                <a class="dropdown-item download-pdf" href="#"
                                                                    data-reorder-no="{{ $reorderGroup['reorder_no'] }}">
                                                                    <i class="fa fa-file"></i> Download as PDF
                                                                </a>
                                                                <a class="dropdown-item delete-reorder" href="#"
                                                                    data-reorder-no="{{ $reorderGroup['reorder_no'] }}">
                                                                    <i class="fa fa-trash"></i> Delete
                                                                </a>
                                                            </div>

                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- Modal -->
    <div class="modal fade" id="changeSupplierModal" tabindex="-1" role="dialog"
        aria-labelledby="changeSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeSupplierModalLabel">Change Supplier</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changeSupplierForm">
                        <div class="form-group">
                            <label for="supplierSelect">Select Supplier</label>
                            <select class="form-select" id="supplierSelect" name="supplier_id">
                                <option value=""></option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">
                                        {{ $supplier->first_name . ' ' . $supplier->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" id="reorderNoInput" name="reorder_no" value="">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateSupplierBtn">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Reorder Details</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div id="spinner" class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div id="modalData" style="display: none;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Ordered Quantity</th>
                                    <th>Buying Price</th>
                                    <th>Sub Total</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExpenseModalLabel">Add Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="expenseForm">
                    <div class="modal-body">
                        <input type="hidden" id="reorderNumber" name="reorderNumber" value="">

                        <div id="expenseRowsContainer">
                            <!-- Initial expense row -->
                            <div class="row mb-3 expenseRow">
                                <div class="col-sm-4">
                                    <select class="form-select" name="category[]" placeholder="Category" required>
                                        <option value=""></option>
                                        <option value="shipping_and_freight">Shipping and Freight</option>
                                        <option value="handling">Handling</option>
                                        <option value="packaging_and_labelling">Packaging and Labelling</option>
                                        <option value="taxes">Taxes</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <input type="number" class="form-control" name="amount[]" placeholder="Amount"
                                        step="0.01" required>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="description[]"
                                        placeholder="Description">
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-danger removeExpenseRow">X</button>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-12 text-center">
                                <button type="button" class="btn btn-success" id="addExpenseRow">+ Row</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Profitabilty Forecast Modal -->
    <div class="modal fade" id="profitabilityForecastModal" tabindex="-1" role="dialog"
        aria-labelledby="profitabilityForecastModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profitabilityForecastModalLabel">Profitability Forecast</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin"></i>
                        <p>Loading...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('js')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $(document).ready(function() {

            $('#reorder-type').change(function() {
                var branchId = $('#branch').val();
                var reorderType = $('#reorder-type').val();
                $.LoadingOverlay("show");
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                // Send AJAX request
                $.ajax({
                    url: '{{ route('reorders.fetch') }}',
                    method: 'POST',
                    data: {
                        branch_id: branchId,
                        reorder_type: reorderType
                    },
                    success: function(response) {
                        // Handle the success response
                        var tableBody = $('#reorder-table tbody');
                        tableBody.empty();
                        $.LoadingOverlay("hide");
                        if (response.length === 0) {
                            toastr.info('No Reorders matched.');
                            return;
                        }
                        $.each(response, function(index, reorderGroup) {
                            var reorderNo = reorderGroup.reorder_no;
                            var date = reorderGroup.date;
                            var supplier = reorderGroup.supplier;
                            var status = reorderGroup.status;
                            var total = reorderGroup.total;

                            var statusClass = '';

                            // Assign appropriate class based on status
                            switch (status) {
                                case 'pending':
                                    statusClass = 'bg-warning';
                                    break;
                                case 'fulfilled':
                                    statusClass = 'bg-info';
                                    break;
                                case 'completed':
                                    statusClass = 'bg-success';
                                    break;
                                default:
                                    statusClass = 'bg-secondary';
                            }

                            var row = $('<tr>');
                            row.append('<td>' + (index + 1) + '</td>');
                            row.append('<td class="text-left">' + date + '</td>');
                            row.append('<td>' + supplier + '</td>');
                            row.append('<td> ₦' + total.toLocaleString() + '</td>');
                            row.append('<td><span class="badge ' + statusClass + '">' +
                                status + '</span></td>');
                            var actionDropdown = $('<div class="dropdown"></div>');
                            var dropdownToggle = $(
                                '<button class="btn btn-secondary dropdown-toggle" type="button" id="actionDropdown' +
                                index +
                                '" data-bs-toggle="dropdown" aria-expanded="false">Action</button>'
                            );
                            var dropdownMenu = $(
                                '<ul class="dropdown-menu" aria-labelledby="actionDropdown' +
                                index + '"></ul>');

                            // Add dropdown items
                            dropdownMenu.append(
                                '<li><a class="dropdown-item view-details" href="#" data-reorder-no="' +
                                reorderNo +
                                '"><i class="fa fa-info-circle"></i> Details</a></li>'
                            );
                            dropdownMenu.append(
                                '<li><a class="dropdown-item" href="{{ route('complete.reorder', ['reorder_no' => 'REORDER_NO']) }}"><i class="fa fa-check-circle"></i> Complete</a></li>'
                                .replace('REORDER_NO', encodeURIComponent(
                                    reorderNo)));
                            dropdownMenu.append(
                                '<li><a class="dropdown-item add-expenses" href="#" data-reorder-no="' +
                                reorderNo +
                                '"><i class="fa fa-plus-circle"></i> Add Expenses</a></li>'
                            );
                            dropdownMenu.append(
                                '<li><a class="dropdown-item  profitability-forecast" href="#" data-reorder-no="' +
                                reorderNo +
                                '"><i class="fa fa-line-chart"></i> Profitability Forecast</a></li>'
                            );
                            dropdownMenu.append(
                                '<li><a class="dropdown-item change-supplier" href="#" data-supplier-id="' +
                                supplier + '" data-reorder-no="' + reorderNo +
                                '"><i class="fa fa-exchange"></i> Change Supplier</a></li>'
                            );

                            dropdownMenu.append(
                                '<li><a class="dropdown-item download-pdf" href="#" data-reorder-no="' +
                                reorderNo +
                                '"><i class="fa fa-file"></i> Download as PDF</a></li>'
                                );
                            dropdownMenu.append(
                                '<li><a class="dropdown-item delete-reorder" href="#" data-reorder-no="' +
                                reorderNo +
                                '"><i class="fa fa-trash"></i> Delete</a></li>');

                            function setReorderNumber(reorderNo) {
                                $('#reorderNumber').val(reorderNo);
                                alert(reoderNo)
                            }

                            // Append elements
                            actionDropdown.append(dropdownToggle);
                            actionDropdown.append(dropdownMenu);

                            // Append to the row
                            row.append($('<td></td>').append(actionDropdown));

                            tableBody.append(row);

                        });
                    },


                    error: function(xhr, status, error) {
                        // Handle the error response
                        console.log(error);
                    }
                });
            });

            $(document).on('click', '.download-pdf', function(e) {
                e.preventDefault();

                var reorderNo = $(this).data('reorder-no');

                // Make AJAX request to fetch data and download PDF
                $.ajax({
                    url: '{{ route('reorder.download-pdf') }}',
                    method: 'POST',
                    data: {
                        reorder_no: reorderNo
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        var blob = new Blob([response]);
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'reorder_' + reorderNo + '.pdf';
                        link.click();
                    },
                    error: function(xhr, status, error) {
                        // Handle error if any
                    }
                });
            });

            $(document).on('click', '.delete-reorder', function(e) {
                e.preventDefault();
                var reorderNo = $(this).data('reorder-no');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this reorder!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: '{{ route('reorder.delete') }}',
                            method: 'POST',
                            data: {
                                reorder_no: reorderNo
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message,
                                    icon: 'success'
                                });
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'An error occurred while deleting the reorder.',
                                    icon: 'error'
                                });
                            }
                        });
                    } else {
                        // User canceled deletion
                    }
                });
            });


            $(document).on('click', '.change-supplier', function(e) {
                e.preventDefault();

                var supplierId = $(this).data('supplier-id');
                var reorderNo = $(this).data('reorder-no');

                // Set the reorder_no value in the hidden input field
                $('#reorderNoInput').val(reorderNo);

                // Set the selected supplier in the dropdown
                $('#supplierSelect').val(supplierId);

                // Open the modal
                $('#changeSupplierModal').modal('show');
            });

            $(document).on('click', '#updateSupplierBtn', function(e) {
                e.preventDefault();

                var reorderNo = $('#reorderNoInput').val();
                var supplierId = $('#supplierSelect').val();

                $.ajax({
                    url: '{{ route('reorder.update-supplier') }}',
                    method: 'POST',
                    data: {
                        reorder_no: reorderNo,
                        supplier_id: supplierId
                    },
                    success: function(response) {
                        // Handle success response
                        Swal.fire({
                            title: 'Success',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Close the modal
                                $('#changeSupplierModal').modal('hide');
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        // Handle error if any
                        Swal.fire({
                            title: 'Error',
                            text: xhr.responseText,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });


            $(document).on('click', '.view-details', function(e) {
                e.preventDefault();

                var reorderNo = $(this).data('reorder-no');

                $('#detailsModal').modal('show');

                $('#spinner').show();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route('reorder.details') }}',
                    type: 'POST',
                    data: {
                        reorder_no: reorderNo
                    },
                    success: function(response) {
                        $('#modalData tbody').empty();

                        var totalBuyingPrice = 0;

                        $.each(response, function(index, dataItem) {
                            var quantity = dataItem.quantity.toLocaleString();
                            var buyingPrice = dataItem.buying_price.toLocaleString();
                            var subtotal = (dataItem.quantity * dataItem.buying_price)
                                .toLocaleString();

                            $('#modalData tbody').append('<tr>' +
                                '<td>' + dataItem.product.name + '</td>' +
                                '<td>' + quantity + '</td>' +
                                '<td>' + buyingPrice + '</td>' +
                                '<td>' + subtotal + '</td>' +
                                '</tr>');

                            totalBuyingPrice += dataItem.quantity * dataItem
                                .buying_price;
                        });

                        var formattedTotal = '₦' + totalBuyingPrice.toLocaleString();

                        $('#modalData tbody').append('<tr>' +
                            '<td colspan="3"><strong>Total Buying Price</strong></td>' +
                            '<td><strong>' + formattedTotal + '</strong></td>' +
                            '</tr>');

                        $('#spinner').hide();
                        $('#modalData').show();
                    },

                    error: function() {

                    }
                });
            });


            // add expense row
            $('#addExpenseRow').on('click', function() {
                var expenseRow = $('.expenseRow').first().clone();
                expenseRow.find('input').val('');
                expenseRow.find('select').val('');
                $('#expenseRowsContainer').append(expenseRow);
            });

            // Remove expense row
            $(document).on('click', '.removeExpenseRow', function() {
                var expenseRow = $(this).closest('.expenseRow');
                if ($('.expenseRow').length > 1) {
                    expenseRow.remove();
                }
            });

            // Show modal and retrieve reorder number
            $(document).on('click', '.add-expenses', function(e) {
                e.preventDefault();

                var reorderNo = $(this).data('reorder-no');

                $('#reorderNumber').val(reorderNo);

                $('#addExpenseModal').modal('show');
            });
            // submit expense form
            $('#expenseForm').on('submit', function(e) {
                e.preventDefault();

                var reorderNo = $('#reorderNumberInput').val();

                var formData = $(this).serialize();

                var submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true);
                submitButton.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route('reorder.save-expenses') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        toastr.success('Expenses saved successfully.');

                        $('#addExpenseModal').modal('hide');

                        submitButton.prop('disabled', false);
                        submitButton.html('Save');
                        $('#expenseForm')[0].reset();

                    },
                    error: function(xhr, status, error) {
                        toastr.error('Error: ' + error);

                        submitButton.prop('disabled', false);
                        submitButton.html('Save');
                    }
                });
            });


            // Open Profitability Forecast modal
            $(document).on('click', '.profitability-forecast', function(e) {
                e.preventDefault();

                var reorderNo = $(this).data('reorder-no');

                $('#forecastReorderNo').val(reorderNo);

                $('#profitabilityForecastModal').modal('show');

                fetchProfitabilityForecast(reorderNo);
            });


            // Fetch Profitability Forecast data
            function fetchProfitabilityForecast(reorderNo) {
                // Show loading spinner
                $('#profitabilityForecastModal .modal-body').html(
                    '<div class="text-center"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div>');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route('reorder.fetch-profitability-forecast') }}',
                    type: 'POST',
                    data: {
                        reorderNo: reorderNo
                    },
                    success: function(response) {
                        // Calculate profitability
                        var inventoryCost = response.inventoryCost;
                        var expenses = response.reorderExpenses;
                        var profit = inventoryCost - expenses;

                        // Generate HTML content for expenses table
                        var html = '<table class="table">';
                        html += '<thead><tr><th>#</th><th>Category</th><th>Amount</th></tr></thead>';
                        html += '<tbody>';

                        // Check if expenses is defined and not empty
                        if (expenses && expenses.length) {
                            $.each(expenses, function(index, expense) {
                                var category = convertCategory(expense.category);
                                html += '<tr>';
                                html += '<td>' + (index + 1) + '</td>';
                                html += '<td>' + category + '</td>';
                                html += '<td>' + expense.amount + '</td>';
                                html += '</tr>';
                            });

                            // Calculate total expenses
                            var totalExpenses = expenses.reduce(function(total, expense) {
                                var amount = parseFloat(expense.amount);
                                return total + amount;
                            }, 0);



                            // Add total row for expenses
                            html += '<tr>';
                            html += '<td><strong>Total Expenses</strong></td>';
                            html += '<td></td>';
                            html += '<td><strong>' + totalExpenses.toLocaleString() + '</strong></td>';
                            html += '</tr>';
                        } else {
                            // Display message for no expenses
                            html += '<tr>';
                            html += '<td colspan="3">No expenses found.</td>';
                            html += '</tr>';
                        }

                        html += '</tbody>';
                        html += '</table>';

                        // Generate HTML content for reorder items table
                        html += '<table class="table">';
                        html +=
                            '<thead><tr><th>Item Name</th><th>Quantity</th><th>Margin</th><th>Total</th></tr></thead>';
                        html += '<tbody>';

                        // Check if reorderItems is defined and not empty
                        if (response.reorderItems && response.reorderItems.length) {
                            var totalReorderItems = 0;

                            $.each(response.reorderItems, function(index, item) {
                                var margin = item.product.selling_price - item.buying_price;
                                var total = item.quantity * margin;
                                totalReorderItems += total;
                                html += '<tr>';
                                html += '<td>' + item.product.name + '</td>';
                                html += '<td>' + item.quantity.toLocaleString() + '</td>';
                                html += '<td>' + margin.toLocaleString() + '</td>';
                                html += '<td>' + total.toLocaleString() + '</td>';
                                html += '</tr>';
                            });

                            // Add total row for reorder items
                            html += '<tr>';
                            html += '<td><strong>Total Reorder Profit</strong></td>';
                            html += '<td></td>';
                            html += '<td></td>';
                            html += '<td><strong>' + totalReorderItems.toLocaleString() +
                                '</strong></td>';
                            html += '</tr>';
                        } else {
                            // Display message for no reorder items
                            html += '<tr>';
                            html += '<td colspan="4">No reorder items found.</td>';
                            html += '</tr>';
                        }

                        html += '</tbody>';
                        html += '</table>';

                        // Append total profit information
                        html += '<div>';
                        html += '<h5>Inventory Cost: ' + response.totalInventoryCost.toLocaleString() +
                            '</h5>';
                        html += '<h5>Gross Reorder Profit: ' + totalReorderItems.toLocaleString() +
                            '</h5>';
                        html += '<h5>Total Expenses: ' + totalExpenses.toLocaleString() + '</h5>';
                        html += '<h5>Expected Profit: ' + (totalReorderItems - totalExpenses)
                            .toLocaleString() + '</h5>';
                        html += '</div>';

                        // Display data in modal body
                        $('#profitabilityForecastModal .modal-body').html(html);
                    },
                    error: function(xhr, status, error) {
                        // Display error message
                        $('#profitabilityForecastModal .modal-body').html(
                            '<div class="text-center"><p>Error: ' + error + '</p></div>');
                    }
                });
            }

            function convertCategory(category) {
                var words = category.split('_');
                for (var i = 0; i < words.length; i++) {
                    words[i] = words[i][0].toUpperCase() + words[i].substr(1);
                }
                return words.join(' ');
            }



        });
    </script>




@endsection
