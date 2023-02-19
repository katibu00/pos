<div class="card recent-table">      
    <div class="card-body">
        <div class="table-responsive">
            <table class="table recent mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Return ID</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recents as $key2 => $recent )
                    @php
                    $total_amount = 0;
                        $returns = App\Models\Returns::select('price','quantity','discount')
                                                ->where('return_no', $recent->return_no)
                                                ->get();
                        foreach ($returns as $return) {
                            $total_amount += ($return->price*$return->quantity)-$return->discount;
                        }
                            
                    @endphp 
                        <tr>
                            <td>{{ $key2 + 1 }}</td>
                            <td>{{ $recent->return_no }}</td>
                            <td>&#8358;{{ number_format($total_amount,0) }}</td>
                            <td>
                                <button type="button" onclick="PrintReceiptContent('{{ $recent->return_no}}')" class="btn btn-secondary btn-sm"><i class="fa fa-print text-white"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>