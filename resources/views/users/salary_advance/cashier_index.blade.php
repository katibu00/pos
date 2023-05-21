@extends('layouts.app')
@section('PageTitle', 'Salary Advance')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <!-- Default panel contents -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">Salary Advance</span></div>
                        <div class="col-md-2 float-right"><button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal"
                                data-bs-target=".addModal">Apply New Salary Advance</button></div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                          @include('users.salary_advance.cashier_index_table')
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
                    <h4 class="modal-title" id="myModalLabel">Apply New Salary Advance</h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('cashier.salary_advance.index') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="first_name" class="col-form-label">Staff:</label>
                            <select class="form-select" name="staff_id">
                                <option value=""></option>
                                @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->first_name.' '.$staff->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="first_name" class="col-form-label">Amount:</label>
                            <input type="number" class="form-control" id="first_name" name="amount" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ml-2">Apply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@section('js')
<script>
    $(document).on('click', '.delete', function(e) {
        e.preventDefault();

        let id = $(this).data('id');
        let amount = $(this).data('amount');

        swal({
                title: "Delete Request of NGN"+amount+"?",
                text: "Once delete, You cannot be able to restore it.",
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
                        url: "{{ route('cashier.salary_advance.delete') }}",
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
