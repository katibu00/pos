@extends('layouts.app')
@section('PageTitle', 'Users')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <!-- Default panel contents -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">{{ $user->first_name.'\'s Profile' }}</span></div>
                        {{-- <div class="col-md-2 float-right"><button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal"
                                data-bs-target=".addModal"> <- Back</button></div> --}}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h4>Shopping History</h4>
                                <div class="table-responsive border">
                                    <table class=" table" style="width:100%; font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Item</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">Total</th>
                                                <th scope="col">status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dates as $key => $date)
                                                @php
                                                    $total_amount = 0;
                                                    $sales = App\Models\Sale::select('stock_id', 'price', 'quantity', 'discount','status')
                                                        ->where('receipt_no', $date->receipt_no)
                                                        ->get();
                                                @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td colspan="2">{{ $date->created_at->format('l, d F') }}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                @foreach ($sales as $key2 => $sale)
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $sale['product']['name'] }}</td>
                                                        <td>{{ number_format($sale->price, 0) }}</td>
                                                        <td>{{ number_format($sale->quantity, 0) }}</td>
                                                        <td>{{ number_format($sale->price * $sale->quantity, 0) }}</td>
                                                        <td>{{ $sale->status ? $sale->status : '--' }}</td>
                                                    </tr>
                                                    @php
                                                        $total_amount += $sale->price * $sale->quantity - $sale->discount;
                                                    @endphp
                                                @endforeach
                                                <tr class="bg-info text-white">
                                                    <td colspan="5">Total</td>
                                                    <td>{{ number_format($total_amount, 0) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                   
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h4>Payment History</h4>

                                <div class="table-responsive border">
                                    <table class=" table" style="width:100%; font-size: 12px;">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Method</th>
                                            </tr>
                                        </thead>
                                        @forelse ($payments as $key => $payment)
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ $payment->created_at->diffForHumans() }}</td>
                                                <td>{{ number_format($payment->payment_amount, 0) }}</td>
                                                <td>{{ number_format($payment->payment_type, 0) }}</td>
                                            </tr>

                                        @empty
                                            <tr>
                                                <td colspan="4" class="bg-danger text-white"> No Records Found</td>
                                            </tr>
                                        @endforelse

                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h4>Summary</h4>
                                <div class="table-responsive border">
                                    <table class=" table" style="width:100%; font-size: 12px;">
                                        <tr>
                                            <th>Sales Count</th>
                                            <td>---</td>
                                        </tr>
                                        <tr>
                                            <th>Balance</th>
                                            <td>---</td>
                                        </tr>
                                        <tr>
                                            <th>Discount</th>
                                            <td>---</td>
                                        </tr>
                                    </table>
                                </div>
                                <button class="btn btn-primary mt-2" data-bs-toggle="modal"
                                data-bs-target=".addModal">Add Payment</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section><!-- #content end -->

   <!-- Large Modal -->
   <div class="modal fade addModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Add New Payment</h4>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <form action="{{ route('customers.save.payment') }}" method="POST">
                @csrf
                <div class="modal-body">

                   
                <div class="form-group">
                    <label for="" class="col-form-label">Amount:</label>
                    <input type="number" class="form-control" id="payment_amount" name="payment_amount" required>
                </div>
                <div class="form-group">
                    <label for="" class="col-form-label">Payment Method:</label>
                    <select class="form-select" name="payment_method" required>
                        <option value=""></option>
                        <option value="cash">Cash</option>
                        <option value="transfer">Transfer</option>
                        <option value="pos">POS</option>
                    </select>
                </div>
                <input type="hidden" value="{{ $user->id }}" name="customer_id">
                <div class="table-responsive">
                <table class="table">
                    <thead>
                      <tr>
                        <th>S/N</th>
                        <th>Sales ID</th>
                        <th>Amount</th>
                        <th>Payment Option</th>
                        <th>Partial Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                        @php
                            $grand_total = 0;
                        @endphp
                        @foreach ($dates as $key => $date)
                        @php
                            $total_amount = 0;
                            $sales = App\Models\Sale::select('stock_id', 'price', 'quantity', 'discount','status')
                                ->where('receipt_no', $date->receipt_no)
                                ->get();
                            foreach ($sales as $sale) {
                                $total_amount += $sale->price * $sale->quantity - $sale->discount;
                            }
                            $grand_total += $total_amount;
                        @endphp
                      <tr>
                        <td>{{ $key+1 }}</td>
                        <td>${{ $date->receipt_no }}</td>
                        <td>{{ $total_amount }}</td>
                        <td>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="payment_option[]{{ $key }}" id="fullPayment{{ $date->receipt_no }}" value="Full Payment">
                              <label class="form-check-label" for="fullPayment{{ $date->receipt_no }}">Full </label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="payment_option[]{{ $key }}" id="partialPayment{{ $date->receipt_no }}" value="Partial Payment">
                              <label class="form-check-label" for="partialPayment{{ $date->receipt_no }}">Partial</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="payment_option[]{{ $key }}" id="noPayment{{ $date->receipt_no }}" checked value="No Payment">
                              <label class="form-check-label" for="noPayment{{ $date->receipt_no }}">None</label>
                            </div>
                          </td>
                          <input type="hidden" name="receipt_no[]" value="{{ $date->receipt_no }}">
                          <input type="hidden" name="full_price[]" value="{{ $total_amount }}">
                        <td class="partial-amount d-none">
                          <input type="number" name="partial_amount[]" class="form-control partial-amount-input" placeholder="Enter Partial Amount">
                        </td>
                        <td class="holdtd"></td>
                        
                  
                      </tr>
                      @endforeach
                     
                    </tbody>
                  </table>
                </div>
                  <input type="hidden" id="grand_total" value="{{ $grand_total }}">
                  <h3>Grand Total: <span id="grand_total_span"></span></h3>
                  
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary ml-2">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection

@section('js')
<script>
$(document).ready(function() {

    var paid_amount = $('#paid_amount').val();
    var grand_total = $('#grand_total').val();
    $('#grand_total_span').html(grand_total);
    var new_grand_total = 0;
    $('input[type="radio"]').click(function() {
      var selectedOption = $(this).val();

      if (selectedOption === 'Partial Payment') {
        $(this).closest('tr').find('.partial-amount').removeClass('d-none');
        $(this).closest('tr').find('.holdtd').addClass('d-none');
        var price = $(this).closest('tr').find('td:eq(2)').text();

      }else
      {
        $(this).closest('tr').find('.partial-amount').addClass('d-none');
        $(this).closest('tr').find('.holdtd').removeClass('d-none');
      }

      if (selectedOption === 'Full Payment') {
       
        var full_paid = $(this).closest('tr').find('td:eq(2)').text();
        // console.log(full_paid);
        new_grand_total = (parseInt(grand_total) -  parseInt(full_paid));
        $('#grand_total_span').html(parseInt(new_grand_total));

      }

    });


    $(".partial-amount-input").on("keyup", function() {
    var partial_paid = $(this).val();
    var price = $(this).closest('tr').find('td:eq(2)').text();
    new_grand_total = grand_total - partial_paid;
    $('#grand_total_span').html(new_grand_total);
  
  });




   



  });
  </script>
@endsection
