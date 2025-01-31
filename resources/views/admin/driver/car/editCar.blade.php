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
                        <h5 class="card-title">Edit Car</h5>
                        <form action="{{ route('admin.cars.updateCar', $car->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="brand_id">Brand <span class="text-danger">*</span></label>
                                        <select name="brand_id" id="brand_id" class="form-control">
                                            <option value="" selected disabled>Select Brand</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ isset($car) && $car->brand_id == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->brand_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="model_id">Model <span class="text-danger">*</span></label>
                                        <select name="model_id" id="model_id" class="form-control">
                                            <option value="" selected disabled>Select Model</option>
                                            @foreach ($carModels as $model)
                                                <option value="{{ $model->id }}"
                                                    {{ isset($car) && $car->model_id == $model->id ? 'selected' : '' }}>
                                                    {{ $model->model_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('model_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="interior">interior <span class="text-danger">*</span></label>
                                        <input type="text" name="interior" id="interior" class="form-control"
                                            value="{{ old('interior', isset($car) ? $car->interior : '') }}" />
                                        @error('interior')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="seat">Seat <span class="text-danger">*</span></label>
                                        <input type="text" name="seat" id="seat" class="form-control"
                                            value="{{ old('seat', isset($car) ? $car->seat : '') }}" />
                                        @error('seat')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="registration_number">Registration Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="registration_number" id="registration_number"
                                            class="form-control"
                                            value="{{ old('registration_number', isset($car) ? $car->registration_number : '') }}" />
                                        @error('registration_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="insurance_number">Insurance Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="insurance_number" id="insurance_number"
                                            class="form-control"
                                            value="{{ old('insurance_number', isset($car) ? $car->insurance_number : '') }}" />
                                        @error('insurance_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="pollution_number">Pollution Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="pollution_number" id="pollution_number"
                                            class="form-control"
                                            value="{{ old('pollution_number', isset($car) ? $car->pollution_number : '') }}" />
                                        @error('pollution_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="rc_number">RC Number <span class="text-danger">*</span></label>
                                        <input type="text" name="rc_number" id="rc_number" class="form-control"
                                            value="{{ old('rc_number', isset($car) ? $car->rc_number : '') }}" />
                                        @error('rc_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="color">Color <span class="text-danger">*</span></label>
                                        <div class="d-flex align-items-center">
                                            <input type="color" name="color" id="color" class="form-control me-2"
                                                value="{{ old('color', isset($car) ? $car->color : '') }}" />

                                            <div id="colorBox"
                                                style="width: 40px; height: 40px; border: 1px solid #ccc; border-radius: 5px; background-color: {{ isset($car) ? $car->color : '#000000' }};">
                                            </div>
                                        </div>
                                        @error('color')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-3">
                                    <div class="form-group local-forms">
                                        <label for="rc_document">RC Document <span class="text-danger">*</span></label>
                                        <input type="file" name="rc_document" id="rc_document" class="form-control"
                                            accept="image/*,.pdf" />

                                        @error('rc_document')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="imageShow mt-4">
                                        @if (isset($car))
                                            @php
                                                $fileExtension = pathinfo($car->rc_document, PATHINFO_EXTENSION);
                                            @endphp
                                            @if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp']))
                                                <!-- Image Display -->
                                                <img src="{{ asset('storage/' . $car->rc_document) }}" class="m-r-5 mb-2"
                                                    alt="rc document"
                                                    style="max-height: 161px; max-width: 166px; border-radius: 5px;">
                                            @elseif (strtolower($fileExtension) == 'pdf')
                                                <!-- PDF Display -->
                                                <a href="{{ asset('storage/' . $car->rc_document) }}" target="_blank"
                                                    class="btn btn-primary">
                                                    View PDF
                                                </a>
                                            @else
                                                <!-- Handle other file types -->
                                                <span class="text-danger">Unsupported file type</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-xl-6 mt-5">
                                    <div class="form-group local-forms mt-5">
                                        <label for="car_images">Images <span class="text-danger">*</span></label>
                                        <input type="file" name="car_images[]" id="car_images" class="form-control"
                                            multiple accept="image/*" />

                                        @error('car_images')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="imageShow mt-4">
                                        @if (isset($car) && $car->car_images)
                                            @php
                                                $images = json_decode($car->car_images, true);
                                            @endphp
                                            @foreach ($images as $image)
                                                <div class="image-container d-inline-block position-relative">
                                                    <img src="{{ asset('storage/' . $image) }}" class="m-r-5 mb-2"
                                                        alt="Car Image"
                                                        style="max-height: 161px; max-width: 166px; border-radius: 5px;">

                                                    <form
                                                        action="{{ route('admin.car.deleteImage', ['id' => $car->id, 'image' => basename($image)]) }}"
                                                        method="POST" style="position: absolute; top: 0; right: 0;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            style="border-radius: 50%; padding: 0 5px; font-size: 12px;">
                                                            &times;
                                                        </button>
                                                    </form>

                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                </div>

                                <div class="col-md-12 mt-5 text-end">
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
        $(document).ready(function() {
            $("#color").on("input", function() {
                let selectedColor = $(this).val();
                $("#colorBox").css("background-color", selectedColor);
            });

            $('#brand_id').change(function() {
                var brandId = $(this).val();

                if (!brandId) {
                    $('#model_id').html('<option value="" selected disabled>Select Model</option>');
                    return;
                }
                $.ajax({
                    url: "{{ route('admin.getModels', '') }}/" + brandId,
                    type: 'GET',
                    success: function(response) {
                        $('#model_id').empty();

                        $('#model_id').append(
                            '<option value="" selected disabled>Select Model</option>');

                        $.each(response.models, function(index, model) {
                            var selected = (model.id ==
                                    '{{ isset($car) ? $car->model_id : '' }}') ?
                                'selected' : '';
                            $('#model_id').append('<option value="' + model.id + '" ' +
                                selected + '>' + model.model_name + '</option>');
                        });
                    }
                });
            });
        });
    </script>

@endsection
