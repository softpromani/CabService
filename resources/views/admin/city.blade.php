@extends('admin.includes.master')
@section('head-area')
<link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>
@endsection
@section('content')

<form action="{{ route('admin.master.city') }}" method="post">
    @csrf
    <div class="card p-3">
        <h4 class="mb-4">Add City</h4>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="id" class="form-label">State ID</label>
                <input type="text" class="form-control" name="id" id="exampleCode">
            </div>
            <div class="col-md-4">
                <label for="city_name" class="form-label">City Name</label>
                <input type="text" class="form-control" name="city_name" id="examplename" >
            </div>

            <div class="col-md-4">
                <label for="pin_code" class="form-label">Pin Code</label>
                <input type="text" class="form-control" name="pin_code" id="examplesname">
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
    ajaxURL: "{{ route('admin.master.city') }}", // URL for the Laravel controller
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