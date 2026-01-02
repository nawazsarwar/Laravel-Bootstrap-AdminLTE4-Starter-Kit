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

    <!-- Select2 -->
    <link href="{{ asset('select2/select2.min.css') }}" rel="stylesheet" />

    <!-- Tempus Dominus (Bootstrap 5 DateTime Picker) -->
    <link href="{{ asset('datetimepicker/tempus-dominus.min.css') }}" rel="stylesheet" />

    <!-- DataTables Bootstrap 5 -->
    <link href="{{ asset('datatables/datatables.min.css') }}" rel="stylesheet" />

    <!-- Dropzone -->
    <link href="{{ asset('dropzone/dropzone.min.css') }}" rel="stylesheet" />

    <!-- AdminLTE 4 CSS -->
    <link href="{{ asset('adminlte4/css/adminlte.min.css') }}" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />

    @yield('styles')
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Header -->
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                            <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                                <i class="fas fa-bars"></i>
                            </a>
                    </li>
                </ul>

                <!-- Right navbar links -->
                <ul class="navbar-nav ms-auto">
                    @if(count(config('panel.available_languages', [])) > 1)
                        <li class="nav-item dropdown">
                                <a class="nav-link" data-bs-toggle="dropdown" href="#">
                                {{ strtoupper(app()->getLocale()) }}
                            </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                @foreach(config('panel.available_languages') as $langLocale => $langName)
                                    <a class="dropdown-item" href="{{ url()->current() }}?change_language={{ $langLocale }}">{{ strtoupper($langLocale) }} ({{ $langName }})</a>
                                @endforeach
                            </div>
                        </li>
                    @endif

                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-fw fa-user nav-icon"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                {{-- <li>
                                    <span class="dropdown-item-text">
                                        <i class="fas fa-fw fa-at nav-icon"></i> {{ auth()->user()->email }}
                                    </span>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li> --}}
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                                        <i class="fas fa-fw fa-sign-out-alt nav-icon"></i> {{ trans('global.logout') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </nav>

        @include('partials.menu')

        <div class="app-main">
            <div class="app-content">
                <div class="container-fluid">
            <!-- Main content -->
            <section class="content" style="padding-top: 20px">
                @if(session('message'))
                    <div class="row mb-2">
                        <div class="col-lg-12">
                            <div class="alert alert-success" role="alert">{{ session('message') }}</div>
                        </div>
                    </div>
                @endif
                @if($errors->count() > 0)
                    <div class="alert alert-danger">
                        <ul class="list-unstyled">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </section>
            <!-- /.content -->
                </div>
            </div>
        </div>

        <footer class="app-footer">
            <div class="float-end d-none d-sm-block">
                <b>Version</b> 4.0
            </div>
            <strong> &copy;</strong> {{ trans('global.allRightsReserved') }}
        </footer>

        <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="{{ asset('bootstrap5/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('select2/select2.min.js') }}"></script>

    <!-- Moment.js -->
    <script src="{{ asset('moment/moment.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('datatables/datatables.min.js') }}"></script>

    <!-- Tempus Dominus (DateTime Picker) -->
    <script src="{{ asset('datetimepicker/tempus-dominus.min.js') }}"></script>

    <!-- Dropzone - Commented out due to CommonJS module incompatibility -->
    <!-- <script src="{{ asset('dropzone/dropzone.min.js') }}"></script> -->

    <!-- CKEditor 5 -->
    <script src="{{ asset('ckeditor5/ckeditor.js') }}"></script>

    <!-- PDFMake -->
    <script src="{{ asset('pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('pdfmake/vfs_fonts.js') }}"></script>

    <!-- JSZip -->
    <script src="{{ asset('jszip/jszip.min.js') }}"></script>

    <!-- AdminLTE 4 JS -->
    <script src="{{ asset('adminlte4/js/adminlte.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/main.js') }}"></script>

    <script>
        $(function() {
  let copyButtonTrans = '{{ trans('global.datatables.copy') }}'
  let csvButtonTrans = '{{ trans('global.datatables.csv') }}'
  let excelButtonTrans = '{{ trans('global.datatables.excel') }}'
  let pdfButtonTrans = '{{ trans('global.datatables.pdf') }}'
  let printButtonTrans = '{{ trans('global.datatables.print') }}'
  let colvisButtonTrans = '{{ trans('global.datatables.colvis') }}'
  let selectAllButtonTrans = '{{ trans('global.select_all') }}'
  let selectNoneButtonTrans = '{{ trans('global.deselect_all') }}'

  let languages = {
                'en': '{{ asset('datatables/i18n/English.json') }}',
                'ru': '{{ asset('datatables/i18n/Russian.json') }}',
                'fr': '{{ asset('datatables/i18n/French.json') }}',
                'es': '{{ asset('datatables/i18n/Spanish.json') }}',
                'tr': '{{ asset('datatables/i18n/Turkish.json') }}',
                'ar': '{{ asset('datatables/i18n/Arabic.json') }}',
                'bn': '{{ asset('datatables/i18n/Bangla.json') }}',
                'zh-Hans': '{{ asset('datatables/i18n/Chinese.json') }}',
                'hi': '{{ asset('datatables/i18n/Hindi.json') }}'
  };

  $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, { className: 'btn' })
  $.extend(true, $.fn.dataTable.defaults, {
    language: {
      url: languages['{{ app()->getLocale() }}']
    },
    columnDefs: [{
        orderable: false,
        className: 'select-checkbox',
        targets: 0
    }, {
        orderable: false,
        searchable: false,
        targets: -1
    }],
    select: {
                    style: 'multi+shift',
      selector: 'td:first-child'
    },
    order: [],
    scrollX: true,
    pageLength: 100,
    dom: 'lBfrtip<"actions">',
    buttons: [
      {
        text: selectAllButtonTrans,
        className: 'btn-primary',
        action: function(e, dt) {
          e.preventDefault()
          dt.rows().deselect();
          dt.rows({ search: 'applied' }).select();
        }
      },
      {
        text: selectNoneButtonTrans,
        className: 'btn-primary',
        action: function(e, dt) {
          e.preventDefault()
          dt.rows({ selected: true }).deselect();
        }
      },
      {
        extend: 'copy',
        className: 'btn-default',
        text: copyButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'csv',
        className: 'btn-default',
        text: csvButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'excel',
        className: 'btn-default',
        text: excelButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'pdf',
        className: 'btn-default',
        text: pdfButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'print',
        className: 'btn-default',
        text: printButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'colvis',
        className: 'btn-default',
        text: colvisButtonTrans,
        exportOptions: {
          columns: ':visible'
        }
      }
    ]
  });

  $.fn.dataTable.ext.classes.sPageButton = '';
});
    </script>

    <script>
        $(document).ready(function() {
    $('.searchable-field').select2({
        minimumInputLength: 3,
        ajax: {
            url: '{{ route("admin.globalSearch") }}',
            dataType: 'json',
            type: 'GET',
            delay: 200,
            data: function (term) {
                return {
                    search: term
                };
            },
            results: function (data) {
                return {
                    data
                };
            }
        },
        escapeMarkup: function (markup) { return markup; },
        templateResult: formatItem,
        templateSelection: formatItemSelection,
                placeholder: '{{ trans('global.search') }}...',
        language: {
            inputTooShort: function(args) {
                var remainingChars = args.minimum - args.input.length;
                var translation = '{{ trans('global.search_input_too_short') }}';
                return translation.replace(':count', remainingChars);
            },
            errorLoading: function() {
                return '{{ trans('global.results_could_not_be_loaded') }}';
            },
            searching: function() {
                return '{{ trans('global.searching') }}';
            },
            noResults: function() {
                return '{{ trans('global.no_results') }}';
            },
        }
    });

    function formatItem (item) {
        if (item.loading) {
            return '{{ trans('global.searching') }}...';
        }
        var markup = "<div class='searchable-link' href='" + item.url + "'>";
        markup += "<div class='searchable-title'>" + item.model + "</div>";
        $.each(item.fields, function(key, field) {
            markup += "<div class='searchable-fields'>" + item.fields_formated[field] + " : " + item[field] + "</div>";
        });
        markup += "</div>";
        return markup;
    }

    function formatItemSelection (item) {
        if (!item.model) {
            return '{{ trans('global.search') }}...';
        }
        return item.model;
    }

    $(document).delegate('.searchable-link', 'click', function() {
        var url = $(this).attr('href');
        window.location = url;
    });
});
    </script>

    @yield('scripts')
</body>

</html>
