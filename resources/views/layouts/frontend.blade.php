<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet" />

    <!-- Font Awesome 6 -->
    <link href="{{ asset('fontawesome/css/all.min.css') }}" rel="stylesheet" />

    <!-- DataTables Bootstrap 5 -->
    <link href="{{ asset('datatables/datatables.min.css') }}" rel="stylesheet" />

    <!-- Select2 -->
    <link href="{{ asset('select2/select2.min.css') }}" rel="stylesheet" />

    <!-- Tempus Dominus (DateTime Picker) -->
    <link href="{{ asset('datetimepicker/tempus-dominus.min.css') }}" rel="stylesheet" />

    <!-- Dropzone -->
    <link href="{{ asset('dropzone/dropzone.min.css') }}" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />

    @yield('styles')
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @guest
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('frontend.home') }}">
                                    {{ __('Dashboard') }}
                                </a>
                            </li>
                        @endguest
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if(Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('frontend.profile.index') }}">{{ __('My profile') }}</a>

                                    @can('user_management_access')
                                        <a class="dropdown-item disabled" href="#">
                                            {{ trans('cruds.userManagement.title') }}
                                        </a>
                                    @endcan
                                    @can('user_access')
                                        <a class="dropdown-item ms-3" href="{{ route('frontend.users.index') }}">
                                            {{ trans('cruds.user.title') }}
                                        </a>
                                    @endcan
                                    @can('role_access')
                                        <a class="dropdown-item ms-3" href="{{ route('frontend.roles.index') }}">
                                            {{ trans('cruds.role.title') }}
                                        </a>
                                    @endcan
                                    @can('permission_access')
                                        <a class="dropdown-item ms-3" href="{{ route('frontend.permissions.index') }}">
                                            {{ trans('cruds.permission.title') }}
                                        </a>
                                    @endcan

                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @if(session('message'))
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-success" role="alert">{{ session('message') }}</div>
                        </div>
                    </div>
                </div>
            @endif
            @if($errors->count() > 0)
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                <ul class="list-unstyled mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="{{ asset('bootstrap5/js/bootstrap.bundle.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('datatables/datatables.min.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('select2/select2.min.js') }}"></script>

    <!-- Moment.js -->
    <script src="{{ asset('moment/moment.min.js') }}"></script>

    <!-- Tempus Dominus (DateTime Picker) -->
    <script src="{{ asset('datetimepicker/tempus-dominus.min.js') }}"></script>

    <!-- Dropzone -->
    <script src="{{ asset('dropzone/dropzone.min.js') }}"></script>

    <!-- CKEditor 5 -->
    <script src="{{ asset('ckeditor5/ckeditor.js') }}"></script>

    <!-- PDFMake -->
    <script src="{{ asset('pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('pdfmake/vfs_fonts.js') }}"></script>

    <!-- JSZip -->
    <script src="{{ asset('jszip/jszip.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/main.js') }}"></script>

    @yield('scripts')

</html>
