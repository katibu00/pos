@extends('layouts.app')
@section('PageTitle', 'Edit Inventory')
@section('content')

 <!-- ============ Body content start ============= -->



        <div class="row my-3">
            <div class="col-md-6 mx-auto">
            
                <div class="card mb-5">
                    <div class="card-body">
                        <form action="{{route('stock.update',$stock->id)}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-3 ">Product Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name" value="{{$stock->name}}" id="inputEmail3" placeholder="Name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="cost" class="col-sm-3 ">Cost Price</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="buying_price" value="{{$stock->buying_price}}" id="cost" placeholder="Cost Price">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="retail" class="col-sm-3 ">Retail Price</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="selling_price" value="{{$stock->selling_price}}" id="retail" placeholder="Retail Price">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="quantity" class="col-sm-3 ">Quantity</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="quantity" value="{{$stock->quantity}}" id="quantity" placeholder="Quantity">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="critical_level" class="col-sm-3 ">Critical Level</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="critical_level" value="{{$stock->critical_level}}" id="critical_level" placeholder="Critical Level">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary d-block">Update Inventory</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



    
 
<!-- ============ Body content End ============= -->
     
@endsection
