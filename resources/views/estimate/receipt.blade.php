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
            <g transform="translate(0.000000,597.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                <path
                    d="M2920 5510 c-308 -34 -650 -141 -914 -287 -215 -119 -376 -240 -556
       -422 -355 -357 -581 -795 -670 -1302 -32 -178 -39 -505 -15 -689 69 -545 296
       -1018 674 -1403 106 -108 247 -232 295 -259 10 -5 19 16 36 80 12 48 45 146
       72 219 l50 132 -91 86 c-550 524 -749 1316 -515 2047 259 808 1030 1368 1884
       1368 943 0 1761 -673 1943 -1599 l23 -116 214 -3 c117 -1 217 1 221 5 10 11
       -17 178 -52 322 -137 557 -485 1062 -962 1395 -307 214 -651 351 -1032 411
       -119 19 -482 28 -605 15z" />
                <path
                    d="M3473 4070 c-27 -11 -43 -40 -43 -78 l0 -30 -72 19 c-170 44 -326 61
       -378 41 -62 -24 -107 -103 -97 -168 14 -86 78 -137 173 -135 62 0 198 23 302
       50 l72 19 0 -119 0 -119 -60 0 -60 0 0 -45 0 -45 -135 0 -135 0 0 -100 0 -100
       -107 0 c-140 0 -225 -13 -336 -51 -218 -73 -404 -235 -510 -446 -53 -104 -79
       -216 -86 -373 l-6 -145 -30 -3 c-59 -7 -82 -53 -49 -100 15 -22 17 -22 303
       -22 266 0 289 1 304 18 18 20 22 59 8 81 -5 8 -26 17 -47 20 l-39 7 2 103 c1
       63 9 124 19 155 37 114 128 216 241 269 60 27 80 31 178 35 l111 4 42 -56
       c104 -137 253 -213 437 -223 75 -4 116 -1 165 11 138 35 254 109 331 211 l41
       55 324 0 324 0 0 -60 0 -60 150 0 150 0 0 60 0 60 495 0 495 0 0 225 0 225
       -495 0 -495 0 -1 38 c-1 20 -2 48 -2 62 l-2 25 -147 3 -148 3 0 -66 0 -65
       -345 0 -345 0 0 100 0 100 -135 0 -135 0 0 45 0 45 -60 0 -60 0 0 120 c0 66 2
       120 4 120 3 0 17 -5 33 -11 55 -21 268 -60 335 -60 84 -1 127 22 157 85 33 68
       25 124 -27 180 -35 37 -65 48 -129 47 -77 0 -286 -40 -357 -67 -13 -5 -16 1
       -16 29 0 60 -54 99 -107 77z" />
                <path
                    d="M5117 2703 c-3 -5 -14 -46 -26 -93 -36 -138 -69 -227 -136 -367 -162
       -338 -393 -602 -705 -805 -230 -151 -484 -249 -770 -299 -83 -15 -462 -19
       -570 -6 l-65 7 0 -97 c0 -114 -18 -180 -70 -267 -19 -32 -34 -60 -32 -62 13
       -14 270 -34 427 -34 405 0 742 79 1093 257 379 192 705 485 939 843 81 122
       191 342 244 485 35 96 94 314 109 403 l7 42 -221 0 c-121 0 -222 -3 -224 -7z" />
            </g>
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
        <div id="customer_name_div" style="font-size: 14px; margin-top: 7px;" class=""><span id="customer_name_span"></span></div>
        <div id="transaction_date" style="font-size: 14px; margin-top: 7px;"></div>
        <div id="account_details" style="font-size: 14px; margin-top: 7px;"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
                {{-- <th>Discount</th> --}}
                {{-- <th>Discounted Total</th> --}}
            </tr>
        </thead>
        <tbody id="receipt_body"></tbody>
        <tfoot></tfoot>
    </table>
    <div style="display: flex; justify-content: center;">
        <img src="/generatedBarcode.png" style="max-width: 100%; height: auto;">
    </div>
    <div style="text-align: center; margin-bottom: 15px;">*** Thank you! ***</div>

    <p>.</p><br>
    <p>.</p><br>
</body>
</html>