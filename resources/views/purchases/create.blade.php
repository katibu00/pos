@extends('layouts.app')
@section('PageTitle', 'Add New Purchases')
@section('content')
    <!-- ============ Body content start ============= -->
    <section id="content">
        <div class="content-wraap mt-3">
            <div class="container clearfix">
                <form id="salesForm">
                    <div class="row mb-4">
                        <div class="col-md-12 mb-4">
                            <div class="card mb-2">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="col-5"><span class="text-bold fs-16">Add New Purchases ({{ auth()->user()->branch->name }} Branch)</span></div>
                                    <div class="col-sm-4 col-md-2"><a class="btn btn-sm btn-secondary me-2" href="{{ route('purchase.index') }}"> <--- Back to list</a></div>
                                </div>
                                <div class="card-body sales-table">
                                    <div class="table-responsive">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-2">
                                                    <label for="picker1">Date</label>
                                                    <input class="form-control form-control-sm" type="date" name="date" required>
                                                </div>
                                            </div>
                                        </div>

                                        <table class="table table-bordered text-center">
                                            <thead>
                                                <tr>
                                                    <th style="width: 2%"></th>
                                                    <th style="width: 30%">Product <span class="text-danger">*</span></th>
                                                    <th>Quantity <span class="text-danger">*</span></th>
                                                    <th>Buying Price</th>
                                                    <th>Selling Price</th>
                                                    <th>
                                                        <a href="#" class="btn btn-success add_row rounded-circle"><i
                                                                class="fa fa-plus"></i></a>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="addMoreRow">
                                                <tr>
                                                    <td>1</td>
                                                    <td>

                                                        <select class="form-select product_id" id="product_id"
                                                            name="product_id[]" required>
                                                            <option value=""></option>
                                                            @foreach ($products as $product)
                                                                <option data-price="{{ $product->selling_price }}"
                                                                    data-quantity="{{ $product->quantity }}"
                                                                    value="{{ $product->id }}">{{ $product->name }} - N{{ number_format($product->buying_price,0) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="quantity[]" step="0.5" id="quantity" class="form-control" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="buying_price[]" id="buying_price"
                                                            class="form-control price">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="selling_price[]" id="selling_price"
                                                            class="form-control discount">
                                                    </td>
                                                    <td >
                                                        <a href="#"
                                                            class="btn btn-danger btn-sm remove_row rounded-circle"><i
                                                                class="fa fa-times-circle"></i></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <button type="submit" class="btn btn-primary ml-2">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                      
                    </div>
                </form>
              
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $('.add_row').on('click', function() {
            var product = $('.product_id').html();
            var numberofrow = ($('.addMoreRow tr').length - 0) + 1;
            var tr = '<tr><td class="no">' + numberofrow + '</td>' +
                '<td><select class="form-select product_id" name="product_id[]" required>' + product +
                '<td><input type="number" name="quantity[]" step="0.5" class="form-control" required></td>' +
                '<td><input type="number" name="buying_price[]" class="form-control"></td>' +
                '<td><input type="number" name="selling_price[]" class="form-control"></td>' +
                '<td><a class="btn btn-danger btn-sm remove_row rounded-circle"><i class="fa fa-times-circle"></i></a></td></tr>';
            $('.product_id').select2();
            $('.addMoreRow').append(tr);
        });

        $('.addMoreRow').delegate('.remove_row', 'click', function() {
            $(this).parent().parent().remove();
        });

        $('.product_id').select2();

    
    </script>

    <script>
        $(document).ready(function() {

            $(document).on('submit', '#salesForm', function(e) {
                e.preventDefault();
                let formData = new FormData($('#salesForm')[0]);
                $.LoadingOverlay("show");
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "{{ route('purchase.store') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {

                        if (res.status == 201) {
                            $.LoadingOverlay("hide");
                            $('#salesForm')[0].reset();
                            $(".product_id"). val('none').trigger('change');

                            Command: toastr["success"](res.message);
                            toastr.options = {
                                closeButton: false,
                                debug: false,
                                newestOnTop: false,
                                progressBar: false,
                                positionClass: "toast-top-right",
                                preventDuplicates: false,
                                onclick: null,
                                showDuration: "300",
                                hideDuration: "1000",
                                timeOut: "5000",
                                extendedTimeOut: "1000",
                                showEasing: "swing",
                                hideEasing: "linear",
                                showMethod: "fadeIn",
                                hideMethod: "fadeOut",
                            };
                        }
                    }
                })
            });

        });
    </script>
@endsection
