<div id="invoice-POS" style="margin-bottom: 20px">

    <center id="top">
        <div class="logo"></div>
        <div class="info">
            <h4>EL-Habib Plumbing Services and Materials - {{ auth()->user()->branch->name }} Branch</h4>
            <h5 style="text-decoration: underline;">Estimate</h5>
        </div>
        <!--End Info-->
    </center>
    <em>Estimate ID:</em>  <em class="tran_id"></em>

    <div id="mid">
        <div class="info">
            @if(auth()->user()->branch->name == 'Azare')
            <p>
                Address : Along Ali Kwara Hospital, Azare.<br/>
                Phone : 0916-844-3058<br/>
            </p>
            @endif
            @if(auth()->user()->branch->name == 'Misau')
            <p>
                Address : Kofar Yamma, Misau, Bauchi State<br/>
                Phone : 0901-782-0678<br/>
            </p>
            @endif
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
            <p style="font-size: 12px">NB: This certifcate is estimated cost only. No real transaction occured.</p>
            <p class="legal" style="text-align: center">*** Thank you! *** </p><br/><br/>
            <p>.</p>
            <p>.</p>
        </div>

    </div>
    <!--End InvoiceBot-->
</div>
<!--End Invoice-->

