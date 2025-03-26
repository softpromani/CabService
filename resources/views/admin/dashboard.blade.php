@extends('admin.includes.master')
@section('content')
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-xxl-4 col-md-4">
                        <div class="card info-card sales-card">


                            <div class="card-body">
                                <h5 class="card-title">Total Driver </h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $dashboardData['active_drivers'] }}</h6>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-4">
                        <div class="card info-card customers-card">


                            <div class="card-body">
                                <h5 class="card-title">Total Customer </h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $dashboardData['total_customer'] }}</h6>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-4">
                        <div class="card info-card car-card">
                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>
                                    <li><a class="dropdown-item filter-booking" data-filter="today" href="#">Today</a>
                                    </li>
                                    <li><a class="dropdown-item filter-booking" data-filter="month" href="#">This
                                            Month</a></li>
                                    <li><a class="dropdown-item filter-booking" data-filter="year" href="#">This
                                            Year</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Total Booking </h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-car-front"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 id="total-booking">{{ $dashboardData['total_booking'] }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-label-primary text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Booking Statistics</h6>
                            <div class="d-flex gap-2">
                                <select id="lead-filter-year" class="form-control form-control-sm">
                                    @for ($i = now()->year; $i >= 2000; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <select id="lead-filter-month" class="form-control form-control-sm">
                                    <option value="">Total Months</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="card-body p-3">
                          <div id="booking-revenue-chart"></div>
                           
                        </div>
                    </div>
                </div>
                </div>
            </div>

        </div>
    </section>
@endsection
@section('script-area')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function() {
            $('.filter-booking').on('click', function(e) {
                e.preventDefault();
                let filterType = $(this).data('filter');

                $.ajax({
                    url: "{{ route('admin.dashboard') }}",
                    type: "GET",
                    data: {
                        filter: filterType
                    },
                    success: function(response) {
                        $('#total-booking').text(response.total_booking);
                    }
                });
            });
          });
        $(document).ready(function () {
        $.ajax({
            url: "{{ route('admin.chart.data') }}",
            type: "GET",
            success: function (response) {
                var options = {
                    chart: {
                        type: 'line'
                    },
                    series: [{
                        name: "Total Bookings",
                        data: response.bookings
                    }, {
                        name: "Total Revenue",
                        data: response.revenues
                    }],
                    xaxis: {
                        categories: response.months
                    }
                };

                var chart = new ApexCharts($("#booking-revenue-chart")[0], options);
                chart.render();
            }
        });
    });
    </script>
@endsection
