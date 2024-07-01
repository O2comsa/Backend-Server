@extends('Admin.layouts.app')

@section('page-title', trans('app.users'))
@section('page-heading', trans('app.users'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.users')</a>
    </li>
@endsection

@section('css_after')
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/sweetalert2/sweetalert2.min.css">
@stop

@section('content')
    <div class="col-md-12">
        <div class="block block-rounded block-bordered">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <small></small>
                </h3>
                <div class="col-md-3 ml-auto">
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        @lang('app.create_user')
                    </a>
                </div>
            </div>
            <div class="block-content">
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label for="user_status">@lang('app.user_status')</label>
                            <select class="form-control" id="user_status" name="user_status">
                                <option value="all">@lang('app.all')</option>
                                <option value="{{ \App\Helpers\UserStatus::ACTIVE }}">@lang('app.'.\App\Helpers\UserStatus::ACTIVE)</option>
                                <option value="{{ \App\Helpers\UserStatus::BANNED }}">@lang('app.'.\App\Helpers\UserStatus::BANNED)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-vcenter" id="UsersTable" style="width: 100%">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>@lang('app.name')</th>
                        <th>@lang('app.email')</th>
                        <th>@lang('app.user_status')</th>
                        <th>@lang('app.change_status')</th>
                        <th>@lang('app.created_at')</th>
                        <th>@lang('app.updated_at')</th>
                        <th>@lang('app.action')</th>
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
    <script src="{{url('/')}}/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page JS Plugins -->
    <script src="{{url('/')}}/js/plugins/es6-promise/es6-promise.auto.min.js"></script>
    <script src="{{url('/')}}/js/plugins/sweetalert2/sweetalert2.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#UsersTable').DataTable({
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{!! route('users.getUsers') !!}',
                columns: [
                    {data: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'status', name: 'status'},
                    {data: 'changeStatus', name: 'changeStatus', orderable: false},
                    {data: 'created_at', name: 'created_at', orderable: false,},
                    {data: 'updated_at', name: 'updated_at', orderable: false,},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });
        $(document).on('change', '.form-control', function (e) {
            var user_status = $('#user_status').val();
            $('#UsersTable').DataTable().ajax.url('{!! route('users.getUsers') !!}' + '/' + user_status).load();
        });
    </script>

    <script type="text/javascript">
        $(document).on('click', '#adminDelete', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            swal.fire({
                title: "{{ trans('app.Are you sure?') }}",
                text: "{{ trans('app.You will not be able to recover this!') }}",
                type: "warning",
                showCancelButton: !0,
                confirmButtonClass: "btn btn-danger m-1",
                cancelButtonClass: "btn btn-secondary m-1",
                confirmButtonText: "{{ trans('app.Yes, delete it!') }}",
                cancelButtonText: "{{ trans('app.close!') }}",
                html: !1,
                preConfirm: function (e) {
                    return new Promise(function (e) {
                        setTimeout(function () {
                            e()
                        }, 50)
                    })
                }
            }).then(function (e) {
                if (e.value) {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "POST",
                        url: "{{route('users.destroy')}}",
                        data: {id: id},
                        success: function (data) {
                            swal.fire({title: "{{trans('app.Deleted!')}}", text: "{{ trans('app.has been deleted.') }}", type: "success", confirmButtonText: "{{trans('app.success')}}"})
                            window.location.href = "";
                        }
                    });
                } else {
                    swal.fire({title: "{{trans('app.cancelled')}}", text: "{{trans('app.delete_canceled')}}", type: "error", confirmButtonText: "{{trans('app.close')}}"})
                }
            })
        });

        $(document).on('change', '#changeStatus', function (e) {
            e.preventDefault();
            var id = $(this).children().data('id');
            var status = $(this).children('option:selected').val();
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: "{{route('users.changeStatus')}}",
                data: {id: id, status: status},
                success: function (data) {
                    {{--swal.fire({title : "{{trans('app.Deleted!')}}", text: "{{ trans('app.has been deleted.') }}", type: "success",confirmButtonText: "{{trans('app.success')}}"})--}}
                        window.location.href = "";
                }
            });
        });
    </script>
@stop

