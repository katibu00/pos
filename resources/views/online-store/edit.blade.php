<!-- update.blade.php -->

@extends('layouts.app')

@section('PageTitle', 'Update Online Store Product')

@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <div class="card">
                    <div class="card-header">
                        <h2>Update {{ $product->name }}</h2>
                    </div>

                    <div class="card-body">
                        <form id="updateForm" action="{{ route('products.update', $product->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="original_price">Original Price (&#8358;)</label>
                                <input type="text" class="form-control" id="original_price"
                                    value="{{ $product->original_price }}" name="original_price" required>
                            </div>

                            <div class="form-group">
                                <label for="selling_price">Selling Price (&#8358;)</label>
                                <input type="text" class="form-control" id="selling_price"
                                    value="{{ $product->selling_price }}" name="selling_price" required>
                            </div>

                            <div class="form-group">
                                <label for="discounted_price">Discounted Price (&#8358;)</label>
                                <input type="text" class="form-control" id="discounted_price"
                                    value="{{ $product->discount_price }}" name="discounted_price">
                            </div>

                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="apply_discount" name="apply_discount"
                                    {{ $product->discount_applied ? 'checked' : '' }}>
                                <label class="form-check-label" for="apply_discount">Apply Discount</label>
                            </div>

                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="featured" name="featured"
                                    {{ $product->featured ? 'checked' : '' }}>
                                <label class="form-check-label" for="featured">Mark as Featured</label>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ $product->description }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            

                            <!-- Add more fields as needed -->

                            <div class="form-group">
                                <label for="additional_images">Upload Images</label>
                                <!-- Dropzone will attach to this div -->
                                <div id="myDropzone" class="dropzone"></div>
                            </div>


                            <button type="submit" id="submitForm" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" rel="stylesheet">

@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.0.0/tinymce.min.js"
        integrity="sha512-xEHlM+pBhSw2P/G+5x3BR8723QPEf2bPr4BLV8p8pvtaCHmWVuSzzKy9M0oqWaXDZrB3r2Ntwmc9iJcNV/nfBQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <script>
        tinymce.init({
            selector: 'textarea#description',
            plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            toolbar_mode: 'floating',
        });

        function getDescriptionContent() {
            var content = tinymce.get('description').getContent();
            return content;
        }
    </script>

    <script>
        Dropzone.options.myDropzone = {
            url: "{{ route('products.update', $product->id) }}",
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 5,
            maxFiles: 10,
            maxFilesize: 2, // MB
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            init: function() {
                var myDropzone = this;

                // Add existing images as file previews
                @foreach ($product->onlineProductImages as $image)
                    var mockFile = {
                        name: "{{ $image->image_url }}",
                        size: 12345,
                        rowId: "{{ $image->id }}" // Add the row ID property here
                    };
                    myDropzone.emit("addedfile", mockFile);
                    myDropzone.emit("thumbnail", mockFile, "{{ asset($image->image_url) }}");
                    myDropzone.emit("complete", mockFile);
                @endforeach

                // Update selector to match your button
                document.querySelector("#submitForm").addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });

                // Function to get CSRF token
                function getCSRFToken() {
                    var token = document.head.querySelector('meta[name="csrf-token"]');
                    return token ? token.content : '';
                }

                this.on("sendingmultiple", function(files, xhr, formData) {
                    // Gets triggered when the form is actually being sent.
                    // Append CSRF token to FormData
                    formData.append('_token', getCSRFToken());
                });
                this.on("removedfile", function(file) {

                    var imageRowId = file.rowId;

                    // Check if imageRowId is defined
                    if (typeof imageRowId !== 'undefined') {
                        // Send AJAX request to delete the image
                        $.ajax({
                            url: "{{ route('delete_image_route', '') }}/" + imageRowId,
                            type: 'POST',
                            data: {
                                _token: getCSRFToken(), 
                                id: imageRowId,
                            },
                            success: function(response) {

                                swal("Removed!", "The image has been successfully removed.",
                                    "success");
                            },
                            error: function(response) {
                                // Handle any errors
                                console.error('Failed to delete image:', response);
                            }
                        });
                    } else {
                        // Log or handle the case where imageRowId is undefined
                        console.log('Image row ID is undefined, not sending delete request.');
                    }
                });

            }
        };
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script> <!-- SweetAlert library -->

    <script>
        $(document).ready(function() {
            $('#submitForm').on('click', function(e) {
                e.preventDefault();

                // Create FormData object to collect form data
                var formData = new FormData($('form')[0]);

                // Get the Dropzone instance
                var myDropzone = Dropzone.forElement("#myDropzone");

                // Append each file to FormData
                myDropzone.files.forEach(function(file) {
                    formData.append('images[]', file);
                });

                // Add the rest of your form data to formData
                var descriptionContent = getDescriptionContent();
                formData.append('description_content', descriptionContent);

                // Disable the submit button and show loading spinner
                $('#submitForm').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...'
                );

                // Send AJAX request for updating the product
                $.ajax({
                    url: "{{ route('products.update', $product->id) }}", // Update the URL to the update route
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Reset form and enable submit button
                        $('form')[0].reset();
                        $('#submitForm').prop('disabled', false).html('Submit');

                        // Show success message
                        swal({
                            title: "Success",
                            text: response.message,
                            icon: "success",
                        }).then((value) => {
                            window.location
                                .reload(); // Reload the page after successful update
                        });

                    },
                    error: function(xhr, status, error) {
                        // Enable submit button and show error message
                        $('#submitForm').prop('disabled', false).html('Submit');

                        // Parse Laravel validation errors and display them using SweetAlert2
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value + '';
                        });
                        swal("Error", errorMessage, "error");
                    }
                });
            });
        });
    </script>

@endsection
