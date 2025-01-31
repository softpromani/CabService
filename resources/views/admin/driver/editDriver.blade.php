@extends('admin.includes.master')
@section('content')
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">User List</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ isset($editUser) ? route('admin.updateDriver', $editUser->id) : route('admin.storeUser') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($editUser))
                        @method('put')
                    @endif
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">User</h5>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control"
                                            value="{{ old('first_name', isset($editUser) ? $editUser->first_name : '') }}" />
                                        @error('first_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control"
                                            value="{{ old('last_name', isset($editUser) ? $editUser->last_name : '') }}" />
                                        @error('last_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="email">Email<span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', isset($editUser) ? $editUser->email : '') }}" />
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="phone">Phone <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ old('phone', isset($editUser) ? $editUser->phone : '') }}" />
                                        @error('phone')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="dob">D.O.B. <span class="text-danger">*</span></label>
                                        <input type="date" name="dob" class="form-control"
                                            value="{{ old('dob', isset($editUser) ? $editUser->dob : '') }}" />
                                        @error('dob')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-12 col-md-6 col-xl-6 mt-3">

                                    <div class="form-group local-forms">
                                        <label for="password">Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" />
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="roleid">Role:<span class="text-danger">*</span></label>
                                        <select name="roleid" id="roleid" class="form-control">
                                            <option selected disabled> -- Select Role --</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ isset($currentRole) && $currentRole == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label>Gender:</label>
                                        <div>
                                            <div class="form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender" id="male"
                                                    value="male"
                                                    {{ isset($editUser) && $editUser->gender == 'male' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="male">
                                                    Male
                                                </label>
                                            </div>
                                            <div class="form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender" id="female"
                                                    value="female"
                                                    {{ isset($editUser) && $editUser->gender == 'female' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="female">
                                                    Female
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">

                                    <div class="form-group local-forms">
                                        <label for="country_id">Country <span class="text-danger">*</span></label>
                                        <select name="country_id" id="country_id" class="form-control">
                                            <option value="" selected disabled> Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ isset($editUser) && $editUser->country_id == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">

                                    <div class="form-group local-forms">
                                        <label for="state_id">State

                                        </label>
                                        <select name="state_id" id="state_id" class="form-control">
                                            <option value="" selected disabled> Select State</option>

                                        </select>
                                        @error('state_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="row">
                                        <div class="col-12 col-md-12 col-xl-12 mt-3">
                                            <div class="form-group local-forms">
                                                <label for="city_id">City </label>
                                                <select name="city_id" id="city_id" class="form-control">


                                                </select>
                                                @error('city_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-12 col-xl-12 mt-3">
                                            <div class="form-group local-forms">
                                                <label for="address">Address </label>
                                                <textarea name="address" id="address" class="form-control" cols="" rows="5">{{ old('address', isset($editUser) ? $editUser->address : '') }}</textarea>
                                                @error('address')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                </div>



                                <div class="col-12 col-md-6 col-xl-6 mt-4">
                                    <div class="form-group local-forms">
                                        <label for="user_image">Image <span class="text-danger">*</span></label>
                                        <input type="file" name="user_image" id="user_image"
                                            class="form-control mt-2" />

                                        @error('user_image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="imageShow mt-4">
                                        @if (isset($editUser))
                                            <img src="{{ asset('storage/' . $editUser->user_image) }}"
                                                class=" m-r-5 mb-2" alt="User Image"
                                                style="max-height: 161px; max-width:166px; border-radius:5px;">
                                        @endif
                                    </div>
                                </div>

                            </div>


                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-body">
                            <h4 class="card-title">User Documents</h4>

                            @php
                                $documentTypes = [
                                    ['label' => 'Driving Licence', 'name' => 'driving_licence', 'prefix' => 'dl'],
                                    ['label' => 'Aadhar Card', 'name' => 'adhar_card', 'prefix' => 'aadhar'],
                                    ['label' => 'PAN Card', 'name' => 'pan_card', 'prefix' => 'pan'],
                                ];
                            @endphp

                            @foreach ($documentTypes as $doc)
                                <div class="row mb-3">
                                    <b>{{ $doc['label'] }}</b>
                                    <input type="hidden" value="{{ $doc['name'] }}"
                                        name="{{ $doc['prefix'] }}_identity_name" />

                                    @foreach (['front', 'back'] as $side)
                                        <div class="col-12 col-md-6 col-xl-4 mt-3">
                                            <div class="form-group local-forms">
                                                <label
                                                    for="{{ $doc['prefix'] }}_{{ $side }}">{{ ucfirst($side) }}</label>
                                                <input type="file" name="{{ $doc['prefix'] }}_{{ $side }}"
                                                    id="{{ $doc['prefix'] }}_{{ $side }}"
                                                    class="form-control" />

                                                @php
                                                    
                                                    $filePath =
                                                        $documents->where('identity_type', $doc['name'])->first()
                                                            ->{$doc['prefix'] . '_document_' . $side} ?? null;
                                                @endphp

                                                @if ($filePath)
                                                    @if (pathinfo($filePath, PATHINFO_EXTENSION) == 'pdf')
                                                        <!-- Display PDF Link -->
                                                        <a href="{{ asset('storage/' . $filePath) }}" target="_blank"
                                                            class="btn btn-info mt-2">View PDF</a>
                                                    @else
                                                        <!-- Display Image -->
                                                        <img src="{{ asset('storage/' . $filePath) }}"
                                                            alt="{{ $doc['label'] }} {{ ucfirst($side) }}"
                                                            class="img-thumbnail mt-2" width="150">
                                                    @endif
                                                @endif

                                                @error($doc['prefix'] . '_' . $side)
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    @endforeach

                                    <!-- Input for Document Number -->
                                    <div class="col-12 col-md-6 col-xl-4 mt-3">
                                        <div class="form-group local-forms">
                                            <label for="{{ $doc['prefix'] }}_number">Number</label>
                                            <input type="text" name="{{ $doc['prefix'] }}_number"
                                                id="{{ $doc['prefix'] }}_number" class="form-control"
                                                value="{{ $documents->where('identity_type', $doc['name'])->first()->identity_number ?? '' }}" />

                                            @error($doc['prefix'] . '_number')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-md-12 mt-5 text-end">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </div>




                </form>
            </div>
        </div>
    </section>
    <script>
        $('#user_image').change(function() {
            console.log("File input changed");
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    console.log("File loaded", e.target.result);
                    var imgElement = $('<img>', {
                        src: e.target.result,
                        class: 'img-fluid , mb-4',
                        style: 'max-height: 161px; max-width:166px; border-radius:5px;'
                    });
                    $('.imageShow').html(imgElement);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
        $(document).ready(function() {
            $('#country_id').change(function() {
                var countryId = $(this).val();
                if (countryId) {
                    $.ajax({
                        url: "{{ route('admin.getStates', '') }}/" + countryId,
                        type: 'GET',
                        success: function(data) {
                            $('#state_id').html(
                                '<option value="" selected disabled>Select State</option>'
                            );

                            $('#state_id').prop('disabled', false);
                            $.each(data, function(key, state) {
                                $('#state_id').append(
                                    `<option value="${state.id}" ${state.id == "{{ isset($editUser) ? $editUser->state_id : '' }}" ? 'selected' : ''}>${state.state_name}</option>`
                                );
                            });

                            if ('{{ isset($editUser) }}') {
                                var selectedState =
                                    "{{ isset($editUser) ? $editUser->state_id : '' }}";
                                $('#state_id').val(selectedState).change();
                            }
                        },
                        error: function() {
                            alert('Error fetching states. Please try again.');
                        }
                    });
                } else {
                    $('#state_id').html('<option value="" selected disabled>Select State</option>');
                    $('#state_id').prop('disabled', true);
                    $('#city_id').html('<option value="" selected disabled>Select City</option>');
                    $('#city_id').prop('disabled', true);
                }
            });

            $('#state_id').change(function() {
                var stateId = $(this).val();
                if (stateId) {
                    $.ajax({
                        url: "{{ route('admin.getCities', '') }}/" + stateId,
                        type: 'GET',
                        success: function(data) {
                            $('#city_id').html(
                                '<option value="" selected disabled>Select City</option>');
                            $('#city_id').prop('disabled', false);
                            $.each(data, function(key, city) {
                                $('#city_id').append(
                                    `<option value="${city.id}" ${city.id == "{{ isset($editUser) ? $editUser->city_id : '' }}" ? 'selected' : ''}>${city.city_name}</option>`
                                );
                            });
                        },
                        error: function() {
                            alert('Error fetching cities. Please try again.');
                        }
                    });
                } else {
                    $('#city_id').html('<option value="" selected disabled>Select City</option>');
                    $('#city_id').prop('disabled', true);
                }
            });
            if ('{{ isset($editUser) }}') {
                var selectedCountry = "{{ isset($editUser) ? $editUser->country_id : '' }}";
                var selectedState = "{{ isset($editUser) ? $editUser->state_id : '' }}";
                var selectedCity = "{{ isset($editUser) ? $editUser->city_id : '' }}";

                $('#country_id').val(selectedCountry).change();

                $('#state_id').val(selectedState).change();
            }
        });
    </script>
@endsection
