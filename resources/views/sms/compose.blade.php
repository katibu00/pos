@extends('layouts.app')
@section('PageTitle', 'Compose SMS')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="card-title">Compose SMS</h2>
                    </div>
                    <div class="card-body">

                        <form id="compose-form">
                            @csrf

                            <div class="form-group">
                                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Enter your message"></textarea>
                                <small id="character-count" class="text-muted">Character count: 0</small>
                                <small id="page-count" class="text-muted">Page count: 1</small>
                            </div>

                            <button type="button" id="send-btn" class="btn btn-primary">Send all Customers</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section><!-- #content end -->
@endsection

@section('css')
    <style>
        .card {
            border: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 10px 20px;
        }

        .card-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        #character-count,
        #page-count {
            display: block;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
@endsection

@section('js')
    <script>
        // Calculate character count and page count
        function calculateCounts() {
            var message = document.getElementById('message').value;
            var characterCount = message.length;
            var pageCount = Math.ceil(characterCount / 160);
            document.getElementById('character-count').textContent = 'Character count: ' + characterCount;
            document.getElementById('page-count').textContent = 'Page count: ' + pageCount;
        }

        document.getElementById('message').addEventListener('input', calculateCounts);
    </script>

    <script>
        $(document).ready(function() {
            $('#send-btn').click(function(e) {
                e.preventDefault();
                var message = $('#message').val();
                if (message.trim() === '') {
                    toastr.error('Message cannot be empty');
                    return;
                }

                // Disable the send button and show loading spinner
                $(this).prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...'
                    );

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: '{{ route('sms.send') }}',
                    data: {
                        message: message
                    },
                    success: function(response) {

                        $('#message').val('');

                        $('#send-btn').prop('disabled', false).text('Send to all Customers');

                        if (response.status === 200) {

                            toastr.success(response.message);
                        } else {

                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {

                        $('#send-btn').prop('disabled', false).text('Send to all Customers');

                        toastr.error('An error occurred while sending the message');
                    }
                });
            });
        });
    </script>
@endsection
