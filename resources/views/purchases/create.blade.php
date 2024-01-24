@extends('layouts.app')
@section('PageTitle', 'Add New Purchases')
@section('content')
<style>
    .read-only-field {
    background-color: #f8f8f8; /* Light gray background color */
    color: #555; /* Dark gray text color */
    cursor: not-allowed; /* Display "not-allowed" cursor on hover to indicate read-only */
}
</style>
    <!-- ============ Body content start ============= -->
    <section id="content">
        <div class="content-wrap mt-3">
            <div class="container clearfix">
                <form action="{{ route('purchase.store') }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-12 mb-4">
                            <div class="card mb-2">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="col-5"><span class="text-bold fs-16">Add New Purchases ({{ auth()->user()->branch->name }} Branch)</span></div>
                                    <div class="col-sm-4 col-md-2"><a class="btn btn-sm btn-secondary me-2" href="{{ route('purchase.index') }}"> <--- Back to list</a></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label for="picker1">Date</label>
                                            <input class="form-control form-control-sm" type="date" name="date" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body sales-table">
                                    @if($errors->any())
                                        <div class="alert alert-danger mt-3">
                                            <ul>
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if(session('success'))
                                    <div class="alert alert-success mt-3">
                                        {{ session('success') }}
                                    </div>
                                @endif
                            
                                @if(session('error'))
                                    <div class="alert alert-danger mt-3">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Product</th>
                                                    <th>Old Purchase Price</th>
                                                    <th>Old Selling Price</th>
                                                    <th>Quantity</th>
                                                    <th>Price Changed</th>
                                                    <th>New Purchase Price</th>
                                                    <th>New Selling Price</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>
                                                        <select class="form-select form-select-sm productSelect" name="product[]" required>
                                                            <option value=""></option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}" data-buying-price="{{ $product->buying_price }}" data-selling-price="{{ $product->selling_price }}">{{ $product->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input class="form-control form-control-sm oldPurchasePrice read-only-field" type="text" name="old_purchase_price[]" readonly></td>
                                                    <td><input class="form-control form-control-sm oldSellingPrice read-only-field" type="text" name="old_selling_price[]" readonly></td>
                                                    <td><input class="form-control form-control-sm" type="text" name="quantity[]"></td>
                                                    <td><input type="checkbox" class="priceChangedCheckbox" name="price_changed[]"></td>
                                                    <td><input class="form-control form-control-sm newPurchasePrice" type="text" name="new_purchase_price[]" style="display: none;"></td>
                                                    <td><input class="form-control form-control-sm newSellingPrice" type="text" name="new_selling_price[]" style="display: none;"></td>
                                                    <td><button type="button" class="btn btn-danger btn-sm deleteRow">X</button></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5"></td>
                                                    <td>Total Buying Price:</td>
                                                    <td><span id="totalBuyingPrice">0.00</span></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <button type="button" class="btn btn-success" id="addRow">+ Add New Row</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
          

            // Add new row button functionality
            $("#addRow").click(function () {
                var rowCount = $("tbody tr").length + 1;

                // Destroy previous select2 instances
                $('.productSelect').select2('destroy');

                // Clone the first row
                var newRow = $("tbody tr:first").clone();

                // Set the new row's serial number
                newRow.find("td:first").text(rowCount);

                // Reset values in the cloned row
                newRow.find(".productSelect").val('');
                newRow.find(".oldPurchasePrice").val('');
                newRow.find(".oldSellingPrice").val('');
                newRow.find(".priceChangedCheckbox").prop('checked', false);
                newRow.find(".newPurchasePrice").val('').hide();
                newRow.find(".newSellingPrice").val('').hide();

                // Append the new row to the table body
                $("tbody").append(newRow);

                // Reinitialize select2 for all product selects
                $('.productSelect').select2();

                updateTotalBuyingPrice();
            });

            $("tbody").on("input", 'input[name="quantity[]"]', function () {
                updateTotalBuyingPrice();
            });

            // Delete row button functionality
            $("tbody").on("click", ".deleteRow", function () {
                $(this).closest("tr").remove();
                updateSerialNumbers();
                updateTotalBuyingPrice();
            });

            // Product select change event
            $("tbody").on("change", ".productSelect", function () {
                var buyingPrice = $(this).find(':selected').data('buying-price');
                var sellingPrice = $(this).find(':selected').data('selling-price');
                $(this).closest("tr").find(".oldPurchasePrice").val(buyingPrice);
                $(this).closest("tr").find(".oldSellingPrice").val(sellingPrice);
                updateTotalBuyingPrice();
            });

            // Checkbox change event
            $("tbody").on("change", ".priceChangedCheckbox", function () {
                var row = $(this).closest("tr");
                if ($(this).is(":checked")) {
                    row.find(".newPurchasePrice").show();
                    row.find(".newSellingPrice").show();
                } else {
                    row.find(".newPurchasePrice").hide();
                    row.find(".newSellingPrice").hide();
                }
                updateTotalBuyingPrice();
            });

            // Update serial numbers
            function updateSerialNumbers() {
                $("tbody tr").each(function (index) {
                    $(this).find("td:first").text(index + 1);
                });
            }

            // Update total buying price
            function updateTotalBuyingPrice() {
                var totalBuyingPrice = 0;
                $("tbody tr").each(function () {
                    var newPurchasePrice = $(this).find(".newPurchasePrice").val();
                    var oldPurchasePrice = $(this).find(".oldPurchasePrice").val();
                    var quantity = $(this).find('input[name="quantity[]"]').val();

                    if ($(this).find(".priceChangedCheckbox").is(":checked") && newPurchasePrice) {
                        totalBuyingPrice += parseFloat(newPurchasePrice) * parseInt(quantity);
                    } else {
                        totalBuyingPrice += parseFloat(oldPurchasePrice) * parseInt(quantity);
                    }
                });

                $("#totalBuyingPrice").text(totalBuyingPrice.toFixed(2));
            }
        });
        $('.productSelect').select2();
    </script>
@endsection
