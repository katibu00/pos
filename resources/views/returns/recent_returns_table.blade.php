<div class="card recent-table">      
    <div class="card-body">
        <div class="table-responsive">
            <table class="table recent mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Return #</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recents as $key2 => $recent )
                    @php
                    $total_amount = 0;
                        $sales = App\Models\Returns::select('price','quantity')
                                                ->where('return_no', $recent->return_no)
                                                ->get();
                        foreach ($sales as $sale) {
                            $total_amount += ($sale->price*$sale->quantity);
                        }
                            
                    @endphp 
                        <tr>
                            <td>{{ $key2 + 1 }}</td>
                            <td>{{ $recent->return_no }}</td>
                            <td>&#8358;{{ number_format($total_amount,0) }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>