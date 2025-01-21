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

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">User</h5>
                        <form action="{{  route('admin.storeUser') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($editUser))
                                @method('put')
                            @endif
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 col-xl-6">
                                    <div class="form-group local-forms">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control"
                                            value="{{ old('first_name', isset($editUser) ? $editUser->first_name : '') }}" />
                                        @error('first_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6">
                                    <div class="form-group local-forms">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control"
                                            value="{{ old('last_name', isset($editUser) ? $editUser->last_name : '') }}" />
                                        @error('last_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6">
                                    <div class="form-group local-forms">
                                        <label for="email">Email<span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', isset($editUser) ? $editUser->email : '') }}" />
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6">
                                    <div class="form-group local-forms">
                                        <label for="phone">Phone <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ old('phone', isset($editUser) ? $editUser->phone : '') }}" />
                                        @error('phone')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-12 col-md-6 col-xl-6">
                                    <div class="col-md-12">
                                        <div class="form-group local-forms">
                                            <label for="password">Password <span class="text-danger">*</span></label>
                                            <input type="password" name="password" class="form-control" />
                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
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
                                    <div class="col-md-12 mt-3">
                                        <div class="form-group local-forms">
                                            <label>Gender:</label>
                                            <div>
                                                <div class="form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        id="male" value="male" {{ isset($editUser) && $editUser->gender == 'male' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="male">
                                                        Male
                                                    </label>
                                                </div>
                                                <div class="form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        id="female" value="female" {{ isset($editUser) && $editUser->gender == 'female' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="female">
                                                        Female
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-12 col-md-6 col-xl-6">
                                    <div class="form-group local-forms">
                                        <label for="user_image">Image <span class="text-danger">*</span></label>
                                        <input type="file" name="user_image" id="user_image" class="form-control" />

                                        @error('user_image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="imageShow mt-4">
                                        @if (isset($editUser))
                                            <img src="{{ asset('storage/' . $editUser->user_image) }}" class=" m-r-5 mb-2"
                                                alt="User Image"
                                                style="max-height: 161px; max-width:166px; border-radius:5px;">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12 mt-5">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

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
    </script>
@endsection
