@extends('admin.includes.master')
@section('head-area')
<link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>
@endsection
@section('content')

<form action="{{ route('admin.master.country') }}" method="post">
    @csrf
    <div class="card p-3">
        <h4 class="mb-4">Add Country</h4>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="name" class="form-label">Country Name</label>
                <input type="text" class="form-control" name="name" id="examplename" aria-describedby="emailHelp">
            </div>
            <div class="col-md-4">
                <label for="Code" class="form-label">Country Code</label>
                <input type="text" class="form-control" name="code" id="exampleCode">
            </div>
            <div class="col-md-4">
                <label for="sname" class="form-label">Short Name</label>
                <input type="text" class="form-control" name="sname" id="examplesname">
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>





    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-body">
                        <div id="example-table">

                        </div>
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
    ajaxURL: "{{ route('admin.master.country') }}", // URL for the Laravel controller
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
