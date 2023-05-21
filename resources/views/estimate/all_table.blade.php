<div class="table-responsive text-nowrap">
    <table class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col">Estimate ID</th>
                <th scope="col">Date</th>
                <th scope="col">Name</th>
                <th scope="col" class="text-center">Amount (&#8358;)</th>
                <th scope="col" class="text-center">Discount (&#8358;)</th>
                <th scope="col" class="text-center">Discounted Amount (&#8358;)</th>
                <th scope="col">Note</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($estimates as $key => $row )
                @php
                    $total_amount = 0;
                    $total_discount = 0;
                    $estimated = App\Models\Estimate::select('price','quantity','discount','customer','created_at','estimate_no','note')
                                            ->where('estimate_no', $row->estimate_no)
                                            ->get();
                    foreach ($estimated as $estimate) {
                        $total_amount += ($estimate->price*$estimate->quantity);
                        $total_discount+= $estimate->discount;
                    }
                            
                @endphp 
            <tr>
              <td class="text-center">{{ $key + $estimates->firstItem() }}</td>

              <th scope="row">{{ $estimate->estimate_no }}</th>
              <td>{{ $estimated[0]->created_at->format('l, d F') }}</td>
              <td>{{ $estimated[0]->customer }}</td>
              <td class="text-center">{{ number_format($total_amount,0) }}</td>
              <td class="text-center">{{ number_format($total_discount,0) }}</td>
              <td class="text-center">{{ number_format($total_amount-$total_discount,0) }}</td>
              <td>{{ $estimated[0]->note }}</td>

              <td>
                <button type="button" onclick="PrintReceiptContent('{{ $row->estimate_no}}')" class="btn btn-secondary btn-sm"><i class="fa fa-print"></i></button>
                <button type="button" class="btn btn-success btn-sm saleItem" data-note="{{ $estimate->note }}" data-name="{{ $estimate->customer }}" data-estimate_no="{{ $estimate->estimate_no  }}" data-payable="{{ $total_amount-$total_discount }}"><i class="fa fa-money" data-bs-toggle="modal" data-bs-target=".addModal"></i></button>
              </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
          {{ $estimates->links() }}
        </ul>
    </nav>

     <!--  Modal -->
     <div class="modal fade addModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Mark As Sold </h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('estimate.all.store') }}" method="POST">
                    @csrf
                <div class="modal-body">
                  
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-1">
                               <select class="form-select form-select-sm" id="payment_method" name="payment_method" required>
                                    <option value="">--Payment Method--</option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="pos">POS</option>
                                    <option value="credit">Credit</option>
                               </select>
                            </div>
                        </div>
                        <div class="col-md-4 customer_div d-none">
                            <div class="mb-1">
                               <select class="form-select form-select-sm" id="customer" name="customer" required>
                                    <option value="">-- Customer --</option>
                                    @foreach ($customers as $customer)   
                                        <option value="{{ @$customer->id }}">{{ @$customer->first_name }}</option>
                                    @endforeach
                               </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="mb-1">
                              Estimate ID:<span id="estimate_no_span"></span>
                            </div>
                            <div class="mb-1">
                              Amount Payable:<span id="payable"></span>
                            </div>
                            <div class="mb-1">
                              Customer Name:<span id="name"></span>
                            </div>
                            <div class="mb-1">
                              Note:<span id="note"></span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="estimate_no" name="estimate_no">
                    <input type="hidden" id="total_amount" name="total_amount">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary ml-2">Mark as Sold</button>
                </div>
            </form>
            </div>
        </div>
    </div>
