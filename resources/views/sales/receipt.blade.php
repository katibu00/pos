<div id="invoice-POS" style="margin-bottom: 20px">

    <center id="top">
        <div class="logo"></div>
        <div class="info">
            <h6>EL-Habib Plumbing Services and Materials - {{ auth()->user()->branch->name }} Branch</h6>
        </div>
        <!--End Info-->
    </center>
    <!--End InvoiceTop-->

    <div id="mid">
        <div class="info">
            <p>
                Address : street city, state 0000</br>
                Email : JohnDoe@gmail.com</br>
                Phone : 555-555-5555</br>
            </p>
        </div>
    </div>
    <!--End Invoice Mid-->

    <div id="bot">

        <div id="table">
            <table style="width: 100%">
                <thead>
                   <tr>
                    <th style="width: 10%">#</th>
                    <th style="text-align: left">Item</th>
                    <th>Qty</th>
                    <th>Amount</th>
                    </tr>
                </thead>

                <tbody id="receipt_body" style="width: 100%">
                
                </tbody>
            </table>
        </div>
        <!--End Table-->

        <div id="legalcopy">
            <p class="legal" style="text-align: center">*** Thank you! *** </p><br/><br/>
            <p>.</p>
            <p>.</p>
        </div>

    </div>
    <!--End InvoiceBot-->
</div>
<!--End Invoice-->

