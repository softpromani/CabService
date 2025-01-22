@extends('admin.includes.master')
@section('title', 'Business Pages')
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4 fw-bold"><span class="text-muted fw-light">Business Setup /</span> Business Pages</h4>



        <!-- Navigation -->
        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item">
                                <a href="{{ route('admin.setting.business-pages.index',['page' => 'about_us']) }}" class="nav-link {{ $page == 'about_us' ? 'active' : '' }}"
                                    >
                                    About Us
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.setting.business-pages.index',['page' => 'terms_condition']) }}" class="nav-link {{ $page == 'terms_condition' ? 'active' : '' }}"
                                    >
                                    Terms & Condition
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.setting.business-pages.index',['page' => 'privacy_policy']) }}" class="nav-link {{ $page == 'privacy_policy' ? 'active' : '' }}"
                                    >
                                    Privacy Policy
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.setting.business-pages.index',['page' => 'refund_policy']) }}" class="nav-link {{ $page == 'refund_policy' ? 'active' : '' }}"
                                    >
                                    Refund Policy
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0">
                            <div class="tab-pane fade show active" id="navs-tab-home" role="tabpanel">
                                <div class="card-body">
                                    <form id="quillForm" action="{{ route('admin.setting.business-pages.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="page" value="{{ $page }}">
                                        <div class="row">
                                            <div class="mb-3 col-12">
                                                <div id="full-editor" name="about_us">{!! $data !!}</div>
                                                <input type="hidden" name="data" id="quillContent">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-3">
                                                <button type="submit" class="btn btn-primary">Update</button>
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
