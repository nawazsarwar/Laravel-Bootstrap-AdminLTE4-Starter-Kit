@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    {{ trans('global.create') }} {{ trans('cruds.role.title_singular') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route("frontend.roles.store") }}" enctype="multipart/form-data">
                        @method('POST')
                        @csrf
                        <div class="mb-3">
                            <label class="required" for="title">{{ trans('cruds.role.fields.title') }}</label>
                            <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}" required>
                            @if($errors->has('title'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('title') }}
                                </div>
                            @endif
                            <div class="form-text text-muted">{{ trans('cruds.role.fields.title_helper') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="required" for="permissions">{{ trans('cruds.role.fields.permissions') }}</label>
                            <div class="mb-2">
                                <span class="btn btn-info btn-sm select-all rounded-0">{{ trans('global.select_all') }}</span>
                                <span class="btn btn-info btn-sm deselect-all rounded-0">{{ trans('global.deselect_all') }}</span>
                            </div>
                            <select class="form-control select2 {{ $errors->has('permissions') ? 'is-invalid' : '' }}" name="permissions[]" id="permissions" multiple required>
                                @foreach($permissions as $id => $permission)
                                    <option value="{{ $id }}" {{ in_array($id, old('permissions', [])) ? 'selected' : '' }}>{{ $permission }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('permissions'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('permissions') }}
                                </div>
                            @endif
                            <div class="form-text text-muted">{{ trans('cruds.role.fields.permissions_helper') }}</div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-danger" type="submit">
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection