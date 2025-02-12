@extends('admin.includes.master')
@section('head-area')
<link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>

@endsection
@section('content')
<div class="card">
    <div class="card-header">
        Fare Setup
    </div>
    <div class="card-body">
        <form action="{{ route('admin.master.fare-setup.store') }}" method="post">
            @csrf
            <div class="row mt-3">
                <div class="col-3">
                    <x-input-box type="number" name="min_km" required='true' label="Distance From (in km)"/>
                </div>
                <div class="col-3">
                    <x-input-box type="number" name="max_km" required='true' label="Distance to (in km)"/>
                </div>
                <div class="col-3">
                    <x-input-box type="number" name="base_fare" required='true' label="Base Fare (in ₹)"/>
                </div>
                <div class="col-3">
                    <x-input-box type="number" name="per_km_rate" required='true' label="Price/ km Fare (in ₹)"/>
                </div>
                <div class="col-3 mt-3">
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">

    </div>
    <div class="card-body">
        <div id="example-table-theme" class="table-bordered">

        </div>
    </div>
</div>
@endsection

@section('script-area')

<script>
    $(document).ready(function(){
   var table = new Tabulator("#example-table-theme", {
    layout: "fitColumns",
    theme: "bootstrap4",
    ajaxURL: "{{ route('admin.master.fare-setup.index') }}", 
    ajaxConfig: "GET", 
    pagination: "remote", 
    paginationSize: 10, 
    paginationSizeSelector: [10, 25, 50, 100], 
    ajaxResponse: function (url, params, response) {
        this.setColumns(response.columns);
        return response.data; 
    },

});
});
</script>
@endsection
