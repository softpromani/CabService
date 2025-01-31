@extends('admin.includes.master')
@section('head-area')
    <link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>
@endsection

@section('content')
    <section class="section profile">
        <div class="row">


            <div class="col-xl-12">

                <div class="card">
                    <div class="card-body pt-3">
                        <ul class="nav nav-tabs nav-tabs-bordered">

                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#profile-overview">Overview</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#document">
                                    Document</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#carImage">
                                    Images</button>
                            </li>



                        </ul>
                        <div class="tab-content pt-2">

                            <div class="tab-pane fade show active profile-overview p-3" id="profile-overview">
                                {{-- <h5 class="card-title">Profile Details</h5> --}}

                                <div class="row ">
                                    <div class="col-lg-3 col-md-4 label ">Brand</div>
                                    <div class="col-lg-9 col-md-8">{{ $car->brand->brand_name }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Model</div>
                                    <div class="col-lg-9 col-md-8">{{ $car->model->model_name ?? '' }}</div>
                                </div>



                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Interior</div>
                                    <div class="col-lg-9 col-md-8">{{ $car->interior }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Seat</div>
                                    <div class="col-lg-9 col-md-8">{{ $car->seat }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Pollution Number</div>
                                    <div class="col-lg-9 col-md-8">{{ $car->pollution_number }}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Registration Number </div>
                                    <div class="col-lg-9 col-md-8">{{ $car->registration_number }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Insurance Number</div>
                                    <div class="col-lg-9 col-md-8">{{ $car->insurance_number }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Color</div>
                                    <div class="col-lg-9 col-md-8">{{ $car->color }}
                                        <div id="colorBox"
                                            style="width: 60px; height: 35px; border: 1px solid #ccc; border-radius: 5px; background-color: {{ isset($car) ? $car->color : '#000000' }};">
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="tab-pane fade  profile-overview p-3" id="document">

                                <div class="row ">
                                    <div class="col-lg-3 col-md-4 label ">RC Number</div>
                                    <div class="col-lg-9 col-md-8">{{ $car->rc_number ?? 12345678 }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">RC Document</div>
                                    <div class="col-lg-9 col-md-8">
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
                                                
                                                <span class="text-danger">Unsupported file type</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane fade  profile-overview p-3" id="carImage">

                                <div class="row ">
                                    @if (isset($car) && $car->car_images)
                                        @php
                                            $images = json_decode($car->car_images, true);
                                        @endphp
                                        @foreach ($images as $image)
                                            <div class="col-md-3">
                                                <div class="imageShow mt-4">


                                                    <div class="image-container d-inline-block position-relative">
                                                        <img src="{{ asset('storage/' . $image) }}" class="m-r-5 mb-2"
                                                            alt="Car Image"
                                                            style="max-height: 161px; max-width: 166px; border-radius: 5px;">
                                                    </div>

                                                </div>

                                            </div>
                                        @endforeach
                                    @endif
                                </div>



                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>

    </section>
@endsection

@section('script-area')
@endsection
