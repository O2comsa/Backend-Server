@extends('Admin.layouts.app')

@section('page-title', trans('app.transactions'))
@section('page-heading', trans('app.transactions'))

    @section('breadcrumb')
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.transactions')</a>
        </li>
    @endsection 

    @section('css_after')
        @parent
        <link rel="stylesheet" href="{{ url('/') }}/js/plugins/datatables/dataTables.bootstrap4.css">
        <link rel="stylesheet" href="{{ url('/') }}/js/plugins/sweetalert2/sweetalert2.min.css">
        <link rel="stylesheet" href="{{ url('/') }}/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
        <link rel="stylesheet" href="{{ url('/') }}/js/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
        <link rel="stylesheet" href="{{ url('/') }}/js/plugins/flatpickr/flatpickr.min.css">
    @stop

    @section('content')
        <div class="col-md-12">
            <div class="block block-rounded block-bordered">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <small></small>
                    </h3>
                    <div class="block-options">
                        <button id="exportButton" class="btn btn-success">@lang('app.export')</button>
                    </div>
                </div>
                <div class="block-content">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label for="from_date">@lang('app.from_date')</label>
                                <input type="text" autocomplete="off" class="js-datepicker form-control" id="from_date" name="from_date" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="to_date">@lang('app.to_date')</label>
                                <input type="text" autocomplete="off" class="js-datepicker form-control" id="to_date" name="to_date" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd">
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-vcenter" id="transactionsTable" style="width: 100%">
                        <thead class="thead-dark">
                        <tr>
                            <th></th>
                            <th>@lang('app.user')</th>
                            <th>@lang('app.amount')</th>
                            <th>@lang('app.note')</th>
                            <th>@lang('app.order_date')</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @stop

    @section('js_after')
        @parent
        <script src="{{ url('/') }}/js/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="{{ url('/') }}/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="{{ url('/') }}/js/plugins/es6-promise/es6-promise.auto.min.js"></script>
        <script src="{{ url('/') }}/js/plugins/sweetalert2/sweetalert2.min.js"></script>
        <script src="{{ url('/') }}/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="{{ url('/') }}/js/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
        <script src="{{ url('/') }}/js/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
        <script src="{{ url('/') }}/js/plugins/flatpickr/flatpickr.min.js"></script>

        <script type="text/javascript">
            jQuery(function () {
                One.helpers(['flatpickr', 'datepicker']);
            });

            $(document).ready(function () {
        var table = $('#transactionsTable').DataTable({
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{!! route('getTransactions') !!}',
                data: function (d) {
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'user', name: 'user'},
                {data: 'in', name: 'in'},
                {data: 'note', name: 'note'},
                {data: 'created_at', name: 'created_at', orderable: false}
            ]
        });

        $('.js-datepicker').change(function() {
            console.log("From Date: " + $('#from_date').val());
            console.log("To Date: " + $('#to_date').val());
            table.draw();
        });

        $('#exportButton').click(function() {
        var from_date = $('#from_date').val() || 'all';
        var to_date = $('#to_date').val() || 'all';
        var search = table.search() || ''; // Retrieve the search term from the DataTable search input
        var exportUrl = '{{ route('exportTransactions', ['from_date' => 'from_date_param', 'to_date' => 'to_date_param', 'search' => 'search_param']) }}';
        exportUrl = exportUrl.replace('from_date_param', from_date)
                            .replace('to_date_param', to_date)
                            .replace('search_param', encodeURIComponent(search));
        window.location.href = exportUrl;
    });

    });

        </script>
    @stop
