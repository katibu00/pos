@extends('layouts.app')
@section('PageTitle', 'Home')
@section('content')
<section id="content">
    <div class="content-wrap">
        <div class="container">

            <div class="card">
                <!-- Default panel contents -->
                <div class="card-header d-flex">
                    <div class="col-2">Inventory</div>
                    <div class="col-2">

                    </div>
                    <div class="col-2">
                        
                    </div>
                    <div class="col-2"><button>Add New</button></div>
                </div>
                <div class="card-body">
                   
                </div>

                <!-- Table -->
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Username</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Mark</td>
                            <td>Otto</td>
                            <td>@mdo</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Jacob</td>
                            <td>Thornton</td>
                            <td>@fat</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Larry</td>
                            <td>the Bird</td>
                            <td>@twitter</td>
                        </tr>
                    </tbody>
                </table>
            </div>





        </div>
    </div>
</section><!-- #content end -->
@endsection