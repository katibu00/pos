@extends('layouts.app')

@section('PageTitle', 'Online Store Products')

@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Online Store Products</h2>

                    @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Selling Price</th>
                                    <th>Discounted Price</th>
                                    <th>Featured</th>
                                    <th>Discount Applied</th>
                                    <th>Featured Image Thumbnail</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $key => $product)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>&#8358;{{ number_format($product->selling_price, 2) }}</td>
                                    <td>&#8358;{{ number_format($product->discount_price, 2) }}</td>
                                    <td><span class="badge bg-{{ $product->featured ? 'success' : 'danger' }}">{{ $product->featured ? 'Yes' : 'No' }}</span></td>
                                    <td><span class="badge bg-{{ $product->discount_applied ? 'success' : 'danger' }}">{{ $product->discount_applied ? 'Yes' : 'No' }}</span></td>
                                    <td>
                                        @if($product->onlineProductImages->where('featured', true)->count() > 0)
                                        <img src="{{ asset($product->onlineProductImages->where('featured', true)->first()->image_url) }}" alt="Featured Image" style="width: 50px; height: auto;">
                                        @else
                                        No Featured Image Available
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-info">Details</a>
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">Edit</a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                        </form>
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
</section>
@endsection
