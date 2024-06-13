@extends('layouts.app')
@section('PageTitle', 'Edit Customer')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">Edit Customer</span></div>
                        <div class="col-md-2 float-right"><a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary me-2"> <--- Customer List</a></div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('customers.update',$user->id)}}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="first_name" class="col-form-label">Name:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="{{$user->first_name}}" required>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-form-label">Phone Number:</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{$user->phone}}" required>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{$user->email}}">
                            </div>
                            <div class="form-group">
                                <label for="deposit" class="col-form-label">Deposit Balance:</label>
                                <input type="number" class="form-control" id="deposit" name="deposit" value="{{$user->deposit}}">
                            </div>
                            <div class="form-group">
                                <label for="balance" class="col-form-label">Credit Balance:</label>
                                <input type="number" class="form-control" id="balance" name="balance" value="{{$user->balance}}">
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary d-block">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- #content end -->
@endsection
