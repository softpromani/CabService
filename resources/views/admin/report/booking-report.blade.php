@extends('admin.includes.master')
@section('content')
    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-xxl-4 col-md-4">
                        <div class="card info-card car-card ">


                            <div class="card-body">
                                <h5 class="card-title">Total Booking </h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-calendar-check-fill"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $totalBooking }}</h6>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="col-xxl-4 col-md-4">
                        <div class="card info-card cancelledbookingreport-card">


                            <div class="card-body">
                                <h5 class="card-title">Cancelled Booking</h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $cancelledBooking }}</h6>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-4">
                        <div class="card info-card revenuereport-card">


                            <div class="card-body">
                                <h5 class="card-title">Total Revenue</h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-currency-rupee"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $totalRevenue }}</h6>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    

                </div>

                <div class="row">
                  

                    <div class="col-md-12">
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
                                        <h5 class="mb-0 fw-semibold" id="total-booking">0</h5>
                                    </div>

                                    <div class="d-flex align-items-center gap-3 ">
                                        <div class="text-muted small"><i class="fa fa-circle me-1"
                                                style="color:#6a81ea"></i> Confirmed: <span
                                                id="total-confirmed-booking">0</span></div>
                                        <div class="text-muted small"><i class="fa fa-circle me-1"
                                                style="color:#00D3C7"></i> Pending: <span
                                                id="total-pending-booking">0</span></div>
                                        <div class="text-muted small"><i class="fa fa-circle me-1"
                                                style="color:#FF5C5C"></i> Cancelled: <span
                                                id="total-cancelled-booking">0</span></div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2 ">
                                        <select id="filter-year" class="form-control form-control-sm">
                                            @for ($i = now()->year; $i >= 2000; $i--)
                                                <option value="{{ $i }}"
                                                    {{ now()->year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>

                                        <select id="filter-month" class="form-control form-control-sm">
                                            <option value="">All Months</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                </option>
                                            @endfor
                                        </select>

                                        <select id="filter-type" class="form-control form-control-sm">
                                            <option value="">-- Quick Filter --</option>
                                            <option value="today">Today</option>
                                            <option value="yesterday">Yesterday</option>
                                            <option value="last_7_days">Last 7 Days</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="bookingChart" style="height: 400px;"></div>
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
        let chart;

        function renderChart(labels, confirmed, cancelled, pending) {
            const options = {
                chart: {
                    type: 'bar',
                    height: 400,
                    stacked: false 
            
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        borderRadius: 2
                    }
                },
                
                series: [{
                        name: 'Confirmed',
                        data: confirmed
                    },
                    {
                        name: 'Cancelled',
                        data: cancelled
                    },
                    {
                        name: 'Pending',
                        data: pending
                    }
                ],
                xaxis: {
                    categories: labels,
                    title: {
                        text: 'Date',
                        style: {
                            fontSize: '14px',
                            fontWeight: 600
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Number of Bookings',
                        style: {
                            fontSize: '14px',
                            fontWeight: 600
                        }
                    }
                },
                colors: ['#00D3C7', '#FF5C5C', '#6a81ea'],
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    shared: true,
                    intersect: false
                }
            };

            if (chart) {
                chart.updateOptions(options);
            } else {
                chart = new ApexCharts(document.querySelector("#bookingChart"), options);
                chart.render();
            }
        }

        function loadChartData() {
            const year = $('#filter-year').val();
            const month = $('#filter-month').val();
            const filter = $('#filter-type').val();

            $.ajax({
                url: "{{ route('admin.report.booking.chart.data') }}",
                type: "GET",
                data: {
                    year,
                    month,
                    filter
                },
                success: function(res) {
                    renderChart(res.labels, res.confirmed, res.cancelled, res.pending);

                    // Update totals
                    const totalConfirmed = res.confirmed.reduce((a, b) => a + b, 0);
                    const totalCancelled = res.cancelled.reduce((a, b) => a + b, 0);
                    const totalPending = res.pending.reduce((a, b) => a + b, 0);
                    const total = totalConfirmed + totalCancelled + totalPending;

                    $('#total-confirmed-booking').text(totalConfirmed);
                    $('#total-cancelled-booking').text(totalCancelled);
                    $('#total-pending-booking').text(totalPending);
                    $('#total-booking').text(total);
                },
                error: function() {
                    alert("Failed to load booking data.");
                }
            });
        }

        
       


        $(document).ready(function() {
            loadChartData();

            // Auto-trigger when filters change
            $('#filter-year, #filter-month, #filter-type').on('change', function() {
                loadChartData();
            });
        });
    </script>
@endsection
