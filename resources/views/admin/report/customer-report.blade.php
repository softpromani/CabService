@extends('admin.includes.master')
@section('content')
    <section class="section dashboard">
        <style>
            .small-badge {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                background-color: #6c757d;
                /* Bootstrap's secondary color */
                color: #fff;
                font-size: 12px;
                line-height: 24px;
                text-align: center;
                display: inline-block;
                padding: 0;
            }
        </style>
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-xxl-4 col-md-4">
                        <div class="card info-card revenuereport-card">


                            <div class="card-body">
                                <h5 class="card-title">Total Customers </h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $totalUsers }}</h6>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="col-xxl-4 col-md-4">
                        <div class="card info-card sales-card">


                            <div class="card-body">
                                <h5 class="card-title">New Customer This Month</h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-calendar-day"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $newUsers }}</h6>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="col-xxl-4 col-md-4">
                        <div class="card info-card cancelledbookingreport-card">


                            <div class="card-body">
                                <h5 class="card-title">Inactive Customer</h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi-person-dash-fill"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6> {{ $inactiveCustomers }}</h6>


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
                                <h6 class="mb-0">Customer Statistics</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                    <!-- Total Customers -->
                                    <div class="d-flex align-items-center">
                                        <h5 class="mb-0 me-2 fw-semibold">Total:</h5>
                                        <h5 class="mb-0 fw-semibold" id="total-customers">0</h5>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 ">
                                        <div class="text-muted small"><i class="fa fa-circle me-1"
                                                style="color:#28a745"></i> Total: <span id="total-cusomer">0</span></div>
                                        <div class="text-muted small"><i class="fa fa-circle me-1"
                                                style="color:#17a2b8"></i> Active: <span id="total-active-cusomerr">0</span>
                                        </div>
                                        <div class="text-muted small"><i class="fa fa-circle me-1"
                                                style="color:#dc3545"></i> Inactive: <span
                                                id="total-inactive-cusomer">0</span></div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <select id="chart-type" class="form-control form-control-sm">
                                            <option value="area">Area</option>
                                            <option value="line">Line</option>
                                            <option value="bar">Bar</option>
                                        </select>

                                        <select id="customer-filter-year" class="form-control form-control-sm">
                                            @for ($i = now()->year; $i >= 2000; $i--)
                                                <option value="{{ $i }}"
                                                    {{ now()->year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>

                                        <select id="customer-filter-month" class="form-control form-control-sm">
                                            <option value="">All Months</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                </option>
                                            @endfor
                                        </select>

                                        <select id="customer-filter-type" class="form-control form-control-sm">
                                            <option value="">-- Quick Filter --</option>
                                            <option value="today">Today</option>
                                            <option value="yesterday">Yesterday</option>
                                            <option value="last_7_days">Last 7 Days</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="customer-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">


                    <div class="col-md-6">
                        <div class="card shadow border-0">
                            <div
                                class="card-header bg-label-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-crown me-2"></i>Top 10 Customers with Most Bookings
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col" class="text-center fw-semibold">#</th>
                                                <th scope="col" class="fw-semibold">Customer</th>
                                                <th scope="col" class="text-end pe-4 fw-semibold"> Total Bookings</th>
                                            </tr>
                                        </thead>
                                        <tbody class="p-4">
                                            @forelse ($topCustomers as $customer)
                                                <tr>
                                                    <td class="text-center">
                                                        @php
                                                            $rankColors = ['#FFD700', '#C0C0C0', '#CD7F32'];
                                                            $color = $rankColors[$loop->index] ?? '#6c757d';
                                                        @endphp
                                                        <span class="small-badge"
                                                            style="background-color: {{ $color }};">
                                                            {{ $loop->iteration }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $customer->name ?? 'Unknown User' }}</td>
                                                    <td class="text-end pe-5"> <span
                                                            class="fw-semibold">{{ $customer->booking_count }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">No top customers
                                                        found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>




                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-label-purple d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-calendar-check me-2"></i>New Booking This Month
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center fw-semibold">#</th>
                                                <th class="fw-semibold">Customer</th>
                                                <th class="fw-semibold">Station</th>
                                            </tr>
                                        </thead>
                                        <tbody class="p-4">
                                            @forelse ($newBookingsThisMonth as $index => $customer)
                                                <tr>
                                                    <td class="text-center">
                                                        <span
                                                            class="small-badge bg-label-success">{{ $index + 1 }}</span>
                                                    </td>
                                                    <td>{{ $customer->name ?? 'Unknown User' }}</td>
                                                    <td>{{ $customer->pickup_station }} &rightarrow;
                                                        {{ $customer->dropoff_station }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted">No customers found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
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
    <script>
        $(document).ready(function() {
            let customerChart = null;

            function fetchCustomerData(filters) {
                $.ajax({
                    url: "{{ route('admin.report.customer.chart.data') }}",
                    type: "GET",
                    data: filters,
                    success: function(response) {
                        $("#total-customers").text(response.total_customers || 0);
                        $("#total-cusomer").text(response.total_sum || 0);
                        $("#total-active-cusomerr").text(response.active_sum || 0);
                        $("#total-inactive-cusomer").text(response.inactive_sum || 0);

                        if (customerChart) customerChart.destroy();

                        customerChart = new ApexCharts(document.querySelector("#customer-chart"), {
                            chart: {
                                height: 300,
                                type: $("#chart-type").val(),
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#28a745', '#17a2b8', '#dc3545'],
                            stroke: {
                                curve: 'smooth',
                                width: 2
                            },
                            dataLabels: {
                                enabled: false
                            },
                            series: [{
                                    name: "Total Customers",
                                    data: response.total
                                },
                                {
                                    name: "Active Customers",
                                    data: response.active
                                },
                                {
                                    name: "Inactive Customers",
                                    data: response.inactive
                                }
                            ],
                            xaxis: {
                                categories: response.months
                            },
                            yaxis: {
                                title: {
                                    text: "Customers"
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: val => val + " customers"
                                }
                            },
                            plotOptions: {
                                bar: {
                                    columnWidth: $("#chart-type").val() === "bar" ? "10%" :
                                        undefined
                                }
                            }
                        });
                        customerChart.render();
                    },
                    error: function() {
                        alert("Failed to load customer data.");
                    }
                });
            }

            function reloadChart() {
                fetchCustomerData({
                    year: $('#customer-filter-year').val(),
                    month: $('#customer-filter-month').val()
                });
            }

            $("#customer-filter-year, #customer-filter-month, #chart-type").change(reloadChart);

            // Initial Load
            reloadChart();
        });
    </script>
@endsection
