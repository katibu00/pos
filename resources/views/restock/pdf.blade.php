<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restock {{ $restock->restock_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Elhabib Plumbing Services and Materials</h1>
        <p>123 Main Street, City, Country</p>
        <p>Email: info@elhabib.com | Phone: +1234567890</p>
    </div>

    <h2>Restock Details</h2>
    <p><strong>Restock Number:</strong> {{ $restock->restock_number }}</p>
    <p><strong>Type:</strong> {{ ucfirst($restock->type) }}</p>
    <p><strong>Status:</strong> {{ ucfirst($restock->status) }}</p>
    <p><strong>Supplier:</strong> {{ $restock->supplier->name ?? 'N/A' }}</p>
    <p><strong>Total Cost:</strong> ${{ number_format($restock->total_cost, 2) }}</p>

    <h3>Restock Items</h3>
    <table>
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
                <td>${{ number_format($item->new_buying_price, 2) }}</td>
                <td>${{ number_format($item->new_selling_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Expenses</h3>
    @if($expenses->count() > 0)
    <table>
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
                <td>${{ number_format($expense->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No expenses recorded.</p>
    @endif

    <h3>Damages</h3>
    @if($damages->count() > 0)
    <table>
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
</body>
</html>