<div class="card recent-table">      
    <div class="card-body">
        <div class="table-responsive">
            <table class="table recent mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sale ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recents as $key2 => $recent )
                    @php
                    $total_amount = 0;
                        $sales = App\Models\Sale::select('price','quantity','discount')
                                                ->where('receipt_no', $recent->receipt_no)
                                                ->get();
                        foreach ($sales as $sale) {
                            $total_amount += ($sale->price*$sale->quantity)-$sale->discount;
                        }
                            
                    @endphp 
                        <tr>
                            <td>{{ $key2 + 1 }}</td>
                            <td>{{ $recent->receipt_no }}</td>
                            @php
                                $name = App\Models\User::select('first_name')->where('id',$recent->customer_name)->first();
                            @endphp
                            <td>{{ @$name->first_name }}</td>
                            <td>&#8358;{{ number_format($total_amount,0) }}</td>
                            <td>
                                <button type="button" onclick="PrintReceiptContent('{{ $recent->receipt_no}}')" class="btn btn-secondary btn-sm"><i class="fa fa-print text-white"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>