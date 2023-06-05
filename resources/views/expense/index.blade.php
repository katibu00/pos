@extends('layouts.app')
@section('PageTitle', 'Expense')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">Expense ({{ auth()->user()->branch->name }})</span></div>
                        <div class="col-md-2 float-right"><button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal"
                                data-bs-target=".addModal">Record New Expense(s)</button></div>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class=" table"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Dates</th>
                                        <th scope="col">Expense</th>
                                        <th class="text-center">Amount</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dates as $key => $date)
                                        <tr class="bg-info text-white"> 
                                            <th scope="row">{{ \Carbon\Carbon::parse(@$date->date)->format('l, d F') }}</th>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        @php
                                            $items = App\Models\Expense::where('branch_id', auth()->user()->branch_id)->where('date',$date->date)->get(); 
                                            $total = 0;
                                        @endphp
                                        @foreach ($items as $key => $item)
                                        @php
                                            $total += $item->amount;
                                        @endphp
                                        <tr>
                                            <td></td>
                                            <td>{{ $item->category->name }}</td>
                                            <td class="text-center">{{ number_format($item->amount,0) }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td></td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="bg-warning text-center">&#8358;{{  number_format($total,0) }}</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>
                            {{ $dates->links() }}
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
                    <h4 class="modal-title" id="myModalLabel">Record New Expense</h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('expense.store') }}" method="POST">
                    @csrf
                <div class="modal-body">
                
                    <div class="add_item">

                        <div class="row">
                         
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="date">Date</label>
                                    <input class="form-control form-control-sm" type="date" name="date" required>
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                    <select class="form-select form-select-sm stock" name="expense_category_id[]" required>
                                        <option value=""></option>
                                        @foreach ($expense_cats as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                    <input class="form-control form-control-sm" type="number" name="amount[]" placeholder="Amount" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <input class="form-control form-control-sm" type="text" name="description[]" placeholder="Description">
                            </div>
                            <div class="col-md-2 mb-2">
                                <select class="form-select form-select-sm" name="payment_method[]" required>
                                    <option value=""></option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="pos">POS</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <span class="btn btn-success btn-sm addeventmore"><i
                                        class="fa fa-plus-circle"></i></span>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary ml-2">Save Expense</button>
                </div>
            </form>
            </div>
        </div>
    </div>


    {{-- invisible --}}
    <div style="visibility: hidden;">
        <div class="whole_extra_item_add" id="whole_extra_item_add">
            <div class="delete_whole_extra_item_add" id="delete_whole_extra_item_add">

                <div class="row">
                    <div class="col-md-3 mb-2">
                        <select class="form-select form-select-sm stock" name="expense_category_id[]" required>
                            <option value=""></option>
                            @foreach ($expense_cats as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                            <input class="form-control form-control-sm" type="number" name="amount[]" placeholder="Amount" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <input class="form-control form-control-sm" type="text" name="description[]" placeholder="Description">
                    </div>
                    <div class="col-md-2 mb-2">
                        <select class="form-select form-select-sm" name="payment_method[]" required>
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                            <option value="pos">POS</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <span class="btn btn-success btn-sm addeventmore"><i class="fa fa-plus-circle"></i></span>
                        <span class="btn btn-danger btn-sm removeeventmore mx-1"><i class="fa fa-minus-circle"></i></span>
                    </div>
                </div>

            </div>
        </div>
    </div>
   

@endsection

@section('js')

<script type="text/javascript">
    $(document).ready(function() {
        var counter = 0;
        $(document).on("click", ".addeventmore", function() {
            var whole_extra_item_add = $("#whole_extra_item_add").html();
            $(this).closest(".add_item").append(whole_extra_item_add);
        });
        $(document).on("click", ".removeeventmore", function(event) {
            $(this).closest(".delete_whole_extra_item_add").remove();
        });
    });
    </script>
@endsection
