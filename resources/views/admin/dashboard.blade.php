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
                    <div class="col-xxl-4 col-md-3">
                        <div class="card info-card sales-card">

                            <a href="{{ route('admin.driver.index') }}">
                                <div class="card-body">
                                    <h5 class="card-title">Total Driver </h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person-circle"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $dashboardData['active_drivers'] }}</h6>


                                        </div>
                                    </div>
                                </div>
                            </a>

                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-3">
                        <div class="card info-card customers-card">

                            <a href="{{ route('admin.customer.index') }}">
                                <div class="card-body">
                                    <h5 class="card-title">Total Customer </h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $dashboardData['total_customer'] }}</h6>


                                        </div>
                                    </div>
                                </div>
                            </a>

                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-3">
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
                            <a href="{{ route('admin.booking.index') }}">
                                <div class="card-body">
                                    <h5 class="card-title">Total Booking </h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-car-front"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6 id="total-booking">{{ $dashboardData['total_booking'] }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-3">
                        <div class="card info-card revenue-card">
                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>
                                    <li><a class="dropdown-item filter-revenue" data-filter="today" href="#">Today</a>
                                    </li>
                                    <li><a class="dropdown-item filter-revenue" data-filter="month" href="#">This
                                            Month</a></li>
                                    <li><a class="dropdown-item filter-revenue" data-filter="year" href="#">This
                                            Year</a></li>
                                </ul>
                            </div>
                            <a href="{{ route('admin.report.revenueReport') }}">
                                <div class="card-body">
                                    <h5 class="card-title">Total Revenue</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-currency-rupee"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6 id="total-revenues">{{ $dashboardData['total_revenue'] }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card ">
                            <div class="card-header bg-label-primary d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Bookings</h5>

                                <a href="{{ route('admin.booking.index') }}" class="btn btn-sm btn-light text-primary">
                                    All ...
                                </a>
                            </div>
                            <div class="card-body p-3">
                                <ul class="list-group list-group-flush">
                                    @forelse ($dashboardData['recent_bookings'] as $booking)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">

                                            <div>
                                                <strong>{{ $booking->book_by_user->first_name . ' ' . $booking->book_by_user->last_name ?? 'Unknown User' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $booking->pickup_station->point_name }} →
                                                    {{ $booking->dropoff_station->point_name }}</small>
                                            </div>
                                            <span
                                                class="badge bg-label-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </li>
                                    @empty
                                        <li class="list-group-item text-center text-muted">No recent bookings found.</li>
                                    @endforelse
                                </ul>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">Live Ride Statistics</h5>
                                    <a href="{{ route('admin.rides.index') }}" class="btn btn-sm bg-label-primary">
                                        View ...
                                    </a>
                                </div>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h4 class="mb-0 text-primary">{{ $dashboardData['total_rides'] ?? 0 }}</h4>
                                        <small class="text-muted">Total Rides</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="mb-0 text-danger">{{ $dashboardData['cancelled_rides'] ?? 0 }}</h4>
                                        <small class="text-muted">Cancelled Rides</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="mb-0 text-warning">{{ $dashboardData['scheduled_rides'] ?? 0 }}</h4>
                                        <small class="text-muted">Scheduled Rides</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Top Earning Drivers</h5>
                                <ul class="list-group list-group-flush">
                                    @foreach ($dashboardData['top_earning_drivers'] as $driver)
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>{{ $driver->name }} </span>
                                            <strong>₹{{ number_format($driver->total_earnings) }}</strong>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    {{-- <div class="col-md-12 mb-4">
                        <div class="card shadow-sm">
                            <div
                                class="card-header bg-label-primary text-white d-flex justify-content-between align-items-center">
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
                                            <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div id="booking-revenue-chart"></div>

                            </div>
                        </div>
                    </div> --}}

                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div
                                class="card-header bg-label-success text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Booking Statistics</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                    <!-- Total Bookings -->
                                    <div class="d-flex align-items-center">
                                        <h5 class="mb-0 me-2 fw-semibold">Total:</h5>
                                        <h5 class="mb-0 fw-semibold" id="booking-total">0</h5>
                                    </div>



                                    <div class="d-flex align-items-center gap-2 ">
                                        <select id="booking-filter-year" class="form-control form-control-sm">
                                            @for ($i = now()->year; $i >= 2000; $i--)
                                                <option value="{{ $i }}"
                                                    {{ now()->year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>

                                        <select id="booking-filter-month" class="form-control form-control-sm">
                                            <option value="">All Months</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">
                                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                </option>
                                            @endfor
                                        </select>

                                        <select id="booking-filter-type" class="form-control form-control-sm">
                                            <option value="">-- Quick Filter --</option>
                                            <option value="today">Today</option>
                                            <option value="yesterday">Yesterday</option>
                                            <option value="last_7_days">Last 7 Days</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="booking-chart"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div
                                class="card-header bg-label-info text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Revenue Statistics</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                    <!-- Total Bookings -->
                                    <div class="d-flex align-items-center">
                                        <h5 class="mb-0 me-2 fw-semibold">Total:</h5>
                                        <h5 class="mb-0 fw-semibold" id="total-revenue">0</h5>
                                    </div>



                                    <div class="d-flex align-items-center gap-2 ">
                                        <select id="revenue-filter-year" class="form-control form-control-sm">
                                            @for ($i = now()->year; $i >= 2000; $i--)
                                                <option value="{{ $i }}"
                                                    {{ now()->year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>

                                        <select id="revenue-filter-month" class="form-control form-control-sm">
                                            <option value="">All Months</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">
                                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                </option>
                                            @endfor
                                        </select>

                                        <select id="revenue-filter-type" class="form-control form-control-sm">
                                            <option value="">-- Quick Filter --</option>
                                            <option value="today">Today</option>
                                            <option value="yesterday">Yesterday</option>
                                            <option value="last_7_days">Last 7 Days</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="revenue-chart"></div>
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

            // Booking Filter
            $('.filter-booking').on('click', function(e) {
                e.preventDefault();
                let filterType = $(this).data('filter');

                $.ajax({
                    url: "{{ route('admin.dashboard') }}",
                    type: "GET",
                    data: {
                        filter: filterType,
                        type: 'booking' // 👈 Add this line
                    },
                    success: function(response) {
                        $('#total-booking').text(response.total_booking);
                    }
                });
            });

            // Ride Filter
            $('.filter-revenue').on('click', function(e) {
                e.preventDefault();
                const filter = $(this).data('filter');

                $.ajax({
                    url: "{{ route('admin.dashboard') }}",
                    method: 'GET',
                    data: {
                        filter,
                        type: 'revenue'
                    },
                    success: function(res) {
                        $('#total-revenues').text(res.total_revenue);
                    }
                });
            });

        });


        $(document).ready(function() {
            let bookingChart = null;
            let revenueChart = null;

            function fetchBookingData(filters) {
                $.ajax({
                    url: "{{ route('admin.bookings.chart.data') }}",
                    type: "GET",
                    data: filters,
                    success: function(response) {
                        // Update total count
                        $("#booking-total").text(response.total || 0);

                        // Destroy old chart if exists
                        if (bookingChart) bookingChart.destroy();

                        bookingChart = new ApexCharts(document.querySelector("#booking-chart"), {
                            chart: {
                                height: 300,
                                type: "bar",
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#28a745'],
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: "50%",
                                    borderRadius: 4
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            series: [{
                                name: "Bookings",
                                data: response.bookings
                            }],
                            xaxis: {
                                categories: response.months
                            },
                            yaxis: {
                                title: {
                                    text: "No. of Bookings"
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: val => val + " Bookings"
                                }
                            }
                        });
                        bookingChart.render();
                    },
                    error: function() {
                        alert("Failed to load booking data.");
                    }
                });
            }

            function fetchRevenueData(filters) {
                $.ajax({
                    url: "{{ route('admin.revenues.chart.data') }}",
                    type: "GET",
                    data: filters,
                    success: function(response) {
                        // Update total revenue
                        $("#total-revenue").text("₹ " + parseFloat(response.total || 0).toFixed(2));

                        // Destroy old chart if exists
                        if (revenueChart) revenueChart.destroy();

                        revenueChart = new ApexCharts(document.querySelector("#revenue-chart"), {
                            chart: {
                                height: 300,
                                type: "area",
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#17a2b8'],
                            stroke: {
                                curve: 'smooth',
                                width: 2
                            },
                            dataLabels: {
                                enabled: false
                            },
                            series: [{
                                name: "Revenue",
                                data: response.revenues
                            }],
                            xaxis: {
                                categories: response.months
                            },
                            yaxis: {
                                title: {
                                    text: "Total Revenue (₹)"
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: val => "₹ " + parseFloat(val).toFixed(2)
                                }
                            }
                        });
                        revenueChart.render();
                    },
                    error: function() {
                        alert("Failed to load revenue data.");
                    }
                });
            }

            // Filter Change Events
            $("#booking-filter-year, #booking-filter-month, #booking-filter-type").change(function() {
                fetchBookingData({
                    year: $('#booking-filter-year').val(),
                    month: $('#booking-filter-month').val(),
                    type: $('#booking-filter-type').val()
                });
            });

            $("#revenue-filter-year, #revenue-filter-month, #revenue-filter-type").change(function() {
                fetchRevenueData({
                    year: $('#revenue-filter-year').val(),
                    month: $('#revenue-filter-month').val(),
                    type: $('#revenue-filter-type').val()
                });
            });

            // Initial Load with selected filter values
            fetchBookingData({
                year: $('#booking-filter-year').val(),
                month: $('#booking-filter-month').val(),
                type: $('#booking-filter-type').val()
            });

            fetchRevenueData({
                year: $('#revenue-filter-year').val(),
                month: $('#revenue-filter-month').val(),
                type: $('#revenue-filter-type').val()
            });
        });
    </script>
@endsection
