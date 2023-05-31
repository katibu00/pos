@extends('layouts.app')
@section('PageTitle', 'Edit User')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <!-- Default panel contents -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">Edit User</span></div>
                        <div class="col-md-2 float-right"><a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-primary me-2">Suppliers List</a></div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('suppliers.update',$user->id)}}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="first_name" class="col-form-label">Name:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="{{$user->first_name}}" required>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-form-label">Phone:</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{$user->phone}}" required>
                            </div>

                          
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary d-block">Update Supplier</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- #content end -->
@endsection
