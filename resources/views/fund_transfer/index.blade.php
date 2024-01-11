@extends('layouts.app')
@section('PageTitle', 'Funds Transfer')

@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2>Funds Transfer</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTransferModal">+ Add New Transfer</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @include('fund_transfer.table')   
                        </div> 
                        <div class="pagination justify-content-center">
                            {{ $fundTransfers->links() }}
                        </div>                    
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="newTransferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Funds Transfer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="fundTransferForm" method="post" action="{{ route('fund_transfer.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="from_account">From Account:</label>
                            <select class="form-control" id="from_account" name="from_account" required>
                                <option value=""></option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="pos">POS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="to_account">To Account:</label>
                            <select class="form-control" id="to_account" name="to_account" required>
                                <option value=""></option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="pos">POS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount:</label>
                            <input type="text" class="form-control" id="amount" name="amount" required>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        function submitForm() {
            var form = $('#fundTransferForm');
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                success: function (data) {
                    $('#newTransferModal').modal('hide');
                    $('.table').load(location.href+' .table');
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                        });
                    }
                    $('#fundTransferForm')[0].reset(); 
                },
                error: function (xhr, status, error) {
                    // Handle Ajax errors
                    console.error(xhr.responseText);

                    try {
                        var data = JSON.parse(xhr.responseText);
                        if (data.errors && data.errors.length > 0) {
                            // Display validation errors as a list
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: '<ul><li>' + data.errors.join('</li><li>') + '</li></ul>',
                            });
                        } else {
                            // Display a general error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'An error occurred!',
                            });
                        }
                    } catch (e) {
                        // Handle parsing error, if any
                        console.error(e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred!',
                        });
                    }
                }
            });
        }

       
    </script>
@endsection



