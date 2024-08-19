<h4>Restock Summary</h4>
<p>Restock Number: {{ $restock->restock_number }}</p>
<p>Type: {{ ucfirst($restock->type) }}</p>
<p>Status: {{ ucfirst($restock->status) }}</p>
<p>Supplier: {{ $restock->supplier->name ?? 'N/A' }}</p>
<p>Total Cost: ₦{{ number_format($restock->total_cost, 2) }}</p>

<h5>Restock Items</h5>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Stock</th>
            <th>Ordered Quantity</th>
            <th>Received Quantity</th>
            <th>New Buying Price</th>
            <th>New Selling Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($restockItems as $item)
        <tr>
            <td>{{ $item->stock->name }}</td>
            <td>{{ $item->ordered_quantity }}</td>
            <td>{{ $item->received_quantity ?? 'N/A' }}</td>
            <td>₦{{ number_format($item->new_buying_price, 2) }}</td>
            <td>₦{{ number_format($item->new_selling_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h5>Expenses</h5>
@if($expenses->count() > 0)
<table class="table table-striped">
    <thead>
        <tr>
            <th>Type</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $expense)
        <tr>
            <td>{{ ucfirst($expense->expense_type) }}</td>
            <td>₦{{ number_format($expense->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>No expenses recorded.</p>
@endif

<h5>Damages</h5>
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
<p>No damages recorded.</p>
@endif