<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Quantity</th>
            <th>Level</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stocks as $stock)
            <tr>
                <td>{{ $stock->name }}</td>
                <td>{{ $stock->quantity }}</td>
                <td>
                    @if($stock->quantity == 0)
                        <span class="badge bg-danger">Out of Stock</span>
                    @elseif($stock->quantity <= $stock->critical_level * 0.5)
                        <span class="badge bg-danger">Very Low</span>
                    @elseif($stock->quantity <= $stock->critical_level)
                        <span class="badge bg-warning">Low</span>
                    @elseif($stock->quantity <= $stock->critical_level * 1.5)
                        <span class="badge bg-info">Medium</span>
                    @elseif($stock->quantity <= $stock->critical_level * 2)
                        <span class="badge bg-primary">High</span>
                    @else
                        <span class="badge bg-success">Very High</span>
                    @endif
                </td>
                <td>
                    <div class="form-check">
                        <input class="form-check-input stock-checkbox" type="checkbox" value="{{ $stock->id }}" id="stock-{{ $stock->id }}" data-name="{{ $stock->name }}" data-buying-price="{{ $stock->buying_price }}">
                        <label class="form-check-label" for="stock-{{ $stock->id }}">
                            Select
                        </label>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="d-flex justify-content-center mt-4">
    {{ $stocks->links() }}
</div>