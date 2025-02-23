@extends('admin.includes.master')
@section('head-area')
    <link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            Add New Route
        </div>
        <div class="card-body">
            <form action="{{ isset($editRoute) ? route('admin.master.route-setup.update', $editRoute->id) : route('admin.master.route-setup.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($editRoute))
                    @method('put')
                @endif
                <div class="row">
                    <div class="col-4 mt-3">
                        <x-input-box name="name" required="true" value="{{ isset($editRoute) ? $editRoute->name : '' }}" />
                    </div>
                    <div class="col-4 mt-3">
                        <x-input-box type="number" name="distance" required="true" value="{{ isset($editRoute) ? $editRoute->distance : '' }}" />
                    </div>
                    <div class="col-4 mt-3">
                        <x-input-box type="file" name="image" required="true" />
                    </div>
                    <div class="col-4 mt-3">
                        <button type="submit" class="btn btn-primary mt-4">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">Route List</div>
        <div class="card-body p-0">
            <div id="route-table" class="table-bordered">

            </div>
        </div>
    </div>
@endsection
@section('script-area')
<script>
    $(document).ready(function () {
        var table = new Tabulator("#route-table", {
            layout: "fitColumns",
            theme: "bootstrap4",
            ajaxURL: "{{ route('admin.master.route-setup.index') }}",
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
