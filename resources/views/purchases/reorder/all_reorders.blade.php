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
                                        <tbody></tbody>
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
                                    <option value="{{ $supplier->id }}">{{ $supplier->first_name . ' ' . $supplier->last_name }}
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
                            row.append('<td> ₦' + total + '</td>');
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
                                '<li><a class="dropdown-item" href="{{ route('complete.reorder', ['reorder_no' => 'REORDER_NO']) }}">Complete</a></li>'
                                .replace('REORDER_NO', encodeURIComponent(
                                    reorderNo)));
                            dropdownMenu.append(
                                '<li><a class="dropdown-item delete-reorder" href="#" data-reorder-no="' +
                                reorderNo + '">Delete</a></li>');
                            dropdownMenu.append(
                                '<li><a class="dropdown-item change-supplier" href="#" data-supplier-id="' +
                                supplier + '" data-reorder-no="' + reorderNo +
                                '">Change Supplier</a></li>'
                            );


                            dropdownMenu.append(
                                '<li><a class="dropdown-item download-pdf" href="#" data-reorder-no="' +
                                reorderNo + '">Download as PDF</a></li>');

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


        });
    </script>



@endsection
