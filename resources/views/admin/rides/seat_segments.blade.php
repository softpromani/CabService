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
                        <p><strong>Rides</strong></p>
                    </div>
                    <div class="card-body p-0">
                        <div id="example-table" class="table-bordered">

                        </div>
                    </div>
                </div>
                <!-- Car Details Modal -->
                <!-- Car Details Modal -->
                <div class="modal fade" id="carDetailsModal" tabindex="-1" aria-labelledby="carDetailsModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="carDetailsModalLabel">Car Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>Brand</th>
                                            <td id="car-brand"></td>
                                        </tr>
                                        <tr>
                                            <th>Model</th>
                                            <td id="car-model"></td>
                                        </tr>
                                        <tr>
                                            <th>Leather Interior</th>
                                            <td id="car-interior"></td>
                                        </tr>
                                        <tr>
                                            <th>Seats</th>
                                            <td id="car-seats"></td>
                                        </tr>
                                        <tr>
                                            <th>Registration Number</th>
                                            <td id="car-register-number"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
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
                ajaxURL: "{{ route('admin.rides.seat-segments', $ride->id) }}",
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
