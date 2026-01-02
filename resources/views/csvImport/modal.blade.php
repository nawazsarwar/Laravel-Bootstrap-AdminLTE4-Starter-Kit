<div class="modal fade" id="csvImportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">@lang('global.app_csvImport')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class='col-md-12'>

                        <form class="form-horizontal" method="POST" action="{{ route($route, ['model' => $model]) }}" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
                                <label for="csv_file" class="col-md-4 control-label">@lang('global.app_csv_file_to_import')</label>

                                <div class="col-md-6">
                                    <input id="csv_file" type="file" class="form-control-file" name="csv_file" required>

                                    @if($errors->has('csv_file'))
                                        <small class="form-text text-muted">
                                            <strong>{{ $errors->first('csv_file') }}</strong>
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="header" id="headerCheck" checked>
                                    <label class="form-check-label" for="headerCheck">@lang('global.app_file_contains_header_row')</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    @lang('global.app_parse_csv')
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>