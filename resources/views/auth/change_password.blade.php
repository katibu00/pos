@extends('layouts.app')
@section('PageTitle', 'Change Password')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">{{ __('Change Password') }}</div>

                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('change.password') }}">
                                    @csrf

                                    <div class="form-group row">
                                        <label for="current_password"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Current Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="current_password" type="password"
                                                class="form-control @error('current_password') is-invalid @enderror"
                                                name="current_password" required autocomplete="current-password">

                                            @error('current_password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="new_password"
                                            class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="new_password" type="password"
                                                class="form-control @error('new_password') is-invalid @enderror"
                                                name="new_password" required autocomplete="new-password">

                                            @error('new_password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="new_password_confirmation"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Confirm New Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="new_password_confirmation" type="password" class="form-control"
                                                name="new_password_confirmation" required autocomplete="new-password">
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Change Password') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

            .form-container {
                max-width: 400px;
                margin: 0 auto;
            }

            .form-label {
                font-weight: bold;
            }

            .btn-primary {
                background-color: #17a2b8;
                border-color: #17a2b8;
            }

            .btn-primary:hover {
                background-color: #138496;
                border-color: #138496;
            }

            .btn-primary:focus {
                box-shadow: none;
            }
        </style>
    @endsection
