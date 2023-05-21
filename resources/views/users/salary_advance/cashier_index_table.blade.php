<table class=" table" style="width:100%">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Staff</th>
            <th scope="col">Date</th>
            <th scope="col">Amount</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($advances as $key => $advance)
            <tr>
                <th scope="row">{{ $key + 1 }}</th>
                <th scope="row">{{ @$advance->staff->first_name.' '.@$advance->staff->last_name }}</th>
                <td>{{ $advance->created_at->diffForHumans() }}</td>
                <td>{{ number_format($advance->amount, 0) }}</td>
                <td>
                    @php
                      $status = $advance->status;
                      $badgeColor = '';
                  
                      switch ($status) {
                        case 'approved':
                          $badgeColor = 'bg-success';
                          break;
                        case 'pending':
                          $badgeColor = 'bg-warning';
                          break;
                        case 'rejected':
                          $badgeColor = 'bg-danger';
                          break;
                      }
                    @endphp
                  
                    <span class="badge {{ $badgeColor }}">{{ ucfirst($status) }}</span>
                  </td>                                           
                   <td>
                    @if($status == 'pending')<button class="btn btn-sm btn-danger mb-1 delete" data-id="{{ $advance->id }}" data-amount="{{ $advance->amount }}"><i class="fa fa-trash"></i></button>@endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>