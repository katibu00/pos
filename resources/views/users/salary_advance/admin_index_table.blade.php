<div class="table-responsive">
    <table class=" table"
        style="width:100%">
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
            @php
                $grand_total_approved = 0;
            @endphp
            @foreach ($staffs as $key => $staff)
           
                <tr>
                    <th scope="row">{{ $key + 1 }}</th>
                    <td>{{ $staff->first_name.' '. $staff->last_name }}</td>
                     <td colspan="4"></td>
                </tr>
                @php
                    $total_approved = 0;
                    $requests = App\Models\SalaryAdvance::where('staff_id',$staff->id)
                                        ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(40))
                                        ->get();
                @endphp

                @foreach ($requests as $key2 => $request)
                @php
                        if($request->status == 'approved')
                        {
                            $total_approved += $request->amount;
                        }
                @endphp
                <tr>
                    <td></td>
                    <th scope="row">{{ $key2 + 1 }}</th>
                    <td>{{ $request->created_at->format('d-M-y') }}</td>
                    <td>&#8358;{{ number_format($request->amount, 0) }}</td>
                    <td>
                        @php
                          $status = $request->status;
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
                        <button class="btn btn-sm btn-success mb-1 approve" data-id="{{ $request->id }}" data-amount="{{ $request->amount }}"><i class="fa fa-check"></i></button>
                       @if($status != 'approved') <button class="btn btn-sm btn-danger mb-1 reject" data-id="{{ $request->id }}" data-amount="{{ $request->amount }}"><i class="fa fa-times"></i></button>@endif
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="4"></td>
                    <td>
                        <p>Staff Total Salary Advanced:</p>
                    </td>
                    <td>&#8358;{{ number_format($total_approved,0) }}</td>
                </tr>
            @endforeach

        </tbody>

    </table>
</div>