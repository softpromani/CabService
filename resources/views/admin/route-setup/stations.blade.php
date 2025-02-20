@extends('admin.includes.master')
@section('head-area')
    <link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary mb-5" data-bs-toggle="modal" id="addStationBtn" data-bs-target="#stationModal" >
            + Stations
        </button>
        <div class="row">


            <!-- Modal -->
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
        <!-- Add/Edit Modal -->
        <div class="modal fade" id="stationModal" tabindex="-1" aria-labelledby="stationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form id="stationForm" method="POST">
                        @csrf
                        <input type="hidden" id="station_id" name="station_id">
                        <input type="hidden" name="_method" id="_method" value="POST">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="stationModalLabel">Add Station</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-4 mt-2">
                                    <x-select-box name="city_id" id="city_id" label="City" :options="$cities" required />
                                </div>
                                <div class="col-4 mt-2">
                                    <x-input-box name="point_name" id="point_name" required />
                                </div>
                                <div class="col-4 mt-2">
                                    <x-input-box type="datetime-local" name="scheduled_time" id="scheduled_time" required />
                                </div>
                                <div class="col-12">
                                    <label>Search Location:</label>
                                    <input id="searchInput" type="text" class="form-control"
                                        placeholder="Enter a location">
                                    <div id="map"></div>
                                </div>
                                <div class="col-6">
                                    <label>Latitude:</label>
                                    <input type="text" id="latitude" name="latitude" class="form-control" readonly>
                                </div>
                                <div class="col-6">
                                    <label>longitute:</label>
                                    <input type="text" id="longitute" name="longitute" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="modalSubmitButton">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script-area')

    <!-- Google Maps Script -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhXmXSE2JxpvyCwPct8nfZK2yJYH605kk&libraries=places">
    </script>
    <script>
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 26.8467,
                    lng: 80.9462
                }, // Default location (Lucknow)
                zoom: 13
            });

            var marker = new google.maps.Marker({
                position: {
                    lat: 26.8467,
                    lng: 80.9462
                },
                map: map,
                draggable: true
            });

            var searchBox = new google.maps.places.SearchBox(document.getElementById('searchInput'));
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(document.getElementById('searchInput'));

            google.maps.event.addListener(searchBox, 'places_changed', function() {
                var places = searchBox.getPlaces();
                if (places.length == 0) return;

                var place = places[0];
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);

                document.getElementById('latitude').value = place.geometry.location.lat();
                document.getElementById('longitute').value = place.geometry.location.lng();
            });

            google.maps.event.addListener(marker, 'dragend', function() {
                var latLng = marker.getPosition();
                document.getElementById('latitude').value = latLng.lat();
                document.getElementById('longitute').value = latLng.lng();
            });
        }

        google.maps.event.addDomListener(window, 'load', initMap);

        $(document).ready(function() {
            var routeId = "{{ $route->id }}";
            var sendUrl = "{{ route('admin.master.route-setup.stations', ':id') }}".replace(':id', routeId);

            var table = new Tabulator("#example-table", {
                layout: "fitColumns",
                ajaxURL: sendUrl,
                ajaxConfig: "GET",
                pagination: "remote",
                paginationSize: 10,
                paginationSizeSelector: [10, 25, 50, 100],
                columns: [],
                ajaxResponse: function(url, params, response) {
                    this.setColumns(response.columns); // Columns set karein
                    return response.data; // Data load karein
                }
            });


            var routeId = "{{ $route->id }}";
            var addUrl = "{{ route('admin.master.route-setup.station-store', ':id') }}".replace(':id', routeId);

            // Add Station Button Click
            $(document).on("click", "#addStationBtn", function() {
                $("#stationModalLabel").text("Add Station");
                $("#stationForm").attr("action", addUrl).attr("method", "POST");
                $("#modalSubmitButton").text("Submit");

                // Reset all input fields
                $("#station_id").val('');
                $("#city_id").val('');
                $("#point_name").val('');
                $("#scheduled_time").val('');
                $("#latitude").val('');
                $("#longitute").val('');

                $("#stationModal").modal("show");
            });

            // Edit Station Button Click
            $ // Edit Station Button Click
            $(document).on("click", ".edit-station", function() {
                var stationId = $(this).data("id");
                var editUrl = "{{ route('admin.master.route-setup.stationUpdate', ':id') }}".replace(':id',
                    stationId);

                $("#stationModalLabel").text("Edit Station");
                $("#stationForm").attr("action", editUrl);
                $("#_method").val("PUT"); // Change method to PUT
                $("#modalSubmitButton").text("Update");

                // Set existing values
                $("#station_id").val(stationId);
                $("#city_id").val($(this).data("city_id"));
                $("#point_name").val($(this).data("point_name"));
                $("#scheduled_time").val($(this).data("scheduled_time"));
                $("#latitude").val($(this).data("latitude"));
                $("#longitute").val($(this).data("longitute"));

                $("#stationModal").modal("show");
            });

        });
    </script>
@endsection
