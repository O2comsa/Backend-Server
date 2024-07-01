@extends('Admin.layouts.app')

@section('page-title', trans('app.permissions'))
@section('page-heading', $edit ? $permission->name : trans('app.create_new_permission'))

@section('content')

    @if ($edit)
        {!! Form::open(['route' => ['permission.update', $permission->id], 'method' => 'PUT', 'id' => 'permission-form']) !!}
    @else
        {!! Form::open(['route' => 'permission.store', 'id' => 'permission-form']) !!}
    @endif

    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">@lang('app.permission_details')</h3>
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
                            <input type="text" class="form-control" id="name" {{  $edit ? 'disabled' : '' }}
                                   name="name" placeholder="@lang('app.permission_name')" value="{{ $edit ? $permission->name : old('name') }}">
                        </div>
                        <div class="form-group">
                            <label for="display_name">@lang('app.display_name')</label>
                            <input type="text" class="form-control" id="display_name"
                                   name="display_name" placeholder="@lang('app.display_name')" value="{{ $edit ? $permission->display_name : old('display_name') }}">
                        </div>
                        <div class="form-group">
                            <label for="description">@lang('app.description')</label>
                            <textarea name="description" id="description" class="form-control">{{ $edit ? $permission->description : old('description') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 ml-auto">
                        <button type="submit" class="btn btn-primary">
                            {{ $edit ? trans('app.update_permission') : trans('app.create_permission') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Alternative Style -->
    </div>



@stop

