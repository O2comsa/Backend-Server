@extends('Admin.layouts.app')

@section('page-title', trans('app.permissions'))
@section('page-heading', trans('app.permissions'))

@section('content')
    {!! Form::open(['route' => 'permission.save', 'class' => 'mb-4']) !!}
    <div class="col-md-12">
        <div class="block block-rounded block-bordered">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <small></small>
                </h3>
                <div class="float-right">
{{--                    <a href="{{ route('permission.create') }}" class="btn btn-primary btn-rounded">--}}
{{--                        <i class="fas fa-plus mr-2"></i>--}}
{{--                        @lang('app.add_permission')--}}
{{--                    </a>--}}
                </div>
            </div>
            <div class="block-content">
                <table class="table table-vcenter">
                    <thead class="thead-dark">
                    <tr>
                        <th class="min-width-200">@lang('app.name')</th>
                        @foreach ($roles as $role)
                            <th class="text-center min-width-100">{{ $role->display_name }}</th>
                        @endforeach
                        <th class="text-center min-width-100">@lang('app.action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ($permissions->count() > 0)
                        @foreach ($permissions as $permission)
                            <tr>
                                <td>{{ $permission->display_name ?: $permission->name }}</td>

                                @foreach ($roles as $role)
                                    <td class="text-center">
                                        <div class="custom-control custom-checkbox">
                                            {!!
                                                Form::checkbox(
                                                    "roles[{$role->id}][]",
                                                    $permission->id,
                                                    $role->hasPermission($permission->name),
                                                    [
                                                        'class' => 'custom-control-input',
                                                        'id' => "cb-{$role->id}-{$permission->id}"
                                                    ]
                                                )
                                            !!}
                                            <label class="custom-control-label d-inline"
                                                   for="cb-{{ $role->id }}-{{ $permission->id }}"></label>
                                        </div>
                                    </td>
                                @endforeach

                                <td class="text-center">
                                    @if(\Auth::user()->hasPermission('permissions.edit'))
                                        <a href="{{ route('permission.edit', $permission->id) }}" class="btn btn-icon"
                                           title="@lang('app.edit_permission')" data-toggle="tooltip" data-placement="top">
                                            <i class="fas fa-edit"></i>
                                        </a>
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
        @if ($permissions->count() > 0)
            <div class="row">
                <div class="col-md-3 ml-auto">
                    <button type="submit" class="btn btn-primary">
                        @lang('app.save_permissions')
                    </button>
                </div>
            </div>
        @endif

    </div>

    {!! Form::close() !!}
@stop
