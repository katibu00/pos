@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-center mb-4">Export Stocks</h2>

    <form action="{{ route('stocks.export') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="branch_id" class="form-label">Select Branch:</label>
                    <select name="branch_id" id="branch_id" class="form-select" required>
                        <option value="" disabled selected>Select a branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">
                        Please select a branch.
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Select Columns:</label>
                    <div class="row">
                        @php
                            $columns = [
                                'name' => 'Name',
                                'buying_price' => 'Buying Price',
                                'selling_price' => 'Selling Price',
                                'quantity' => 'Quantity',
                                'pending_pickups' => 'Pending Pickups',
                                'critical_level' => 'Critical Level',
                                'created_at' => 'Created At',
                                'updated_at' => 'Updated At',
                            ];
                        @endphp
                        @foreach($columns as $field => $label)
                            <div class="col-6 col-md-4 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="columns[]" id="{{ $field }}" value="{{ $field }}" checked>
                                    <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Export to Excel</button>
                </div>
            </div>
        </div>
    </form>
</div>
<style>
    .card {
    border-radius: 0.5rem;
    border: none;
}

.card-body {
    padding: 1.5rem;
}

.form-select, .form-check-input {
    padding: 0.75rem;
}

.form-check-label {
    margin-left: 0.5rem;
}

.btn-primary {
    background-color: #007bff;
    border: none;
    padding: 0.75rem;
    font-size: 1.1rem;
    border-radius: 0.5rem;
}

@media (max-width: 768px) {
    .card {
        margin-bottom: 1rem;
    }

    .btn-primary {
        padding: 1rem;
    }
}

</style>
@endsection
