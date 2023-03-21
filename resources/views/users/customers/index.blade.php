@extends('layouts.app')
@section('PageTitle', 'Users')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <div class="card">
                    <!-- Default panel contents -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold text-danger fs-16">Customers ({{ auth()->user()->branch->name }} Branch)</span></div>
                        <div class="col-md-2 float-right"><button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal"
                                data-bs-target=".addModal">Add New Customer</button></div>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class=" table"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Balance</th>
                                        <th scope="col">Last Payment</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $key => $user)
                                   
                                        <tr>
                                            <th scope="row">{{ $key + 1 }}</th>
                                            <td>{{ $user->first_name }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>&#8358;{{ number_format($user->balance) }}</td>
                                            @php
                                                $payment = App\Models\Payment::select('created_at','payment_amount')->where('customer_id',$user->id)->latest()->first();
                                            @endphp
                                            <td>{!! @$payment ? '&#8358;'.number_format($payment->payment_amount,0).', '.$payment->created_at->diffForHumans():' - ' !!}</td>
                                            <td>
                                                <a class="btn btn-sm btn-primary mb-1"
                                                    href="{{ route('customers.profile', $user->id) }}" title="View Profile"> <i
                                                        class="fa fa-user"></i></a>
                                                <button class="btn btn-sm btn-danger mb-1 deleteItem" data-id="{{ $user->id }}" data-name="{{ $user->first_name }}"><i class="fa fa-trash"></i></button>
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
                            <input type="text" class="form-control" id="first_name" name="first_name"  required>
                            @error('first_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                       
                        <div class="form-group">
                            <label for="email" class="col-form-label">Phone Number:</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
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


@endsection

@section('js')
<script>
    $(document).on('click', '.deleteItem', function(e) {
        e.preventDefault();

        let id = $(this).data('id');
        let name = $(this).data('name');

        swal({
                title: "Delete "+name+"?",
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

                            if(res.status == 200)
                            {
                                Command: toastr["success"](
                                        "User deleted Successfully."
                                    );
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
                                    window.location.replace('{{ route('customers.index') }}');

                            }else
                            {

                            Command: toastr["error"](
                            "Error Occured"
                                );
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
                    });

                }
            });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
