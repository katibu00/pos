<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.108.0">
    <title>Signin · El-Habib Plumbing Material and Services Ltd</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <meta name="theme-color" content="#712cf9">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            width: 300px;
            margin: 0 auto;
            margin-top: 100px;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        .card h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .card form {
            margin-bottom: 20px;
        }

        .card .form-floating {
            margin-bottom: 10px;
        }

        .card .checkbox {
            margin-bottom: 10px;
        }

        .card .btn-primary {
            background-color: #712cf9;
            border-color: #712cf9;
        }

        .card .btn-primary:hover {
            background-color: #5d23d4;
            border-color: #5d23d4;
        }

        .card .btn-primary:focus {
            box-shadow: none;
        }

        .card .mt-5 {
            margin-top: 20px;
        }

        .card input[type="email"],
        .card input[type="password"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #f1f3f5;
        }

        .card input[type="email"]:focus,
        .card input[type="password"]:focus {
            outline: none;
            box-shadow: none;
            background-color: #e9ecef;
        }

        .card label {
            margin-bottom: 5px;
        }

        .card .alert {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="card">
        <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
        <ul id="error_list"></ul>
        <form id="loginForm">
            <div class="form-floating">
                <input type="email" class="form-control" id="email" placeholder="name@example.com">
                <label for="email">Email address</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="password" placeholder="Password">
                <label for="password">Password</label>
            </div>

            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" id="remember"> Remember me
                </label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit" id="submit_btn">Sign in</button>
        </form>
        <p class="mt-5 mb-3 text-muted">&copy; El-habib Plumbing Services and Materials</p>
    </div>

    <script src="/jquery-3.6.3.min.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on('submit', '#loginForm', function(e) {
                e.preventDefault();
                var data = {
                    'email': $('#email').val(),
                    'password': $('#password').val(),
                    'remember': $('#remember').val(),
                }
                spinner =
                    '<span class="indicator-pro4gress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span>';
                $('#submit_btn').html(spinner);
                $('#submit_btn').attr("disabled", true);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "/login",
                    data: data,
                    dataType: "json",
                    success: function(response) {

                        if (response.status == 400) {
                            $('#error_list').html("");
                            $('#error_list').addClass('alert alert-danger');
                            $.each(response.errors, function(key, err) {
                                $('#error_list').append('<li>' + err + '</li>');
                            });
                            $('#submit_btn').text("Login");
                            $('#submit_btn').attr("disabled", false);
                        }
                        if (response.status == 401) {
                            $('#error_list').html("");
                            $('#error_list').addClass('alert alert-danger');
                            $('#error_list').append('<li>' + response.message + '</li>');
                            $('#submit_btn').text("Login");
                            $('#submit_btn').attr("disabled", false);
                        }
                        if (response.status == 200) {
                            $('#error_list').html("");
                            $('#error_list').removeClass('alert alert-danger');
                            $('#error_list').addClass('alert alert-success');
                            $('#error_list').append(
                                '<li>Login Successful. Redirecting to Dashboard. . .</li>');

                            if (response.user == 'admin') {
                                window.location.replace('{{ route('admin.home') }}');
                            }
                            if (response.user == 'cashier') {
                                window.location.replace('{{ route('sales.index') }}');
                            }

                        }
                    }
                });
            });
        });
    </script>
</body>

</html>
