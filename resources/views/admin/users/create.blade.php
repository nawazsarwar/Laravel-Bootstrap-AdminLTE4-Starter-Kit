@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.user.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.users.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="required" for="name">{{ trans('cruds.user.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                @endif
                <div class="form-text text-muted">{{ trans('cruds.user.fields.name_helper') }}</div>
            </div>
            <div class="mb-3">
                <label class="required" for="email">{{ trans('cruds.user.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email') }}" required>
                @if($errors->has('email'))
                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                @endif
                <div class="form-text text-muted">{{ trans('cruds.user.fields.email_helper') }}</div>
            </div>
            <div class="mb-3">
                <label class="required" for="password">{{ trans('cruds.user.fields.password') }}</label>
                <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password" required>
                @if($errors->has('password'))
                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                @endif
                <div class="form-text text-muted">{{ trans('cruds.user.fields.password_helper') }}</div>
            </div>
            <div class="mb-3">
                <label class="required" for="roles">{{ trans('cruds.user.fields.roles') }}</label>
                <div class="mb-2">
                    <span class="btn btn-info btn-sm select-all rounded-0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-sm deselect-all rounded-0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('roles') ? 'is-invalid' : '' }}" name="roles[]" id="roles" multiple required>
                    @foreach($roles as $id => $role)
                        <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                </select>
                @if($errors->has('roles'))
                    <div class="invalid-feedback">{{ $errors->first('roles') }}</div>
                @endif
                <div class="form-text text-muted">{{ trans('cruds.user.fields.roles_helper') }}</div>
            </div>
            <div class="mb-3">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection