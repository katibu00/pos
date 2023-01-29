<div class="table-responsive text=nowrap">
    <table class="table d-none table-hover" style="width:100%">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Date</th>
                <th scope="col" class="text-center">Items Purchased</th>
                <th scope="col" class="text-center">Total Cost (&#8358;)</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $key => $purchase)
                <tr>
                    <td scope="row">{{ $key + $purchases->firstItem() }}</td>
                    <th>{{ \Carbon\Carbon::parse(@$purchase->date)->format('l, d F') }}</th>
                    @php
                        $spending = 0;
                        $items = App\Models\Purchase::with('product')->where('date', @$purchase->date)->get();
                        foreach ($items as $key => $item) {
                            $spending += @$item['product']['buying_price'] * @$item->quantity;
                        }
                    @endphp
                    <td  class="text-center">{{ number_format(@$items->count(), 0) }}</td>
                    <td  class="text-center">{{ number_format(@$spending, 0) }}</td>
                    <td>
                        <a class="btn btn-sm btn-primary mb-1"
                            href="{{ route('purchase.details', @$purchase->date) }}"><i class="fa fa-eye"></i></a>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal{{ $key }}" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Delete ?</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('stock.delete') }}" method="post">
                                    @csrf
                                    <p>You cannot undo this operation once executed.</p>
                                    <input type="hidden" name="id" value="{{ @$purchase->id }}">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger ml-2">Delete</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach

        </tbody>

    </table>
</div>
<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        {{ @$purchases->links() }}
    </ul>
</nav>
