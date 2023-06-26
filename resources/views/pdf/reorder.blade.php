<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Store Name</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .details {
            margin-bottom: 20px;
        }

        .details-table {
            width: 100%;
        }

        .details-table td {
            vertical-align: top;
            width: 50%;
        }

        .details-table td.right-column {
            text-align: right;
        }

        .details-table td p {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="title">El-Habib Plumbing Materials & Services Ltd.</div>

    <table class="details-table">
        <tr>
            <td>
                <p>Branch: Azare Branch</p>
                <p>Phone: 0916-844-3058</p>
                <p>Email: support@elhabibplumbing.com</p>
                <p>Website: www.elhabibplumbing.com</p>
            </td>
            <td class="right-column">
                <p>Order Number: {{ $records[0]->reorder_no }}</p>
                <p>Supplier Name: {{ $records[0]->supplier->first_name }}</p>
                <p>Date Issued: {{ $records[0]->created_at->format('Y-m-d') }}</p>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th style="text-align: center;">Ordered Qty</th>
                {{-- <th>Old Price</th> --}}
                {{-- <th>Price Change</th> --}}
                <th>Price</th>
                <th>Sub total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $reorder)
                <tr>
                    <td>{{ $reorder->product->name }}</td>
                    <td style="text-align: center;">{{ $reorder->quantity }}</td>
                    {{-- <td>{{ $reorder->buying_price }}</td> --}}
                    {{-- <td><input type="checkbox"></td> --}}
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
