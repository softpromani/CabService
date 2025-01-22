@extends('admin.includes.master')
@section('title', 'Business Setting')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4 fw-bold"><span class="text-muted fw-light">Business Setup /</span> Business Setting</h4>
    <form action="{{ route('admin.setting.business-setting.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
       <!-- Company Info -->
        <div class="row">
            <div class="col-xl-12">
                <div class="mb-4 card">
                    <div class="card-header">
                        <h5 class="mb-0 text-capitalize d-flex gap-1">
                            <i class="tio-user-big"></i>
                            Company information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-4">
                                <label class="form-label" for="company_name">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name"
                                    placeholder="company name" value="{{ getBusinessSetting('company_name') }}">
                            </div>
                            <div class="mb-3 col-4">
                                <label class="form-label" for="company_phone">Phone</label>
                                <input type="text" class="form-control" id="company_phone" name="company_phone"
                                    placeholder="company phone" value="{{ getBusinessSetting('company_phone') }}">
                            </div>
                            <div class="mb-3 col-4">
                                <label class="form-label" for="company_email">Email</label>
                                <input type="text" class="form-control" id="company_email" name="company_email"
                                    placeholder="company email" value="{{ getBusinessSetting('company_email') }}">
                            </div>
                            <div class="mb-3 col-4">
                                <label class="form-label" for="company_country">Country</label>
                                <select name="company_country" class="form-control">
                                    <option value="" disabled>--Select Country--</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            {{ getBusinessSetting('company_country') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-4">
                                <label class="form-label" for="company_address">Company Address</label>
                                <input type="text" class="form-control" id="company_address" name="company_address"
                                    placeholder="company address" value="{{ getBusinessSetting('company_address') }}">
                            </div>
                            <div class="mb-3 col-4">
                                <label class="form-label" for="company_copyright_text">Company Copyright Text</label>
                                <input type="text" class="form-control" id="company_copyright_text" name="company_copyright_text"
                                    placeholder="company copyright text" value="{{ getBusinessSetting('company_copyright_text') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Website Color -->
            <div class="col-xl-6">
                <div class="mb-4 card">
                    <div class="card-header">
                        <h5 class="mb-0 text-capitalize d-flex gap-1">
                            <i class="tio-user-big"></i>
                            Website Color
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-6">
                                <input type="color" class="form-control" id="primary_color" name="primary_color"
                                    value="{{ getBusinessSetting('primary_color') ?? '#000000' }}"
                                    style="height: 70px; width: 120px;">
                                <label class="form-label" for="primary_color">Primary Color</label>
                            </div>
                            <div class="mb-3 col-6">
                                <input type="color" class="form-control" id="secondary_color" name="secondary_color"
                                    value="{{ getBusinessSetting('secondary_color') ?? '#000000' }}"
                                    style="height: 70px; width: 120px;">
                                <label class="form-label" for="secondary_color">Secondary Color</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Website Header Logo -->
            <div class="col-xl-6">
                <div class="mb-4 card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 text-capitalize d-flex gap-1">
                            <i class="tio-user-big"></i>
                            Website Header Logo
                        </h5>
                        <span class="badge bg-label-primary">(1000 x 308 px)</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-12 text-center">
                                <img src="{{ asset('storage/'.getBusinessSetting('website_header_logo') ?? 'assets/img/placeholder-1-1.png') }}" style="width: 50px;" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <input type="file" class="form-control" id="website_header_logo" name="website_header_logo">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Website Footer Logo -->
            <div class="col-xl-6">
                <div class="mb-4 card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 text-capitalize d-flex gap-1">
                            <i class="tio-user-big"></i>
                            Website Footer Logo
                        </h5>
                        <span class="badge bg-label-primary">(1000 x 308 px)</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-12 text-center">
                                <img src="{{ asset('storage/'.getBusinessSetting('website_footer_logo') ?? 'assets/img/placeholder-1-1.png') }}" style="width: 50px;" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <input type="file" class="form-control" id="website_footer_logo" name="website_footer_logo">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Website Favicon Icon -->
            <div class="col-xl-6">
                <div class="mb-4 card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 text-capitalize d-flex gap-1">
                            <i class="tio-user-big"></i>
                            Website Favicon
                        </h5>
                        <span class="badge bg-label-primary">(Ratio 1:1)</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-12 text-center">
                                <img src="{{ asset('storage/'.getBusinessSetting('website_favicon') ?? 'assets/img/placeholder-1-1.png') }}" style="width: 50px;" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <input type="file" class="form-control" id="website_favicon" name="website_favicon">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-3">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>

    </form>

</div>
@endsection
@section('script-area')
    <script src="{{ asset('assets/js/forms-selects.js') }}"></script>
    <script src="{{ asset('assets/js/forms-tagify.js') }}"></script>
    <script src="{{ asset('assets/js/forms-typeahead.js') }}"></script>
@endsection
