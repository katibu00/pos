<div class="table-responsive text-nowrap">
    <table class="table d-none table-hover" style="width:100%">
        <thead>
            <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col">Shopping List Name</th>
                <th scope="col">Supplier Name</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stocks as $key => $stock )
                    
            <tr>
              <td class="text-center">{{ $key + $stocks->firstItem() }}</td>
              <th scope="row">{{ $stock->name }}</th>
              <th scope="row">{{ $stock->supplier->first_name }}</th>
              
              <td class="text-center">
                  <a class="btn btn-sm btn-primary my-1" href="{{route('stock.edit',$stock->id)}}"> <i class="fa fa-edit"></i></a>
                  <button class="btn btn-sm btn-danger delete1111" data-id="{{ $stock->id }}"><i class="fa fa-trash"></i></button>
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