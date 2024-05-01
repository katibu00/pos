@extends('layouts.app')

@section('PageTitle', 'Edit Online Store Product Category')

@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card-header">
                        <h2>Edit Online Store Product Category</h2>
                    </div>

                    <!-- Validation Errors -->
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Success Message -->
                    @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    <!-- Edit Category Form -->
                    <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Category Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="image">Category Image</label>
                                <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" style="width: 50px; height: 50px;">
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="featured" name="featured" @if($category->featured) checked @endif>
                                <label class="form-check-label" for="featured">Featured</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
