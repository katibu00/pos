@extends('layouts.app')
@section('PageTitle', 'Home')

@section('content')
<section id="content" style="background: rgb(240, 240, 240)">
    <div class="content-wrap">
        <div class="container">
            <div class="container">
                <div class="row">

                    <div class="col-md-12">
                        <form class="row" action="{{ route('admin.view_cashier_dashboard') }}" method="POST" id="date_form">
                            @csrf
                            <div class="row">  
                                <div class="col-md-3">
                                    <label for="staff_id" class="form-label">Select Cashier</label>
                                    <select class="form-select form-select-sm" id="staff_id" name="staff_id" required>
                                        @foreach ($staffss as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->first_name.' '.$staff->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="date_type" class="form-label">Select Date Type</label>
                                    <select class="form-select form-select-sm" id="date_type" name="date_type">
                                        <option value="single" {{ isset($date_type) && $date_type === 'single' ? 'selected' : '' }}>Single Date</option>
                                        <option value="range" {{ isset($date_type) && $date_type === 'range' ? 'selected' : '' }}>Date Range</option>
                                    </select>
                                </div>
                                <div class="col-md-4"> 
                                     <div id="single_date_fields" style="{{ isset($date_type) && $date_type === 'single' ? 'display: block;' : 'display: none;' }}">
                                        <label for="selected_date" class="form-label">Selected Date</label>
                                        <input type="date" class="form-control form-control-sm" id="selected_date" name="selected_date" {{ isset($date_type) && $date_type === 'single' ? 'required' : '' }} value="{{ isset($selected_date) ? $selected_date : '' }}">
                                    </div>
                                    <div id="range_date_fields" style="{{ isset($date_type) && $date_type === 'range' ? 'display: block;' : 'display: none;' }}">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" {{ isset($date_type) && $date_type === 'range' ? 'required' : '' }} value="{{ isset($start_date) ? $start_date : '' }}">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" {{ isset($date_type) && $date_type === 'range' ? 'required' : '' }} value="{{ isset($end_date) ? $end_date : '' }}">
                                    </div>
                                </div>

                                <div class="col-md-2">  <label class="invisible">Submit</label>
                                    <button type="submit" class="btn btn-sm btn-primary text-white col-12">View Stats</button>
                                </div>
                            </div>
                            
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

@endsection


@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var dateTypeSelect = document.getElementById('date_type');
        var singleDateFields = document.getElementById('single_date_fields');
        var rangeDateFields = document.getElementById('range_date_fields');

        // Function to show/hide fields based on selected date type
        function toggleDateFields() {
            if (dateTypeSelect.value === 'single') {
                singleDateFields.style.display = 'block';
                rangeDateFields.style.display = 'none';
                document.getElementById('start_date').removeAttribute('required');
                document.getElementById('end_date').removeAttribute('required');
            } else {
                singleDateFields.style.display = 'none';
                rangeDateFields.style.display = 'block';
                document.getElementById('selected_date').removeAttribute('required');
                document.getElementById('start_date').setAttribute('required', '');
                document.getElementById('end_date').setAttribute('required', '');
            }
        }

        // Initial invocation
        toggleDateFields();

        // Event listener for date type change
        dateTypeSelect.addEventListener('change', toggleDateFields);
    });
</script>

@endsection