@extends('layouts.app')
@section('PageTitle', 'Users')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <div class="card">

                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <h3 class="text-bgold fs-20">Customers ({{ auth()->user()->branch->name }} Branch)</h3>
                        </div>
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <div class="form-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search">
                            </div>
                        </div>

                        <div class="col-12 col-md-2 mb-md-0">
                            <button class="btn btn-lsm btn-primary text-white" data-bs-toggle="modal"
                                data-bs-target=".addModal">+
                                New Customer</button>
                        </div>
                    </div>

                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @include('users.customers.table')

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
                    <h4 class="modal-title" id="myModalLabel">Add New Customer</h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">


                        <div class="form-group">
                            <label for="first_name" class="col-form-label">Customer Name:</label>
                            <input type="text" class="form-control" id="first_name" name="first_name">
                            @error('first_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-form-label">Phone Number:</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                            @error('phone')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
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

    <div class="modal fade depositModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Add New Deposit</h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('customers.save.deposit') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="" class="col-form-label">Payment Amount:</label>
                                <input type="number" step="any" class="form-control" placeholder="Amount"
                                    name="amount">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="" class="col-form-label">Payment Method:</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value=""></option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="pos">POS</option>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" value="{{ @$user->id }}" name="customer_id">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ml-2">Add Deposit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@section('js')

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
