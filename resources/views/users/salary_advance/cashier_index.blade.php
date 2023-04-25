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
                            <table class=" table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($advances as $key => $advance)
                                        <tr>
                                            <th scope="row">{{ $key + 1 }}</th>
                                            <td>{{ $advance->created_at->diffForHumans() }}</td>
                                            <td>{{ number_format($advance->amount, 0) }}</td>
                                            <td>{{ ucfirst($advance->status) }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-danger mb-1" data-toggle="modal"
                                                    data-target="#delete{{ $key }}"><i
                                                        class="fa fa-trash"></i></button>
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
                    <h4 class="modal-title" id="myModalLabel">Apply New Salary Advance</h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('cashier.salary_advance.index') }}" method="POST">
                    @csrf
                    <div class="modal-body">
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
