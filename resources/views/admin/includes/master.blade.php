<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Dashboard -Air Travel Services</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('assets/admin/img/cablogo.jpeg') }}" rel="icon">
    <link href="{{ asset('assets/admin/img/cablogo.jpeg') }}" rel="apple-touch-icon">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>


    {{--  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        {{--  @vite(['assets/admin/vendor/bootstrap/css/bootstrap.min.css', 'assets/admin/vendor/bootstrap-icons/bootstrap-icons.css', 'assets/admin/vendor/boxicons/css/boxicons.min.css', 'assets/admin/vendor/quill/quill.snow.css', 'assets/admin/vendor/quill/quill.bubble.css', 'assets/admin/vendor/remixicon/remixicon.css', 'assets/admin/vendor/simple-datatables/style.css', 'assets/admin/css/style.css'])
    @else  --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['assets/admin/vendor/bootstrap/css/bootstrap.min.css', 'assets/admin/vendor/bootstrap-icons/bootstrap-icons.css', 'assets/admin/vendor/boxicons/css/boxicons.min.css', 'assets/admin/vendor/quill/quill.snow.css', 'assets/admin/vendor/quill/quill.bubble.css', 'assets/admin/vendor/remixicon/remixicon.css', 'assets/admin/vendor/simple-datatables/style.css', 'assets/admin/css/style.css'])

    @else
        @include('admin.includes.head');
    {{--  @endif  --}}
    @yield('head-area')
</head>

<body>

    <!-- ======= Header ======= -->

    @include('admin.includes.topbar')


    <!-- End Header -->

    <!-- ======= Sidebar ======= -->

    @include('admin.includes.sidebar')


    <!-- End Sidebar-->

    <main id="main" class="main">

        @yield('content')

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->

    @include('admin.includes.footer')


    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    @include('admin.includes.foot')
    @yield('script-area')

</body>

</html>
