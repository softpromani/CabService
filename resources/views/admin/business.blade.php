@extends('admin.includes.master')
@section('content')
<div class="pagetitle">
    <h1>Business Setting</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Business</li>
        </ol>
    </nav>
</div>

<!-- End Page Title -->


<div class="card">
    <div class="card-body">
        <h5 class="card-title">Add Business</h5>

        <!-- No Labels Form -->
        <form class="row g-3" action="{{ route('admin.business-Setting') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="col-md-6">
                <label for="inputAppLogo" class="form-label">App Logo</label>
                <input type="file" class="form-control" name="app_logo" placeholder="App Logo">
            </div>
            <div class="col-md-6">
                <label for="inputSplashScreen" class="form-label">Splash Screen</label>
                <input type="file" class="form-control" name="splash_screen" placeholder="Splash Screen">
            </div>
            <div class="col-md-6">
                <label for="inputPrimaryColor" class="form-label">Primary Color</label>
                <input type="text" class="form-control" name="primary_color" placeholder="Primary Color">
            </div>
            <div class="col-md-6">
                <label for="inputSecondaryColor" class="form-label">Secondary Color</label>
                <input type="text" class="form-control" name="secondary_color" placeholder="Secondary Color">
            </div>
            <div class="col-md-6">
                <label for="inputTextColor" class="form-label">Text Color</label>
                <input type="text" class="form-control" name="text_color" placeholder="Text Color">
            </div>
            <div class="col-md-6">
                <label for="inputGoogleMapApi" class="form-label">Google Map Api</label>
                <input type="text" class="form-control" name="google_map_api" placeholder="Google Map Api">
            </div>
            <div class="col-md-6">
                <label for="inputWebLogo" class="form-label">Web Logo</label>
                <input type="file" class="form-control" name="web_logo" placeholder="Web Logo">
            </div>
            <div>
                <button type="submit" class="btn btn-primary"> Submit </button>
            </div>
        </form>

    </div>
    @endsection
