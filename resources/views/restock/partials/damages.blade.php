<h5>Current Damages</h5>
@if($damages->count() > 0)
<table class="table table-striped">
    <thead>
        <tr>
            <th>Stock</th>
            <th>Quantity</th>
            <th>Damage Level</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        @foreach($damages as $damage)
        <tr>
            <td>{{ $damage->stock->name }}</td>
            <td>{{ $damage->quantity }}</td>
            <td>{{ ucfirst($damage->damage_level) }}</td>
            <td>{{ $damage->notes ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>No damages recorded yet.</p>
@endif

<h5>Record New Damage</h5>
<form action="{{ route('restock.store.damages', $restock->id) }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="restock_item_id" class="form-label">Stock Item</label>
        <select class="form-select" id="restock_item_id" name="restock_item_id" required>
            @foreach($restockItems as $item)
                <option value="{{ $item->id }}">{{ $item->stock->name }} (Ordered: {{ $item->ordered_quantity }})</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Damaged Quantity</label>
        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
    </div>
    <div class="mb-3">
        <label for="damage_level" class="form-label">Damage Level</label>
        <select class="form-select" id="damage_level" name="damage_level" required>
            <option value="minor">Minor</option>
            <option value="moderate">Moderate</option>
            <option value="severe">Severe</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="notes" class="form-label">Notes</label>
        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Record Damage</button>
</form>