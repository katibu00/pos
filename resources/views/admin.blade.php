@extends('layouts.app')
@section('PageTitle', 'Home')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <form class="row row-cols-lg-auto g-3 align-items-end" action="{{ route('change_branch') }}" method="POST">
                    @csrf
                    <div class="col-12">
                        <label for="branch" class="visually-hidden">Password</label>
                        <select id="branch" name="branch_id" class="form-select form-select-sm">
                            <option value=""></option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ auth()->user()->branch_id == $branch->id ? 'selected': ''}}>{{ $branch->name }}</option>
                            @endforeach
                        </select>                   
                     </div>
                    <button type="submit" class="btn btn-sm btn-info col-12">Change</button>
                </form>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
                            <div class="card-header">Today's Sales</div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($todays_total,2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3" style="max-width: 20rem;">
                            <div class="card-header">This Week's Sales</div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($weeks_total,2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info mb-3" style="max-width: 20rem;">
                            <div class="card-header">Discounts (Today)</div>
                            <div class="card-body">
                                <p class="card-text">&#8358;{{ number_format($discounts,2) }}</p>
                            </div>
                        </div>
                    </div>
                   
                </div>


            </div>
        </div>
    </section>
@endsection
