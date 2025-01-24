@extends('admin.includes.master')
@section('head-area')
<link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>

@endsection

@section('content')

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"></div>
                <div class="card-body p-0">
                    <div id="example-table" class="table-bordered">

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
    ajaxURL: "{{ route('admin.customer.index') }}", // URL for the Laravel controller
    ajaxConfig: "GET", // HTTP request type
    pagination: "remote", // Enable remote pagination
    paginationSize: 10, // Number of rows per page
    paginationSizeSelector: [10, 25, 50, 100], // Page size options
    ajaxResponse: function (url, params, response) {
        this.setColumns(response.columns);
        return response.data; // Return the response data for Tabulator to process
        console.log(response.data);
    },

});
});
</script>
@endsection
