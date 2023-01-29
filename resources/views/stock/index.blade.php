@extends('layouts.app')
@section('PageTitle', 'Inventories')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">

            <div class="card">
                <!-- Default panel contents -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col-2 d-none d-md-block"><span class="text-bold fs-16">Inventory</span></div>
                    <div class="col-sm-5 col-md-3">
                        <select class="form-select form-select-sm" id="branch">
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-5 col-md-3">
                        <input type="text" class="form-control form-control-sm" id="search" placeholder="search...">
                    </div>
                    <div class="col-2 d-none d-md-block"><button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target=".addModal">Add New</button></div>
                </div>
                <div class="card-body">
                   
                </div>

                <!-- Table -->
               
                <div class="table-data">
                    @include('stock.table')
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
                    <h4 class="modal-title" id="myModalLabel">Add New Inventories</h4>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ route('stock.store') }}" method="POST">
                    @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3 form-group mb-3">
                            <label for="branch_id">Branch</label>
                            <select class="form-select form-select-sm" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="add_item">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-1">
                                    <input class="form-control form-control-sm" type="text" name="name[]"
                                        placeholder="Product Title" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-1">
                                    <input class="form-control form-control-sm" type="number" name="buying_price[]"
                                        placeholder="Cost Price" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-1">
                                    <input class="form-control form-control-sm" type="number" name="selling_price[]"
                                        placeholder="Retail Price" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-1">
                                    <input class="form-control form-control-sm" type="number" name="quantity[]"
                                        placeholder="Quantity" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-1">
                                    <input class="form-control form-control-sm" type="number" name="critical_level[]"
                                        placeholder="Critical Level" required>
                                </div>
                            </div>

                            <div class="col-md-2 my-1">
                                <span class="btn btn-success btn-sm addeventmore"><i
                                        class="fa fa-plus-circle"></i></span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary ml-2">Add Inventory</button>
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
                    <div class="col-md-3">
                        <div class="mb-1">
                            <input class="form-control form-control-sm" type="text" name="name[]"
                                placeholder="Product Title" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-1">
                            <input class="form-control form-control-sm" type="number" name="buying_price[]"
                                placeholder="Cost Price" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-1">
                            <input class="form-control form-control-sm" type="number" name="selling_price[]"
                                placeholder="Retail Price" required>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="mb-1">
                            <input class="form-control form-control-sm" type="number" name="quantity[]" placeholder="Quantity"
                                required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-1">
                            <input class="form-control form-control-sm" type="number" name="critical_level[]"
                                placeholder="Critical Level" required>
                        </div>
                    </div>
                    <div class="col-md-3 my-1">
                        <span class="btn btn-success btn-sm addeventmore"><i class="fa fa-plus-circle"></i></span>
                        <span class="btn btn-danger btn-sm removeeventmore mx-1"><i
                                class="fa fa-minus-circle"></i></span>
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
                //  alert('cliked');
                var whole_extra_item_add = $("#whole_extra_item_add").html();
                $(this).closest(".add_item").append(whole_extra_item_add);
                counter++
            });
            $(document).on("click", ".removeeventmore", function(event) {
                $(this).closest(".delete_whole_extra_item_add").remove();
                counter -= 1;
            });
        });
    </script>

    <script type="text/javascript">
        $(document).on('change', '#branch', function() {


            var branch_id = $('#branch').val();
            // $('.loader').removeClass('d-none')
            $('.table').addClass('d-none');
            $.LoadingOverlay("show")

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '{{ route('fetch-stocks') }}',
                data: {
                    'branch_id': branch_id
                },
                success: function(response) {


                    $('.table-data').html(response);
                    $('.table').removeClass('d-none');
                    // $('.loader').addClass('d-none')
                    $.LoadingOverlay("hide")


                }
            });
        });


        //pagination
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();

            let page = $(this).attr('href').split('page=')[1]
            fetchData(page)

        });

        function fetchData(page) {

            var branch_id = $('#branch').val();

            $.ajax({
                url: "/paginate-stocks?branch_id=" + branch_id + "&page=" + page,
                success: function(response) {

                    $('.table-data').html(response);
                    $('.table').removeClass('d-none');

                }
            });
        }
    </script>

    <script>
        //search
        $(document).on('keyup', function(e) {
            e.preventDefault();

            let query = $('#search').val();
            let branch_id = $('#branch').val();

            if (branch_id == '') {
                alert('please choose a branch');
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('search-stocks') }}",
                method: 'POST',
                data: {
                    'query': query,
                    'branch_id': branch_id
                },

                success: function(response) {
                    $('.table-data').html(response);
                    $('.table').removeClass('d-none');

                    if (response.status == 404) {
                        $('.table-data').html(
                            '<p class="text-danger text-center">No Data Matched the Query</p>');
                    }
                }
            });

        });
    </script>


    <script>
        $(document).on('click', '.delete', function(e) {
            e.preventDefault();

            let id = $(this).data('id');

            swal({
                    title: "Delete Product?",
                    text: "Once deleted, you will not be able to recover it!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {

                        var data = {
                            'id': id,
                        }

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: "POST",
                            url: "{{ route('stock.delete') }}",
                            data: data,
                            dataType: "json",
                            success: function(response) {

                                $('.table').load(location.href+' .table');
                                

                            }
                        });

                    }
                });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@endsection