@extends('layouts.app')
@section('PageTitle', 'Purchases')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">

            <div class="card">
                <!-- Default panel contents -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col-2 d-none d-md-block"><span class="text-bold fs-16">Purchases</span></div>
                    <div class="col-sm-5 col-md-3">
                        <select class="form-select form-select-sm" id="branch_id">
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3 col-md-5 d-none d-md-block">
                       
                    </div>
                    <div class="col-sm-4 col-md-2"><a class="btn btn-sm btn-primary me-2" href="{{ route('purchase.create') }}">+ New Purchases</a></div>
                </div>
                <div class="card-body">
                    <div class="table-data">
                        @include('purchases.table')
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
                    <h4 class="modal-title" id="myModalLabel">Add New Purchases</h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('purchase.store') }}" method="POST">
                    @csrf
                <div class="modal-body">
                
                    <div class="add_item">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="branch">Branch</label>
                                <select class="form-select form-select-sm" name="branch" id="branch" required>
                                    <option value="">Select Branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label for="picker1">Date</label>
                                    <input class="form-control form-control-sm" type="date" name="date" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-5 mb-2">
                                    <select class="form-select form-select-sm stock" name="product_id[]" required></select>
                            </div>

                            <div class="col-md-2 mb-2">
                                    <input class="form-control form-control-sm" type="number" name="quantity[]" placeholder="Quantity" required>
                            </div>
                            <div class="col-md-2 mb-2">
                                <input class="form-control form-control-sm" type="number" name="buying_price[]" placeholder="Buying Price">
                            </div>
                            <div class="col-md-2 mb-2">
                                    <input class="form-control form-control-sm" type="number" name="selling_price[]" placeholder="Selling Price">
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
                    <button type="submit" class="btn btn-primary ml-2">Save Purchases</button>
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
                    <div class="col-md-5 mb-2">
                            <select class="form-select form-select-sm stock" name="product_id[]" required>
                                <option value="">--</option>
                            </select>
                    </div>

                    <div class="col-md-2 mb-2">
                            <input class="form-control form-control-sm" type="number" name="quantity[]" placeholder="Quantity" required>
                    </div>
                    <div class="col-md-2 mb-2">
                            <input class="form-control form-control-sm" type="number" name="buying_price[]" placeholder="Buying Price">
                    </div>
                    <div class="col-md-2 mb-2">
                            <input class="form-control form-control-sm" type="number" name="selling_price[]" placeholder="Selling Price">
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


    //fetch branch stock
    $(document).on('change', '#branch', function() {

        var branch_id = $('#branch').val();


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: '{{ route('fetch-branch-stocks') }}',
            data: {
                'branch_id': branch_id
            },
            success: function(response) {
                var html = '<option value="">--Select--</option>';
                $.each(response.stocks, function(key, v) {
                    html += '<option value="' + v.id + '">' + v.name +' - N'+v.buying_price +'</option>';
                });
                html = $('.stock').html(html);
            }
        });
    });


    $(document).on('change', '#branch_id', function() {


        var branch_id = $('#branch_id').val();
        $.LoadingOverlay("show")
        $('.table').addClass('d-none');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: '{{ route('fetch-purchases') }}',
            data: {
                'branch_id': branch_id
            },
            success: function(response) {

                $('.table-data').html(response);
                $('.table').removeClass('d-none');
                $.LoadingOverlay("hide")

            }
        });
    });

    $(document).on('click', '.pagination a', function(e){
      e.preventDefault();
        
      let page = $(this).attr('href').split('page=')[1]
      fetchData(page)
        
      });

      function fetchData(page){
      
        var branch_id = $('#branch_id').val();
      
        $.ajax({
            url: "/paginate-purchases?branch_id="+branch_id+"&page="+page,
            success: function(response){
              
                $('.table-data').html(response);
                $('.table').removeClass('d-none');
            
            }
        });
      }
</script>
@endsection
