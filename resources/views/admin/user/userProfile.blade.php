@extends('admin.includes.master')

@section('head-area')
    <link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
@endsection

@section('content')
    <section class="section profile">
        <div class="row">
            <div class="col-xl-4">

                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                        <img src="{{ isset($user->user_image) ? asset('storage/' . $user->user_image) : asset('assets/admin/img/default-profile.jpg') }}"
                            alt="Profile" class="rounded-circle">
                        <h2>
                            @isset($user)
                                {{ $user->full_name }}
                            @endisset
                        </h2>
                        <h3> @isset($user)
                                {{ $user->role_name }}
                            @endisset
                        </h3>
                        <div class="social-links mt-2">
                            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-xl-8">

                <div class="card">
                    <div class="card-body pt-3">
                        <ul class="nav nav-tabs nav-tabs-bordered">

                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#profile-overview">Overview</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit
                                    Profile</button>
                            </li>


                        </ul>
                        <div class="tab-content pt-2">

                            <div class="tab-pane fade show active profile-overview" id="profile-overview">

                                <h5 class="card-title">Profile Details</h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label ">Full Name</div>
                                    <div class="col-lg-9 col-md-8">{{ $user->first_name }}
                                        {{ $user->last_name }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Country</div>
                                    <div class="col-lg-9 col-md-8">{{ $user->country->name ?? 'India' }}</div>
                                </div>

                                @isset($user->address)
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Address</div>
                                        <div class="col-lg-9 col-md-8">
                                            {{ $user->address ?? '' }}
                                            {{ $user->city ? ', ' . $user->city->city_name : '' }}
                                            {{ $user->state ? ', ' . $user->state->state_name : '' }}
                                           
                                        </div>
                                    </div>
                                @endisset
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Phone</div>
                                    <div class="col-lg-9 col-md-8">{{ $user->phone }}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Email</div>
                                    <div class="col-lg-9 col-md-8">{{ $user->email }}</div>
                                </div>
                                


                            </div>

                            <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                                <form action="{{ route('admin.updateUserProfile', $user->id) }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @if (isset($user))
                                        @method('put')
                                    @endif
                                    <div class="row mb-3">
                                        <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile
                                            Image</label>
                                        <div class="col-md-8 col-lg-9">
                                            <img id="previewImage"
                                                src="{{ isset($user) ? asset('storage/' . $user->user_image) : asset('assets/admin/img/messages-3.jpg') }}"
                                                alt="Profile Image">

                                            <div class="pt-2">
                                                <label for="imageUpload" class="btn btn-primary btn-sm"
                                                    title="Upload new profile image ">
                                                    <i class="bi bi-upload text-light"></i>
                                                </label>
                                                <input type="file" id="imageUpload" name="user_image"
                                                    style="display: none;" accept="image/*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="first_name" class="col-md-4 col-lg-3 col-form-label">First Name</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input type="text" name="first_name" class="form-control" id="first_name"
                                                value="{{ old('first_name', $user->first_name) }}" />
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="last_name" class="col-md-4 col-lg-3 col-form-label">Last Name</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input type="text" name="last_name" class="form-control" id="last_name"
                                                value="{{ old('last_name', $user->last_name) }}" />
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="Address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="address" type="text" class="form-control" id="Address"
                                                value="{{ $user->address }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="phone" type="text" class="form-control" id="Phone"
                                                value="{{ $user->phone }}">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="email" type="email" class="form-control" id="Email"
                                                value="{{ $user->email }}">
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label for="country_id" class="col-md-4 col-lg-3 col-form-label">Country</label>
                                        <div class="col-md-8 col-lg-9">
                                            <select name="country_id" id="country_id" class="form-control">
                                                <option value="" selected disabled> Select Country</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}"
                                                        {{ isset($user) && $user->country_id == $country->id ? 'selected' : '' }}>
                                                        {{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="state_id" class="col-md-4 col-lg-3 col-form-label">State</label>
                                        <div class="col-md-8 col-lg-9">
                                            <select name="state_id" id="state_id" class="form-control">
                                                <option value="" selected disabled> Select State</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="city_id" class="col-md-4 col-lg-3 col-form-label">City</label>
                                        <div class="col-md-8 col-lg-9">
                                            <select name="city_id" id="city_id" class="form-control">
                                                <option value="" selected disabled> Select City</option>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="gender" class="col-md-4 col-lg-3 col-form-label">Gender</label>
                                        <div class="col-md-8 col-lg-9">
                                            <select name="gender" id="gender" class="form-control">
                                                <option value="" selected disabled> Gender</option>
                                                <option value="female"
                                                    {{ isset($user) && $user->gender == 'female' ? 'selected' : '' }}>
                                                    Female</option>
                                                <option value="male"
                                                    {{ isset($user) && $user->gender == 'male' ? 'selected' : '' }}>
                                                    Male</option>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="dob" class="col-md-4 col-lg-3 col-form-label">Password</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="password" type="password" class="form-control" id="password">
                                        </div>
                                    </div>

                                    {{-- <div class="row mb-3">
                                        <label for="Twitter" class="col-md-4 col-lg-3 col-form-label">Twitter
                                            Profile</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="twitter" type="text" class="form-control" id="Twitter"
                                                value="https://twitter.com/#">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="Facebook" class="col-md-4 col-lg-3 col-form-label">Facebook
                                            Profile</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="facebook" type="text" class="form-control" id="Facebook"
                                                value="https://facebook.com/#">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="Instagram" class="col-md-4 col-lg-3 col-form-label">Instagram
                                            Profile</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="instagram" type="text" class="form-control" id="Instagram"
                                                value="https://instagram.com/#">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="Linkedin" class="col-md-4 col-lg-3 col-form-label">Linkedin
                                            Profile</label>
                                        <div class="col-md-8 col-lg-9">
                                            <input name="linkedin" type="text" class="form-control" id="Linkedin"
                                                value="https://linkedin.com/#">
                                        </div>
                                    </div> --}}

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>

                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
    <script>
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
                                    `<option value="${state.id}" ${state.id == "{{ isset($user) ? $user->state_id : '' }}" ? 'selected' : ''}>${state.state_name}</option>`
                                );
                            });

                            if ('{{ isset($user) }}') {
                                var selectedState =
                                    "{{ isset($user) ? $user->state_id : '' }}";
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
                                    `<option value="${city.id}" ${city.id == "{{ isset($user) ? $user->city_id : '' }}" ? 'selected' : ''}>${city.city_name}</option>`
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
            if ('{{ isset($user) }}') {
                var selectedCountry = "{{ isset($user) ? $user->country_id : '' }}";
                var selectedState = "{{ isset($user) ? $user->state_id : '' }}";
                var selectedCity = "{{ isset($user) ? $user->city_id : '' }}";

                $('#country_id').val(selectedCountry).change();

                $('#state_id').val(selectedState).change();
            }

            $('#imageUpload').change(function(e) {
                if (e.target.files && e.target.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImage').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(e.target.files[0]);
                }
            });
        });
    </script>
@endsection
