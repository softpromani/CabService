@extends('admin.includes.master')
@section('title', 'Business Pages')
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4 fw-bold"><span class="text-muted fw-light">Business Setup /</span> Third Party API</h4>



        <!-- Navigation -->
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item">
                                <a href="{{ route('admin.setting.thirdPartyApi','mail_config') }}" class="nav-link {{ $page == 'mail_config' ? 'active' : '' }}" >
                                    Mail Config
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.setting.thirdPartyApi','sms_config') }}" class="nav-link {{ $page == 'sms_config' ? 'active' : '' }}" >
                                    SMS Config
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.setting.thirdPartyApi','google-map') }}" class="nav-link {{ $page == 'google-map' ? 'active' : '' }}" >
                                    Google Map
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0">
                            <div class="tab-pane fade {{ $page == 'mail_config' ? 'show active' : '' }}" id="mail-config" role="tabpanel">
                                <div class="card-body">
                                    <form action="{{ route('admin.setting.thirdPartyApiPost') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="mail_config" />
                                        <div class="row">
                                            <div class="mb-3 col-6">
                                                <x-input-box name="mailer_name" value="{{ $data->mailer_name??'' }}" required />
                                            </div>
                                            <div class="mb-3 col-6">
                                                <x-input-box name="host" value="{{ $data->host??'' }}" required/>
                                            </div>
                                            <div class="mb-3 col-6">
                                                <x-input-box name="driver" value="{{ $data->driver??'' }}" required />
                                            </div>
                                            <div class="mb-3 col-6">
                                                <x-input-box name="port"  value="{{ $data->port??'' }}" required />
                                            </div>
                                            <div class="mb-3 col-6">
                                                <x-input-box name="username" value="{{ $data->username??'' }}" required />
                                            </div>
                                            <div class="mb-3 col-6">
                                                <x-input-box name="email" value="{{ $data->email??'' }}" required/>
                                            </div>
                                            <div class="mb-3 col-6">
                                                <x-select-box name="encryption" value="{{ $data->encryption??'' }}" :options="['ssl'=>'SSL','tls'=>'TLS']" required />
                                            </div>
                                            <div class="mb-3 col-6">
                                                <x-input-box type="password" name="password" value="{{ $data->password??'' }}" required/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-3">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                
                            <div class="tab-pane fade {{ $page == 'sms_config' ? 'show active' : '' }}" id="sms-config" role="tabpanel">
                                <div class="card-body">
                                    <h5>SMS Configuration</h5>
                                    <p>SMS config content goes here...</p>
                                </div>
                            </div>
                
                            <div class="tab-pane fade {{ $page == 'google-map' ? 'show active' : '' }}" id="google-map" role="tabpanel">
                                <div class="card-body">
                                    <form action="{{ route('admin.setting.thirdPartyApiPost') }}" method="POST" id="google-api-form">
                                        @csrf
                                        <input type="hidden" name="type" value="google-map" />
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <x-input-box name="google_api_key"  value="{{$data?->google_api_key??''}}" required />
                                            </div>
                                        <div class="col-12 mt-3">
                                            <div class="mb-3">
                                                <button type="button" class="btn btn-secondary" id="validate-api-key">Validate API Key</button>
                                                <button type="submit" class="btn btn-primary">Save API Key</button>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    </form>
                                    <div id="api-key-validation-result" class="mt-2"></div>
                                </div>  
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <!--/ Navigation -->



    </div>
@endsection
@section('script-area')
    <script src="{{ asset('assets/js/forms-editors.js') }}"></script>

    {{-- validate google api key --}}
    <script>
      document.getElementById('validate-api-key').addEventListener('click', async function () {
    const apiKey = document.getElementById('google_api_key').value;
    const resultDiv = document.getElementById('api-key-validation-result');
    resultDiv.innerHTML = '🔄 Validating API key...';

    if (!apiKey) {
        resultDiv.innerHTML = '<div class="text-danger">❌ Please enter an API key to validate.</div>';
        return;
    }

    // List of APIs to check
    const apiTests = {
        "Maps JavaScript API": `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initDummy`,
        "Places API": `https://maps.googleapis.com/maps/api/place/textsearch/json?query=Delhi&key=${apiKey}`,
        "Geocoding API": `https://maps.googleapis.com/maps/api/geocode/json?address=Delhi&key=${apiKey}`,
        "Distance Matrix API": `https://maps.googleapis.com/maps/api/distancematrix/json?origins=Delhi&destinations=Mumbai&key=${apiKey}`,
        "Elevation API": `https://maps.googleapis.com/maps/api/elevation/json?locations=28.7041,77.1025&key=${apiKey}`,
        "Directions API": `https://maps.googleapis.com/maps/api/directions/json?origin=Delhi&destination=Mumbai&key=${apiKey}`,
        "Time Zone API": `https://maps.googleapis.com/maps/api/timezone/json?location=28.7041,77.1025&timestamp=1672531200&key=${apiKey}`,
        "Roads API": `https://roads.googleapis.com/v1/nearestRoads?points=28.7041,77.1025&key=${apiKey}`
    };

    let results = [];

    // Function to test each API endpoint
    for (const [apiName, url] of Object.entries(apiTests)) {
        try {
            const response = await fetch(url);
            
            // For Maps JS API, check if the request succeeded, not JSON parsing
            if (apiName === "Maps JavaScript API" && response.ok) {
                results.push(`<div class="text-success">✅ ${apiName} is enabled and working.</div>`);
                continue;
            }

            const data = await response.json();

            if (!data.error_message) {
                results.push(`<div class="text-success">✅ ${apiName} is enabled and working.</div>`);
            } else {
                results.push(`<div class="text-warning">⚠️ ${apiName} is restricted: ${data.error_message}</div>`);
            }
        } catch (error) {
            results.push(`<div class="text-danger">❌ ${apiName} check failed: ${error.message}</div>`);
        }
    }

    resultDiv.innerHTML = results.join('');
});


        </script>
        
@endsection
