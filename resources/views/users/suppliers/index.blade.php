@extends('layouts.app')
@section('PageTitle', 'Users')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <!-- Default panel contents -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">Suppliers</span></div>
                        <div class="col-md-2 float-right"><button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal"
                                data-bs-target=".addModal">Add New Supplier</button></div>
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
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $key => $user)
                                        <tr>
                                            <th scope="row">{{ $key + 1 }}</th>
                                            <td>{{ $user->first_name }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>
                                                <a class="btn btn-sm btn-primary mb-1"
                                                    href="{{ route('suppliers.edit', $user->id) }}"> <i
                                                        class="fa fa-edit"></i></a>
                                                <button class="btn btn-sm btn-danger mb-1" data-toggle="modal"
                                                    data-target="#exampleModal{{ $key }}"><i
                                                        class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModal{{ $key }}" tabindex="-1"
                                            role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Delete
                                                            {{ $user->first_name }}?</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('suppliers.delete') }}" method="post">
                                                            @csrf
                                                            <p>You cannot undo this operation once executed.</p>
                                                            <input type="hidden" name="id"
                                                                value="{{ $user->id }}">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-danger ml-2">Delete</button>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
                    <h4 class="modal-title" id="myModalLabel">Add New Supplier</h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                       
                        <div class="form-group">
                            <label for="first_name" class="col-form-label">Name:</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                            @error('first_name')
                                <span class="text-danger">{{  $message }}</span>
                            @enderror
                        </div>
                       
                        <div class="form-group">
                            <label for="email" class="col-form-label">Phone Number:</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                            @error('phone')
                                <span class="text-danger">{{  $message }}</span>
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
