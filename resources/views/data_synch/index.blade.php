@extends('layouts.app')
@section('PageTitle', 'Data Synch')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">Data Synch ({{ auth()->user()->branch->name }})</span></div>
                        <div class="col-md-2 float-right">
                            <button id="syncBtn" class="btn btn-sm btn-primary me-2">Sync Data to Server</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-header bg-primary text-white">
                                        Total Sales Records
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text fs-16">{{ $salesCount }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-header bg-success text-white">
                                        Total Estimates Records
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text fs-16">{{ $estimatesCount }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-header bg-info text-white">
                                        Total Returns Records
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text fs-16">{{ $returnsCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- #content end -->
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Function to handle the button click event
        $('#syncBtn').on('click', function() {
            // Disable the button to prevent multiple clicks during the sync process
            $(this).prop('disabled', true);

            // Show loading animation on the button
            $(this).html('Please wait...');

            // AJAX request for data synchronization
            $.ajax({
                url: '/data-sync/send',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data.message); // You can show a success message to the user if needed.
                },
                error: function(error) {
                    console.error('Error:', error);
                    // Handle errors or show an error message to the user.
                },
                complete: function() {
                    // Re-enable the button and reset its text after the sync process is complete
                    $('#syncBtn').prop('disabled', false);
                    $('#syncBtn').html('Sync Data to Server');
                }
            });
        });
    });
</script>
@endsection
