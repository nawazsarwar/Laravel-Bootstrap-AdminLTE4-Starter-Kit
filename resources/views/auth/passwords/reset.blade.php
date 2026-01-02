@extends('layouts.app')
@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href="{{ route('admin.home') }}">
            {{ trans('panel.site_title') }}
        </a>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">
                {{ trans('global.reset_password') }}
            </p>

            <form method="POST" action="{{ route('password.request') }}">
                @csrf

                <input name="token" value="{{ $token }}" type="hidden">

                <div class="input-group mb-3">
                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="{{ trans('global.login_email') }}">
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

                <div class="input-group mb-3">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="{{ trans('global.login_password_confirmation') }}">
                    <div class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                {{ trans('global.reset_password') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection