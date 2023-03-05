<div class="card recent-table">      
    <div class="card-body">
        <div class="table-responsive">
            <table class="table recent mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Estimate #</th>
                        <th>Customer Name</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recents as $key2 => $recent )
                    @php
                    $total_amount = 0;
                        $estimates = App\Models\Estimate::select('price','quantity','discount')
                                                ->where('estimate_no', $recent->estimate_no)
                                                ->get();
                        foreach ($estimates as $estimate) {
                            $total_amount += ($estimate->price*$estimate->quantity)-$estimate->discount;
                        }
                            
                    @endphp 
                        <tr>
                            <td>{{ $key2 + 1 }}</td>
                            <td>{{ $recent->estimate_no }}</td>
                           
                            <td>{{ $recent->customer }}</td>
                            <td>&#8358;{{ number_format($total_amount,0) }}</td>
                            <td>
                                <button type="button" onclick="PrintReceiptContent('{{ $recent->estimate_no}}')" class="btn btn-secondary btn-sm"><i class="fa fa-print"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>