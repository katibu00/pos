@extends('layouts.app')
@section('PageTitle', 'Complete Reorder')
@section('content')

    <!-- ============ Body content start ============= -->
    <section id="content">
        <div class="content-wraap mt-3">
            <div class="container clearfix">
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">

                        <div class="card mb-2">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <p>Complete Reorder</p>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <a href="{{ route('reorder.all.index') }}" class="btn btn-primary">Back</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body sales-table">
                                <div class="table-responsive container">
                                    <form action="{{ route('complete.reorder.submit') }}" method="POST">
                                        @csrf
                                        <table class="table">
                                            <thead>
                                              <tr>
                                                <th>Product Name</th>
                                                <th>Ordered Quantity</th>
                                                <th>Supplied Quantity</th>
                                                <th>Before Buying Price</th>
                                                <th>Before Selling Price</th>
                                                <th>Price Change</th>
                                                <th>After Buying Price</th>
                                                <th>After Selling Price</th>
                                              </tr>
                                            </thead>
                                            <tbody>
                                              @foreach ($records as $record)
                                                <tr>
                                                  <td>{{ $record->product->name }}
                                                    <input type="hidden" name="product_id[]" value="{{ $record->product_id }}" />
                                                    <input type="hidden" name="reorder_no" value="{{ $reorderNo }}" />
                                                </td>
                                                  <td>{{ $record->quantity }}</td>
                                                  <td>
                                                    <input type="number" name="supplied_quantity[]" value="{{ $record->quantity }}">
                                                  </td>
                                                  <td>{{ $record->product->buying_price }}</td>
                                                  <td>{{ $record->product->selling_price }}</td>
                                                  <td>
                                                    <input type="checkbox" class="show-after-prices" data-row="{{ $loop->index }}">
                                                  </td>
                                                  <td>
                                                    <input type="number" name="after_buying_price[]" value="{{ $record->after_buying_price }}" class="after-buying-price" style="display: none;">
                                                  </td>
                                                  <td>
                                                    <input type="number" name="after_selling_price[]" value="{{ $record->after_selling_price }}" class="after-selling-price" style="display: none;">
                                                  </td>
                                                </tr>
                                              @endforeach
                                            </tbody>
                                          </table>
                                          <button type="submit" class="btn btn-primary">Complete Reorder</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('.show-after-prices').change(function() {
                var row = $(this).data('row');
                var afterBuyingPriceInput = $('.after-buying-price').eq(row);
                var afterSellingPriceInput = $('.after-selling-price').eq(row);

                if ($(this).is(':checked')) {
                    afterBuyingPriceInput.show();
                    afterSellingPriceInput.show();
                    afterBuyingPriceInput.attr('required', 'required');
                    afterSellingPriceInput.attr('required', 'required');
                } else {
                    afterBuyingPriceInput.hide();
                    afterSellingPriceInput.hide();
                    afterBuyingPriceInput.removeAttr('required');
                    afterSellingPriceInput.removeAttr('required');
                }
            });
        });
    </script>
@endsection







