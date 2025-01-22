@extends('admin.includes.master')
@section('title', 'Business Pages')
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4 fw-bold"><span class="text-muted fw-light">Business Setup /</span> Third Party API</h4>



        <!-- Navigation -->
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item">
                                <a href="{{ route('admin.setting.thirdPartyApi', 'mail_config') }}" class="nav-link {{ $page == 'mail_config' ? 'active' : '' }}"
                                    >
                                    Mail Config
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.setting.thirdPartyApi', 'sms_config') }}" class="nav-link {{ $page == 'sms_config' ? 'active' : '' }}"
                                    >
                                    SMS Config
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0">
                            <div class="tab-pane fade show active" id="navs-tab-home" role="tabpanel">
                                <div class="card-body">
                                    <form action="{{ route('admin.setting.thirdPartyApiPost') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="{{ $page }}" />
                                        <div class="row">
                                            <div class="mb-3 col-6">
                                                <label class="form-label" for="mailer_name">Mailer Name</label>
                                                <input type="text" class="form-control" id="mailer_name" name="mailer_name"
                                                    placeholder="mailer name" >
                                            </div>
                                            <div class="mb-3 col-6">
                                                <label class="form-label" for="host">Host</label>
                                                <input type="text" class="form-control" id="host" name="host"
                                                    placeholder="host" >
                                            </div>
                                            <div class="mb-3 col-6">
                                                <label class="form-label" for="driver">Driver</label>
                                                <input type="text" class="form-control" id="driver" name="driver"
                                                    placeholder="driver" >
                                            </div>
                                            <div class="mb-3 col-6">
                                                <label class="form-label" for="port">Port</label>
                                                <input type="text" class="form-control" id="port" name="port"
                                                    placeholder="port" >
                                            </div>
                                            <div class="mb-3 col-6">
                                                <label class="form-label" for="username">Username</label>
                                                <input type="text" class="form-control" id="username" name="username"
                                                    placeholder="username" >
                                            </div>
                                            <div class="mb-3 col-6">
                                                <label class="form-label" for="email">Email ID</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    placeholder="email" >
                                            </div>
                                            <div class="mb-3 col-6">
                                                <label class="form-label" for="encryption">Encryption</label>
                                                <input type="text" class="form-control" id="encryption" name="encryption"
                                                    placeholder="encryption" >
                                            </div>
                                            <div class="mb-3 col-6">
                                                <label class="form-label" for="password">Password</label>
                                                <input type="password" class="form-control" id="password" name="password"
                                                    placeholder="password" >
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-3">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Navigation -->



    </div>
@endsection
@section('script-area')
    <script src="{{ asset('assets/js/forms-editors.js') }}"></script>
@endsection
