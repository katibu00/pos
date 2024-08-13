@foreach($branches as $branch)
<div class="col-md-4 mb-3">
    <div class="card bg-gradient-primary text-white shadow-lg rounded-lg overflow-hidden">
        <div class="card-body">
            <h5 class="card-title font-weight-bold mb-2">{{ $branch->name }}</h5>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-uppercase text-sm">Available Balance</span>
                <span class="h3 mb-0">₦{{ number_format($branch->expense_balance, 0) }}</span>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0">
            <small class="text-white">Last updated: {{ $branch->updated_at->diffForHumans() }}</small>
        </div>
    </div>
</div>
@endforeach