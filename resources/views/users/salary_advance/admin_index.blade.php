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

                        <div class="table-responsive">
                            <table class=" table"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Cashier</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($staffs as $key => $staff)
                                   
                                        <tr>
                                            <th scope="row">{{ $key + 1 }}</th>
                                            <td>{{ $staff->first_name.' '. $staff->last_name }}</td>
                                             <td colspan="4"></td>
                                        </tr>
                                        @php
                                            $requests = App\Models\SalaryAdvance::where('cashier_id',$staff->id)->whereMonth('created_at',  \Carbon\Carbon::now()->month)->get();
                                        @endphp

                                        @foreach ($requests as $key2 => $request)
                                        <tr>
                                            <td></td>
                                            <th scope="row">{{ $key + 1 }}</th>
                                            <td>{{ $request->created_at->diffForHumans() }}</td>
                                            <td>&#8358;{{ number_format($request->amount, 0) }}</td>
                                            <td>{{ ucfirst($request->status) }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-success mb-1" data-toggle="modal"
                                                    data-target="#delete{{ $key }}"><i
                                                        class="fa fa-check"></i></button>
                                                <button class="btn btn-sm btn-danger mb-1" data-toggle="modal"
                                                    data-target="#delete{{ $key }}"><i
                                                        class="fa fa-times"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endforeach

                                </tbody>

                            </table>
                        </div>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
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
