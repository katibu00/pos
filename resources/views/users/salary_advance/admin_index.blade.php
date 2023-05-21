@extends('layouts.app')
@section('PageTitle', 'Salary Advance')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <div class="card">
                    <!-- Default panel contents -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">Salary Advance This Month ({{ auth()->user()->branch->name }} Branch)</span></div>
                    </div>
                    <div class="card-body">
                        @include('users.salary_advance.admin_index_table')
                    </div>

                </div>
            </div>
        </div>
    </section><!-- #content end -->



    <div class="modal fade depositModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
                                <input type="number" step="any" class="form-control" placeholder="Amount" name="amount">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="" class="col-form-label">Payment Method:</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value=""></option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="POS">POS</option>
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
    $(document).on('click', '.reject', function(e) {
        e.preventDefault();

        let id = $(this).data('id');
        let amount = $(this).data('amount');

        swal({
                title: "Reject Request of NGN"+amount+"?",
                text: "Only Approved Request will be deducted from staff Salaries",
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
                        url: "{{ route('cashier.salary_advance.reject') }}",
                        data: data,
                        dataType: "json",
                        success: function(res) {

                            if(res.status == 200)
                            {
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
                                    $('.table').load(location.href + ' .table');

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
    $(document).on('click', '.approve', function(e) {
        e.preventDefault();

        let id = $(this).data('id');
        let amount = $(this).data('amount');

        swal({
                title: "Approve Request of NGN"+amount+"?",
                text: "Only Approved Request will be deducted from staff Salaries",
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
                        url: "{{ route('cashier.salary_advance.approve') }}",
                        data: data,
                        dataType: "json",
                        success: function(res) {

                            if(res.status == 200)
                            {
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
                                    $('.table').load(location.href + ' .table');

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
