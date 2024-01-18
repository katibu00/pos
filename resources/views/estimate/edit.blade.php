@extends('layouts.app')
@section('PageTitle', 'Edit Estimate')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <!-- Default panel contents -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">Edit Estimate</span></div>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Discount</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $estimate->product->name }}</td>
                                        <td>{{ $estimate->price }}</td>
                                        <td>{{ $estimate->quantity }}</td>
                                        <td>
                                            <input type="text" class="form-control" name="discount"
                                                value="{{ $estimate->discount }}">
                                        </td>
                                        <td>{{ $estimate->total_price }}</td>
                                    </tr>
                                    <!-- Add more rows if needed -->
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
