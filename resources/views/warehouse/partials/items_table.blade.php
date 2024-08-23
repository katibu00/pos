<table class="table">
    <thead>
        <tr>
            <th>Warehouse</th>
            <th>Item</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($warehouseItems as $item)
            <tr>
                <td>{{ $item->warehouse->name }}</td>
                <td>{{ $item->stock->name }}</td>
                <td id="quantity-{{ $item->id }}">{{ $item->quantity }}</td>
                <td>
                    <button class="btn btn-sm btn-primary transfer-btn" data-id="{{ $item->id }}" data-quantity="{{ $item->quantity }}">
                        Transfer to Store
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $warehouseItems->links() }}