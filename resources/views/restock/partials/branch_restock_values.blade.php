@foreach($branchRestockValues as $branchValue)
<div class="col-md-4 mb-3">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $branchValue->name }}</h5>
            <p class="card-text">Last 30 days: ${{ number_format($branchValue->total_value, 2) }}</p>
        </div>
    </div>
</div>
@endforeach
