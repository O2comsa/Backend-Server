@extends('Admin.layouts.app')

@section('page-title', trans('app.roles'))
@section('page-heading', $edit ? $role->name : trans('app.create_new_role'))

@section('content')

    @if ($edit)
        {!! Form::open(['route' => ['role.update', $role->id], 'method' => 'PUT', 'id' => 'role-form']) !!}
    @else
        {!! Form::open(['route' => 'role.store', 'id' => 'role-form']) !!}
    @endif
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title"> @lang('app.role_details_big')</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-4">
                        <p class="font-size-sm text-muted">
                        </p>
                    </div>
                    <div class="col-lg-8 col-xl-5">
                        <div class="form-group">
                            <label for="name">@lang('app.name')</label>
                            <input type="text" class="form-control" id="name"
                                   name="name" placeholder="@lang('app.role_name')" value="{{ $edit ? $role->name : old('name') }}">
                        </div>
                        <div class="form-group">
                            <label for="display_name">@lang('app.display_name')</label>
                            <input type="text" class="form-control" id="display_name"
                                   name="display_name" placeholder="@lang('app.display_name')" value="{{ $edit ? $role->display_name : old('display_name') }}">
                        </div>
                        <div class="form-group">
                            <label for="description">@lang('app.description')</label>
                            <textarea name="description" id="description" class="form-control">{{ $edit ? $role->description : old('description') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 ml-auto">
                        <button type="submit" class="btn btn-primary">
                            {{ $edit ? trans('app.update_role') : trans('app.create_role') }}
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <!-- END Alternative Style -->
    </div>

@stop

