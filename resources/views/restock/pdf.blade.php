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
        <p>Address: Along Ali Kwara Hospital, Azare.</p>
        <p>Email: support@elhabibplumbing.com | Phone: +234 703 610 0364</p>
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
                <th>#</th>
                <th>Product</th>
                <th>Ordered Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($restockItems as $key => $item)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $item->stock->name }}</td>
                <td>{{ $item->ordered_quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>