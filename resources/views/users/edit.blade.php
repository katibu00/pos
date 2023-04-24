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
                        <div class="col-md-2 float-right"><a href="{{ route('users.index') }}" class="btn btn-sm btn-primary me-2">Users List</a></div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('users.update',$user->id)}}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="first_name" class="col-form-label">First Name:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="{{$user->first_name}}" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name" class="col-form-label">Last Name:</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{$user->last_name}}" required>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{$user->email}}" required>
                            </div>
                            <div class="form-group">
                                <label for="position">Branch</label>
                                <select class="form-select" name="branch_id">
                                    <option value="">Select Branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ $user->branch_id == $branch->id ?'selected':''}}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                    <label for="position">Position</label>
                                    <select class="form-select" id="position" name="position" required>
                                        <option value=""></option>
                                        <option value="admin" @if($user->usertype == 'admin') selected @endif>Admin</option>
                                        <option value="cashier"  @if($user->usertype == 'cashier') selected @endif>Cashier</option>
                                    </select>
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-form-label">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary d-block">Update User</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- #content end -->
@endsection
