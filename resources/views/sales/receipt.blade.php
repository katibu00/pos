<!DOCTYPE html>
<html>
<head>
    <style>
        /* CSS styles */

        /* Reset default browser styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styles */
        body {
            font-family: Arial, sans-serif;
            font-size: 14px; /* Increased font size for better readability */
        }

        /* Header styles */
        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            text-align: center;
        }

        .business-name {
            font-size: 16px; /* Increased font size for better readability */
            font-weight: bold;
            margin-top: 10px; /* Increased margin for better spacing */
        }

        .contact-details {
            font-size: 14px; /* Increased font size for better readability */
            margin-top: 5px; /* Increased margin for better spacing */
            margin-bottom: 10px; /* Increased margin for better spacing */
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            padding: 8px; /* Increased padding for better spacing */
            text-align: left;
            border-bottom: 1px solid #000;
            font-size: 14px; /* Increased font size for better readability */
        }

        th {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="40.000000pt" height="40.000000pt"
                viewBox="0 0 669.000000 597.000000" preserveAspectRatio="xMidYMid meet">
                <!-- SVG path code here -->
            </svg>
        </div>
        <div class="business-name">EL-Habib Plumbing Materials and Services - {{ auth()->user()->branch->name }} Branch</div>
        @if (auth()->user()->branch->name == 'Azare')
        <div class="contact-details">
            Address: Along Ali Kwara Hospital, Azare.<br>
            Phone: 0916-844-3058<br>
            Email: support@elhabibplumbing.com<br>
            Website: www.elhabibplumbing.com
        </div>
        @endif
        @if (auth()->user()->branch->name == 'Misau')
        <div class="contact-details">
            Address: Kofar Yamma, Misau, Bauchi State<br>
            Phone: 0901-782-0678<br>
            Email: support@elhabibplumbing.com<br>
            Website: www.elhabibplumbing.com
        </div>
        @endif
        <div style="font-size: 14px; margin-top: 15px;">Ref ID: <span class="tran_id"></span></div>
        <div id="customer_name_div" style="font-size: 14px; margin-top: 7px;" class=""><span id="customer_name_span"></span></div> 
        <div style="font-size: 14px; margin-top: 7px;">Sale Date & Time: {{ date('F j, Y h:i A') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="receipt_body">
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;">Total:</td>
                <td id="total"></td>
            </tr>
        </tfoot>
    </table>
    <div style="display: flex; justify-content: center;">
        <img src="/generatedBarcode.png" style="max-width: 100%; height: auto;">
    </div>
    <div style="text-align: center; margin-bottom: 15px;">*** Thank you! ***</div>

    <p>.</p><br>
    <p>.</p><br>
</body>
</html>
