<div class="table-responsive text-nowrap">
    <table class="table d-none table-hover" style="width:100%">
        <thead>
            <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col">Product Name</th>
                <th scope="col" class="text-center">Cost Price (&#8358;)</th>
                <th scope="col" class="text-center">Retail Price (&#8358;)</th>
                <th scope="col" class="text-center">Quantity</th>
                <th scope="col" class="text-center">Awaiting Pickup</th>
                <th scope="col" class="text-center">Awaiting Delivery</th>
                <th scope="col" class="text-center">Critical Level</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stocks as $key => $stock )
                
                <tr>
              <td class="text-center">{{ $key + $stocks->firstItem() }}</td>
              <th scope="row">{{ $stock->name }}</th>
              <td class="text-center">{{ number_format($stock->buying_price,0) }}</td>
              <td class="text-center">{{ number_format($stock->selling_price,0) }}</td>
              <td class="text-center">{{ number_format($stock->quantity,0) }}</td>
              <td class="text-center">@if($stock->pending_pickups == 0) <span class="badge bg-success">-</span> @else <span class="badge bg-danger">{{ number_format($stock->pending_pickups,0) }}</span> @endif</td>
              <td class="text-center">@if($stock->awaiting_delivery == 0) <span class="badge bg-success">-</span> @else <span class="badge bg-warning">{{ number_format($stock->awaiting_delivery,0) }}</span> @endif</td>
              <td class="text-center">{{ number_format($stock->critical_level,0) }}</td>
              <td class="text-center">
                <a class="btn btn-sm btn-primary my-1" href="{{route('stock.edit',$stock->id)}}">
                     <i class="fa fa-edit"></i>
                </a>
                <a class="btn btn-sm btn-info my-1" href="{{route('inventory.copy',$stock->id)}}">
                     <i class="fa fa-copy"></i>
                </a>
                <button class="btn btn-sm btn-warning update-prices"
                         data-id="{{ $stock->id }}"
                        data-name="{{ $stock->name }}"
                        data-buying-price="{{ $stock->buying_price }}"
                        data-selling-price="{{ $stock->selling_price }}">
                    <i class="fa fa-money-bill"></i>
                </button>
                <button class="btn btn-sm btn-danger delete1111" data-id="{{ $stock->id }}">
                    <i class="fa fa-trash"></i>
                </button>
                <a class="btn btn-sm btn-success my-1" href="{{ route('online-store.copy', $stock->id) }}">
                    <i class="fa fa-upload"></i> Copy to Online Store
                </a>
            </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
          {{ $stocks->links() }}
        </ul>
    </nav>