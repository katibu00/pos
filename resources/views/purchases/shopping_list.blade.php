@extends('layouts.app')
@section('PageTitle', 'Shopping List')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">

            <div class="card">
                <!-- Default panel contents -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col-2 d-none d-md-block"><span class="text-bold fs-16">Shopping List</span></div>
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
                </div>
                <div class="card-body">
                    <div class="table-data">
                        @include('purchases.shopping_list_table')
                    </div>
                </div>
            </div>

        </div>
    </div>
</section><!-- #content end -->
   
@endsection

@section('js')

<script type="text/javascript">

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
            url: '{{ route('fetch-shopping-list') }}',
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
