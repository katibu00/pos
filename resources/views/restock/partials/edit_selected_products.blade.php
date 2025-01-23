<table class="table table-sm selected-products-table">
    <thead>
        <tr><th>#</th><th>Product</th><th>Qty</th><th></th></tr>
    </thead>
    <tbody>
        @foreach($restockItems as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td class="product-name" title="{{ $item->stock->name }}">{{ $item->stock->name }}</td>
            <td>
                <input type="number" name="stocks[{{ $item->stock_id }}][id]" value="{{ $item->stock_id }}" hidden>
                <input type="number" name="stocks[{{ $item->stock_id }}][quantity]" value="{{ $item->ordered_quantity }}" 
                       min="1" class="form-control form-control-sm quantity-input" data-stock-id="{{ $item->stock_id }}">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-link text-danger remove-product" data-stock-id="{{ $item->stock_id }}">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>