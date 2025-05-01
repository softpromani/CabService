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
                    <div class="card-header">
                        <p><strong>Stations</strong></p>
                    </div>
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
        $(document).ready(function() {
            var table = new Tabulator("#example-table", {
                ajaxURL: "{{ route('admin.rides.stations', $ride->id) }}",
                ajaxConfig: "GET",
                pagination: "remote",
                paginationSize: 10,
                paginationSizeSelector: [10, 25, 50, 100],
                ajaxResponse: function(url, params, response) {
                    this.setColumns(response.columns);
                    return response.data;
                },
            });

            
        });
    </script>
@endsection
