<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ trans('panel.site_title') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet" />

    <!-- Font Awesome 6 -->
    <link href="{{ asset('fontawesome/css/all.min.css') }}" rel="stylesheet" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet" />

    <!-- AdminLTE 4 CSS -->
    <link href="{{ asset('adminlte4/css/adminlte.min.css') }}" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />

    @yield('styles')
</head>

<body class="login-page bg-body-secondary">
    @yield('content')

    <!-- jQuery -->
    <script src="{{ asset('jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="{{ asset('bootstrap5/js/bootstrap.bundle.min.js') }}"></script>

    <!-- AdminLTE 4 JS -->
    <script src="{{ asset('adminlte4/js/adminlte.min.js') }}"></script>

    @yield('scripts')
</body>

</html>
