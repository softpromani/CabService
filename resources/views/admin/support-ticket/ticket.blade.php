@extends('admin.includes.master')
@section('head-area')
    <link href="https://unpkg.com/tabulator-tables@6.3.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.0/dist/js/tabulator.min.js"></script>
@endsection
@section('content')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body ">
                        <div class="px-3 py-4  mt-3">
                            <div class="d-flex flex-wrap justify-content-between gap-3 ">
                                <div class="">
                                    <form action="{{ url()->current() }}" method="GET">
                                        <div class="input-group mb-3 ">
                                            <input type="search" name="searchValue" class="form-control" placeholder="search ticket by subject" aria-label="search ticket by subject" aria-describedby="button-addon2">
                                            <button class="btn btn-primary" type="submit" id="button-addon2">Search</button>
                                          </div>
                                       
                                    </form>
                                </div>
                                <div class="">
                                    <div class="d-flex flex-wrap flex-sm-nowrap gap-3 justify-content-end">
                                        @php($priority = request()->has('priority') ? request()->input('priority') : '')
                                        <select class="form-control border-color-c1 w-160 filter-tickets"
                                            data-value="priority">
                                            <option value="all">all_Priority</option>
                                            <option value="low" {{ $priority == 'low' ? 'selected' : '' }}>low</option>
                                            <option value="medium" {{ $priority == 'medium' ? 'selected' : '' }}>medium</option>
                                            <option value="high" {{ $priority == 'high' ? 'selected' : '' }}>high</option>
                                            <option value="urgent" {{ $priority == 'urgent' ? 'selected' : '' }}>urgent</option>
                                        </select>

                                        @php($status = request()->has('status') ? request()->input('status') : '')
                                        <select class="form-control border-color-c1 w-160 filter-tickets"
                                            data-value="status">
                                            <option value="all">{{ 'all Status' }}</option>
                                            <option value="1" {{ $status == 1 ? 'selected' : '' }}>open</option>
                                            <option value="0" {{ $status == 0 ? 'selected' : '' }}>close</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach ($tickets as $key => $ticket)
                    <div class="card pt-3 mb-3">

                        <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3 mt-3">
                            <!-- Customer Info -->
                            <div class="d-flex gap-3 ">
                                @if ($ticket->customer)
                                    <img class="avatar avatar-lg"
                                        src="{{ $ticket->customer->user_image ? asset('storage/' . $ticket->customer->user_image) : asset('assets/admin/img/user.png') }}"
                                        alt="User Image">
                                    <div>
                                        <h6 class="mb-0">{{ $ticket->customer->first_name ?? '' }}
                                            {{ $ticket->customer->last_name ?? '' }}</h6>
                                        <div class="text-muted fz-12">{{ $ticket->customer->email ?? '' }}</div>
                                        <div class="d-flex gap-2 mt-1">
                                            <span class="custom-badge status-red fz-12 px-2 radius-50">
                                                {{ str_replace('_', ' ', $ticket->priority) }}
                                            </span>
                                            <span
                                                class=" radius-50 {{ $ticket->status == 1 ? 'badge bg-success-light' : ' custom-badge status-blue' }} fz-12 px-2">
                                                {{ $ticket->status == 1 ? 'Open' : 'Close' }}
                                            </span>
                                            <span class="text-grey">
                                                {{ str_replace('_', ' ', $ticket->type) }}
                                            </span>
                                        </div>

                                        <div class="text-muted fz-12 mt-2">
                                            @if ($ticket->created_at->diffInDays(\Carbon\Carbon::now()) < 7)
                                                {{ date('D h:i A', strtotime($ticket->created_at)) }}
                                            @else
                                                {{ date('d M Y h:i A', strtotime($ticket->created_at)) }}
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <h6>Customer not found!</h6>
                                @endif
                            </div>

                            <div class="form-check form-switch mb-5">
                                <input class="form-check-input confirmation_alert" type="checkbox"
                                    data-id="{{ $ticket->id }}"
                                    data-alert_message="Are you sure you want to change the ticket status?"
                                    data-alert_title="Change Ticket Status" data-alert_type="warning"
                                    data-status_field="status" data-alert_url="{{ route('admin.support-ticket.status') }}"
                                    {{ $ticket->status == '1' ? 'checked' : '' }}>
                            </div>
                        </div>

                        <hr>
                        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="row">
                                <div class="col-lg-10">
                                    <p class="mb-0 text-muted">{{ $ticket->description }}</p>

                                </div>
                                <div class="col-lg-2 text-end">
                                    <a class="btn btn-primary btn-sm"
                                        href="{{ route('admin.support-ticket.singleTicket', $ticket['id']) }}">
                                        <i class="tio-open-in-new"></i> View
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>
@endsection
@section('script-area')
<script>
    $(document).ready(function () {
    $('.filter-tickets').on('change', function () {
        let priority = $('select[name="priority"]').val();
        let status = $('select[name="status"]').val();
        let searchValue = $('#datatableSearch').val();

        $.ajax({
            url: "{{ route('admin.support-ticket.view') }}",
            method: "GET",
            data: {
                priority: priority,
                status: status,
                searchValue: searchValue
            },
            success: function (response) {
                $('#ticket-list').html(response.html); 
            }
        });
    });
});

</script>
@endsection
