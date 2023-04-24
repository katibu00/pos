@extends('layouts.app')
@section('PageTitle', 'Return a Credit Sales')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">Return a Credit Sale ({{ auth()->user()->branch->name }})</span></div>
                        <div class="col-md-2 float-right"><a href="javascript:void(0)" onclick="history.back();" class="btn btn-sm btn-primary me-2"><-- Go back to User Profile</a></div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.return.index') }}" method="post">
                            @csrf
                        <div class="table-responsive">
                            <table class=" table"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">S/N</th>
                                        <th scope="col">Item</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Discount</th>
                                        <th scope="col">Purchased Qty</th>
                                        <th scope="col">Returned Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $key => $sale)
                                        <tr> 
                                            <th>{{ $key+1 }}</th>
                                            <td>
                                                <select class="form-select form-select-sm" name="product_id[]" readonly>
                                                    <option value="{{ $sale->product->id }}">{{ $sale->product->name }}</option>
                                                </select>
                                            </td>
                                            <input type="hidden" value="{{ $sale->id }}" name="sale_id[]">
                                            <td><input type="number" class="form-control form-control-sm" value="{{ $sale->price }}" name="price[]" readonly></td>
                                            <td><input type="number" class="form-control form-control-sm" value="{{ $sale->discount }}" name="discount[]" readonly></td>
                                            <td><input type="number" class="form-control form-control-sm" value="{{ $sale->quantity }}" name="quantity[]" readonly></td>
                                            <td><input type="number" class="form-control form-control-sm" name="returned_qty[]" {{ $sale->quantity < 1 ? 'readonly': '' }}></td>
                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>
                        </div>
                        <button type="submit" class="btn btn-secondary">Submit</button>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </section><!-- #content end -->
   

@endsection
