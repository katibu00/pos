<div class="table-responsive text=nowrap">
    <table class="table d-none table-hover" style="width:100%">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Product</th>
                <th scope="col">Buying Price</th>
                <th scope="col">Last Purchase (&#8358;)</th>
                <th scope="col">Current Quantity (&#8358;)</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lows as $key => $low)
                <tr>
                    <td scope="row">{{ $key + 1 }}</td>
                   <td>{{ $low->name }}</td>
                   <td>{{ number_format($low->buying_price,0) }}</td>
                   @php
                    
                       @$last = App\Models\Purchase::where('stock_id',@$low->id)->first();
                     
                   @endphp
                   <td>{{ @$last->created_at? $last->created_at->diffForHumans():'Never' }} ({{ @$last->quantity }})</td>
                   <td>{{ @$low->quantity }}</td>
                </tr>
            @endforeach

        </tbody>

    </table>
</div>
<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        {{-- {{ @$purchases->links() }} --}}
    </ul>
</nav>
