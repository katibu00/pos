@extends('layouts.app')

@section('PageTitle', 'Copy Products to Online Store')

@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <div class="card">
                    <div class="card-header">
                        <h2>Copy {{ $stock->name }} to Online Store</h2>
                        <p>
                            <strong>Product Name:</strong> {{ $stock->name }} &nbsp;&nbsp;&nbsp;
                            <strong>Buying Price:</strong> &#8358;{{ number_format($stock->buying_price, 2) }}
                            &nbsp;&nbsp;&nbsp;
                            <strong>Selling Price:</strong> &#8358;{{ number_format($stock->selling_price, 2) }}
                            &nbsp;&nbsp;&nbsp;
                            <strong>Quantity:</strong> {{ $stock->quantity }}
                        </p>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('online-store.copy.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="stock_id" value="{{ $stock->id }}">
                            <div class="form-group">
                                <label for="original_price">Original Price (&#8358;)</label>
                                <input type="text" class="form-control" id="original_price" value="{{ $stock->selling_price }}" name="original_price" required>
                            </div>
                            <div class="form-group">
                                <label for="selling_price">Selling Price (&#8358;)</label>
                                <input type="text" class="form-control" id="selling_price" name="selling_price" required>
                            </div>
                            <div class="form-group">
                                <label for="discounted_price">Discounted Price (&#8358;)</label>
                                <input type="text" class="form-control" id="discounted_price" name="discounted_price">
                            </div>
                            
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="apply_discount" name="apply_discount">
                                <label class="form-check-label" for="apply_discount">Apply Discount</label>
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="featured" name="featured">
                                <label class="form-check-label" for="featured">Mark as Featured</label>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="additional_images">Upload Images</label>
                                <!-- Dropzone will attach to this div -->
                                <div id="myDropzone" class="dropzone"></div>
                            </div>
                            <button type="button" id="submitForm" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" rel="stylesheet">

@endsection

@section('css')
<style>
    
</style>
@endsection


@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.0.0/tinymce.min.js" integrity="sha512-xEHlM+pBhSw2P/G+5x3BR8723QPEf2bPr4BLV8p8pvtaCHmWVuSzzKy9M0oqWaXDZrB3r2Ntwmc9iJcNV/nfBQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
    // Dropzone configuration
    Dropzone.options.myDropzone = {
    url: "{{ route('online-store.copy.store') }}",
    autoProcessQueue: false,
    uploadMultiple: true,
    parallelUploads: 5,
    maxFiles: 10,
    maxFilesize: 2, // MB
    acceptedFiles: 'image/*',
    addRemoveLinks: true,
    init: function() {
        var myDropzone = this;

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
        $('#submitForm').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');

        // Send AJAX request
        $.ajax({
            url: "{{ route('online-store.copy.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Reset form and enable submit button
                $('form')[0].reset();
                $('#submitForm').prop('disabled', false).html('Submit');

                swal({
                    title: "Success",
                    text: response.message,
                    icon: "success",
                }).then((value) => {
                    window.location.href = "{{ route('stock.index') }}";
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
