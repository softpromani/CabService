@extends('admin.includes.master')
@section('content')
    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-xxl-4 col-md-3">
                        <div class="card info-card revenuereport-card">


                            <div class="card-body">
                                <h5 class="card-title">Total Revenue </h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-currency-rupee"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>₹{{ number_format($revenue['total']) }}</h6>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="col-xxl-4 col-md-3">
                        <div class="card info-card sales-card">


                            <div class="card-body">
                                <h5 class="card-title">Today's Revenue</h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-calendar-day"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>₹{{ number_format($revenue['today']) }}</h6>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-3">
                        <div class="card info-card car-card">


                            <div class="card-body">
                                <h5 class="card-title">This Month's </h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-calendar-month"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>₹{{ number_format($revenue['month']) }}</h6>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-xxl-4 col-md-3">
                        <div class="card info-card customers-card">


                            <div class="card-body">
                                <h5 class="card-title">Avg Revenue / Ride</h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-graph-up-arrow"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>₹{{ number_format($revenue['avg_per_ride']) }}</h6>


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
                                class="card-header bg-label-info text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Revenue Statistics</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                    <!-- Total Bookings -->
                                    <div class="d-flex align-items-center">
                                        <h5 class="mb-0 me-2 fw-semibold">Total:</h5>
                                        <h5 class="mb-0 fw-semibold" id="total-revenue">0</h5>
                                        <small class="text-muted ms-2" id="total-revenue-change"></small>
                                    </div>


                                    <div class="d-flex align-items-center gap-2 ">
                                        <select id="chart-type" class="form-control form-control-sm">
                                            <option value="area">Area</option>
                                            <option value="line">Line</option>
                                            <option value="bar">Bar</option>
                                        </select>

                                        <select id="revenue-filter-year" class="form-control form-control-sm">
                                            @for ($i = now()->year; $i >= 2000; $i--)
                                                <option value="{{ $i }}"
                                                    {{ now()->year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>

                                        <select id="revenue-filter-month" class="form-control form-control-sm">
                                            <option value="">All Months</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}
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
            let revenueChart = null;

            function fetchRevenueData(filters) {
                $.ajax({
                    url: "{{ route('admin.report.revenue.chart.data') }}",
                    type: "GET",
                    data: filters,
                    success: function(response) {
                        $("#total-revenue").text("₹ " + parseFloat(response.total || 0).toFixed(2));

                        // Calculate and show comparison
                        let change = response.total - response.previous_total;
                        let percent = response.previous_total > 0 ?
                            (change / response.previous_total) * 100 :
                            0;
                        let indicator = change >= 0 ? '↑' : '↓';
                        let text =
                            `${indicator} ₹${Math.abs(change).toFixed(2)} (${percent.toFixed(2)}%)`;
                        $("#total-revenue-change").text(text).toggleClass("text-success", change >= 0)
                            .toggleClass("text-danger", change < 0);

                        if (revenueChart) revenueChart.destroy();

                        revenueChart = new ApexCharts(document.querySelector("#revenue-chart"), {
                            chart: {
                                height: 300,
                                type: $("#chart-type").val(),
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
                            },
                            plotOptions: {
                                bar: {
                                    columnWidth: $("#chart-type").val() === "bar" ? "30%" :
                                        undefined
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

            function reloadChart() {
                fetchRevenueData({
                    year: $('#revenue-filter-year').val(),
                    month: $('#revenue-filter-month').val(),
                    type: $('#revenue-filter-type').val()
                });
            }

            $("#revenue-filter-year, #revenue-filter-month, #revenue-filter-type, #chart-type").change(reloadChart);

            // Initial Load
            reloadChart();
        });
    </script>
@endsection
