<div class="table-responsive text-nowrap">
    <table class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col">Sales ID</th>
                <th scope="col">Date</th>
                <th scope="col">Name</th>
                <th scope="col" class="text-center">Amount (&#8358;)</th>
                <th scope="col" class="text-center">Discount (&#8358;)</th>
                <th scope="col" class="text-center">Discounted Amount (&#8358;)</th>
                <th scope="col">Note</th>
                <th scope="col">Collected?</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $key => $row)
                @php
                    $total_amount = 0;
                    $total_discount = 0;
                    $saled = App\Models\Sale::select('price', 'quantity', 'discount', 'customer', 'created_at', 'receipt_no', 'note', 'collected')
                        ->where('receipt_no', $row->receipt_no)
                        ->with('buyer')
                        ->get();
                    foreach ($saled as $sale) {
                        $total_amount += $sale->price * $sale->quantity;
                        $total_discount += $sale->discount;
                    }

                @endphp
                <tr>
                    @if ($sales instanceof Illuminate\Pagination\LengthAwarePaginator)
                        <td class="text-center">{{ $key + $sales->firstItem() }}</td>
                    @else
                        <td class="text-center">{{ $key + 1 }}</td>
                    @endif
                    <th scope="row">{{ $sale->receipt_no }}</th>
                    <td>{{ $saled[0]->created_at->format('l, d F') }}</td>
                    <td>
                        @if (is_null($saled[0]->customer))
                            Walk-in Customer
                        @elseif (is_numeric($saled[0]->customer))
                            {{ @$saled[0]->buyer->first_name }}
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($total_amount, 0) }}</td>
                    <td class="text-center">{{ number_format($total_discount, 0) }}</td>
                    <td class="text-center">{{ number_format($total_amount - $total_discount, 0) }}</td>
                    <td>{{ $saled[0]->note }}</td>
                    <td>
                        @if ($saled[0]->collected == 1)
                        <span class="badge bg-success">Collected</span>@else<span
                                class="badge bg-danger">Awaiting</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="dots"></span>
                                <span class="dots"></span>
                                <span class="dots"></span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#"
                                    onclick="PrintReceiptContent('{{ $row->receipt_no }}')"><i class="fa fa-print"></i>
                                    Receipt</a>
                                @if ($saled[0]->collected == 1)
                                    <a class="dropdown-item" href="#"
                                        onclick="confirmPickup('{{ $row->receipt_no }}')">
                                        <i class="fa fa-truck"></i> Awaiting Pickup
                                    </a>
                                @else
                                    <a class="dropdown-item" href="#"
                                        onclick="confirmDeliver('{{ $row->receipt_no }}')">
                                        <i class="fas fa-shipping-fast"></i> Deliver
                                    </a>
                                @endif
                            </div>
                        </div>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            @if ($sales instanceof Illuminate\Pagination\LengthAwarePaginator && $sales->hasPages())
                {{ $sales->links() }}
            @endif
        </ul>
    </nav>
</div>
