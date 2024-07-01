@extends('Admin.layouts.app')

@section('page-title', trans('app.roles'))
@section('page-heading', trans('app.roles'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.roles')</a>
    </li>
@endsection

@section('css_after')
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/sweetalert2/sweetalert2.min.css">
@stop

@section('content')

    <div class="col-md-12">
        <div class="block block-rounded block-bordered">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <small></small>
                </h3>
                <div class="float-right">
                    <a href="{{ route('role.create') }}" class="btn btn-primary btn-rounded">
                        <i class="fas fa-plus mr-2"></i>
                        @lang('app.add_role')
                    </a>
                </div>
            </div>

            <div class="block-content">
                <table class="table table-vcenter">
                    <thead class="thead-dark">
                    <tr>
                        <th class="min-width-100">@lang('app.name')</th>
                        <th class="min-width-150">@lang('app.display_name')</th>
                        <th class="min-width-150">@lang('app.users_with_this_role')</th>
                        <th class="text-center">@lang('app.action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ($roles->count() > 0)
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->display_name }}</td>
                                <td>{{ $role->users->count() }}</td>
                                <td class="text-center">
                                    @if(\Auth::user()->hasPermission('roles.edit'))
                                        <a href="{{ route('role.edit', $role->id) }}" class="btn btn-icon"
                                           title="@lang('app.edit_role')" data-toggle="tooltip" data-placement="top">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if(\Auth::user()->hasPermission('roles.delete') && $roles->count() > 1 && $role->name != 'super_admin')
                                        <button name="Delete" id="Delete" data-id="{{ $role->id }}">
                                            <i class="fas fa-trash"></i> @lang('app.delete_role')
                                        </button>

                                        {{--                                            <a href="{{ route('role.destroy', $role->id) }}" class="btn btn-icon"--}}
                                        {{--                                           title="@lang('app.delete_role')"--}}
                                        {{--                                           data-toggle="tooltip"--}}
                                        {{--                                           data-placement="top"--}}
                                        {{--                                           data-method="DELETE"--}}
                                        {{--                                           data-confirm-title="@lang('app.please_confirm')"--}}
                                        {{--                                           data-confirm-text="@lang('app.are_you_sure_delete_role')"--}}
                                        {{--                                           data-confirm-delete="@lang('app.yes_delete_it')">--}}
                                        {{--                                            <i class="fas fa-trash"></i>--}}
                                        {{--                                        </a>--}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4"><em>@lang('app.no_records_found')</em></td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>


        </div>
    </div>

@stop


@section('js_after')
    <script src="{{url('/')}}/js/plugins/es6-promise/es6-promise.auto.min.js"></script>
    <script src="{{url('/')}}/js/plugins/sweetalert2/sweetalert2.min.js"></script>

    <script type="text/javascript">
        $(document).on('click', '#Delete', function (e) {
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
                        url: "{{route('role.delete')}}",
                        data: {id: id},
                        success: function (data) {
                            swal.fire({title : "{{trans('app.Deleted!')}}", text: "{{ trans('app.has been deleted.') }}", type: "success",confirmButtonText: "{{trans('app.success')}}"})
                            window.location.href = "";
                        }
                    });
                } else {
                    swal.fire({title : "{{trans('app.cancelled')}}", text: "{{trans('app.delete_canceled')}}", type: "error",confirmButtonText: "{{trans('app.close')}}"})
                }
            })
        });

    </script>
@stop
