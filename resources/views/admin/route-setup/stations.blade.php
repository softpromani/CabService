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
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
   + Stations
  </button>
  
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form action="{{ route('admin.master.route-setup.station-store',$route->id) }}" method="POST">
            @csrf
            <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Route <b>{{ $route->name }}</b></h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                        <div class="row">
                            <div class="col-4 mt-2">
                                <x-select-box name="city_id" label="City" :options="$cities"  required/>
                            </div>
                            <div class="col-4 mt-2">
                                <x-input-box name="point_name" required="" />
                            </div>
                            <div class="col-4 mt-2">
                                <x-input-box type="datetime-local" name="scheduled_time" required/>
                            </div>
                            <div class="col-12">
                                <label>Search Location:</label>
                                <input id="searchInput" type="text" class="form-control" placeholder="Enter a location">
                                <div id="map"></div>
                            </div>
                            <div class="col-6">
                                <label>Latitude:</label>
                                <input type="text" id="latitude" name="latitude" class="form-control" readonly>
                            </div>
                            <div class="col-6">
                                <label>Longitude:</label>
                                <input type="text" id="longitude" name="longitute" class="form-control" readonly>
                            </div>
                        </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
      </div>
    </div>
  </div>
@endsection
@section('script-area')
 <!-- Google Maps Script -->
 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhXmXSE2JxpvyCwPct8nfZK2yJYH605kk&libraries=places"></script>
 <script>
     function initMap() {
         var map = new google.maps.Map(document.getElementById('map'), {
             center: { lat: 26.8467, lng: 80.9462 }, // Default location (Lucknow)
             zoom: 13
         });

         var marker = new google.maps.Marker({
             position: { lat: 26.8467, lng: 80.9462 },
             map: map,
             draggable: true
         });

         var searchBox = new google.maps.places.SearchBox(document.getElementById('searchInput'));
         map.controls[google.maps.ControlPosition.TOP_LEFT].push(document.getElementById('searchInput'));

         google.maps.event.addListener(searchBox, 'places_changed', function () {
             var places = searchBox.getPlaces();
             if (places.length == 0) return;

             var place = places[0];
             map.setCenter(place.geometry.location);
             marker.setPosition(place.geometry.location);

             document.getElementById('latitude').value = place.geometry.location.lat();
             document.getElementById('longitude').value = place.geometry.location.lng();
         });

         google.maps.event.addListener(marker, 'dragend', function () {
             var latLng = marker.getPosition();
             document.getElementById('latitude').value = latLng.lat();
             document.getElementById('longitude').value = latLng.lng();
         });
     }

     google.maps.event.addDomListener(window, 'load', initMap);
 </script>
@endsection