@extends('layouts.app')
@section('content')
<div class="login-box">
    <div class="login-logo">
        @if(file_exists(public_path('logo.png')))
            <div class="text-center">
                <img src="{{ asset('logo.png') }}" alt="Logo" style="max-width: 100px; height: auto;">
            </div>
        @else
            <a href="{{ route('admin.home') }}">
                {{ trans('panel.site_title') }}
            </a>
        @endif
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">
                {{ trans('global.login') }}
            </p>

            @if(session()->has('message'))
                <div class="alert alert-info">
                    {{ session()->get('message') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="input-group mb-3">
                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required autocomplete="email" autofocus placeholder="{{ trans('global.login_email') }}" name="email" value="{{ old('email', null) }}">
                    <div class="input-group-text">
                        <i class="fas fa-envelope"></i>
                    </div>
                    @if($errors->has('email'))
                        <div class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                </div>

                <div class="input-group mb-3">
                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="{{ trans('global.login_password') }}">
                    <div class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </div>
                    @if($errors->has('password'))
                        <div class="invalid-feedback">
                            {{ $errors->first('password') }}
                        </div>
                    @endif
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember">
                            <label class="form-check-label" for="remember">{{ trans('global.remember_me') }}</label>
                        </div>
                    </div>
                    <div class="col-6">
                        @if(Route::has('password.request'))
                            <p class="mb-1">
                                <a href="{{ route('password.request') }}">
                                    {{ trans('global.forgot_password') }}
                                </a>
                            </p>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-block btn-primary">
                                {{ trans('global.login') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row text-center">
                <p class="mb-0">
                    <a href="{{ route('register') }}">
                        {{ trans('global.register') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
