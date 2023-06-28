@extends('layouts.app')
@section('PageTitle', 'SMS Balance')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="card-title">SMS Balance</h2>
                    </div>
                    <div class="card-body">

                        <div class="balance-container">
                            <div class="balance-icon">
                                <i class="icon-line2-speech-bubble"></i>
                            </div>
                            <div class="balance-text">
                                Your Balance:
                            </div>
                            <div class="balance-value">
                                {{ number_format($balance,2) }}
                            </div>
                        </div>

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

    .balance-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: 24px;
    }

    .balance-icon {
        color: #17a2b8;
    }

    .balance-value {
        font-weight: bold;
    }
</style>
@endsection
