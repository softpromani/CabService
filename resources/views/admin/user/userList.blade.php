@extends('admin.includes.master')
@section('head-area')
    <link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>
@endsection
@section('content')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">User List</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">User</h5>
                            <a href="{{ route('admin.addUser') }}" class="btn btn-primary">Add User</a>
                        </div>
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>Sr No.</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><img width="70px" height="70px"
                                            src="{{ asset('storage/' . $user->user_image) }}"
                                            class="rounded-circle m-r-5" alt="User Image"></td>
                                        <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td> {{ $user->roles[0]->name ?? '' }}</td>
                                        <td style="white-space: nowrap;">

                                            <a href="{{ route('admin.editUser' , $user->id) }}" class="btn bg-success-light" style="margin-right: 5px;">
                                                Edit
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div id="example-table"></div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
@section('script-area')
<script>
    $(document).ready(function(){
   var table = new Tabulator("#example-table", {
    ajaxURL: "{{ route('admin.userList') }}", // URL for the Laravel controller
    ajaxConfig: "GET", // HTTP request type
    pagination: "remote", // Enable remote pagination
    paginationSize: 10, // Number of rows per page
    paginationSizeSelector: [10, 25, 50, 100], // Page size options
    ajaxResponse: function (url, params, response) {
        this.setColumns(response.columns);
        return response.data; // Return the response data for Tabulator to process
    },
    
});
});
</script>
@endsection
