@extends('layouts.app')
@section('PageTitle', 'Return a Credit Sales')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col-4 "><span class="text-bold fs-16">Return a Credit Sale ({{ auth()->user()->branch->name }})</span></div>
                    <div class="col-md-2 float-right">
                        <a href="javascript:void(0)" onclick="history.back();" class="btn btn-sm btn-primary me-2"><-- Go back to User Profile</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="returnForm" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">S/N</th>
                                        <th scope="col">Item</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Discount</th>
                                        <th scope="col">Purchased Qty</th>
                                        <th scope="col">Previous</th>
                                        <th scope="col">Returned Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $key => $sale)
                                    <tr>
                                        <th>{{ $key+1 }}</th>
                                        <td>
                                            <select class="form-select form-select-sm" name="product_id[]" readonly>
                                                <option value="{{ $sale->product->id }}">{{ $sale->product->name }}</option>
                                            </select>
                                        </td>
                                        <input type="hidden" value="{{ $sale->id }}" name="sale_id[]">
                                        <td><input type="number" class="form-control form-control-sm" value="{{ $sale->price }}" name="price[]" readonly></td>
                                        <td><input type="number" class="form-control form-control-sm" value="{{ $sale->discount }}" name="discount[]" readonly></td>
                                        <td><input type="number" class="form-control form-control-sm" value="{{ $sale->quantity }}" name="quantity[]" readonly></td>
                                        <td><input type="number" class="form-control form-control-sm" value="{{ $sale->returned_qty }}" name="pre_returned_qty[]" readonly></td>
                                        <td><input type="number" class="form-control form-control-sm" name="returned_qty[]" {{ $sale->quantity < 1 ? 'readonly': '' }}></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <input type="hidden" name="customer_id" value="{{ $sales[0]->customer }}"/>
                        </div>
                        <button id="submitButton" type="submit" class="btn btn-secondary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Handling quantity validation
        $('input[name="returned_qty[]"]').keyup(function() {
            var index = $(this).closest('tr').index(); 
            var quantity = parseInt($('input[name="quantity[]"]').eq(index).val());
            var preReturnedQty = parseInt($('input[name="pre_returned_qty[]"]').eq(index).val());
            var returnedQty = parseInt($(this).val());
            if (returnedQty + preReturnedQty > quantity) {
                toastr.error('The amount you entered exceeds the quantity bought.');
                $(this).val('');
            }
        });

        // Handling form submission via AJAX
        $('#returnForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            var form = $(this);
            var formData = form.serialize(); // Serialize the form data

            // Disable the submit button and show a loading spinner
            var submitButton = $('#submitButton');
            submitButton.prop('disabled', true);
            submitButton.html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

            // Perform AJAX form submission
            $.ajax({
                url: form.attr('action'), // The form action URL
                method: 'POST',
                data: formData,
                success: function(response) {
                    // Show success message using SweetAlert
                    Swal.fire({
                        title: 'Success!',
                        text: 'Credit Sales was updated successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    setTimeout(function() {
                        window.location.href = "{{ route('customers.profile', $sales[0]->customer) }}";
                    }, 500); // Delay before redirect
                },
                error: function(xhr, status, error) {
                    // Handle error
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        // Re-enable the submit button
                        submitButton.prop('disabled', false);
                        submitButton.html('Submit');
                    });
                }
            });
        });
    });
</script>
@endsection
