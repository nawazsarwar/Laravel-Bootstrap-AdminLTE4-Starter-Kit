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
            <p class="login-box-msg">{{ trans('global.register') }}</p>
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="input-group mb-3">
                    <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" required autofocus placeholder="{{ trans('global.user_name') }}" value="{{ old('name', null) }}">
                    <div class="input-group-text">
                        <i class="fas fa-user"></i>
                    </div>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                </div>

                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required placeholder="{{ trans('global.login_email') }}" value="{{ old('email', null) }}">
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
                    <input type="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" required placeholder="{{ trans('global.login_password') }}">
                    <div class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </div>
                    @if($errors->has('password'))
                        <div class="invalid-feedback">
                            {{ $errors->first('password') }}
                        </div>
                    @endif
                </div>

                <div class="input-group mb-3">
                    <input type="password" name="password_confirmation" class="form-control" required placeholder="{{ trans('global.login_password_confirmation') }}">
                    <div class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                {{ trans('global.register') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
