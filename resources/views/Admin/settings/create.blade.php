@extends('Admin.layouts.app')

@if($edit)
    @section('page-title', trans('app.edit_setting'))
@section('page-heading', trans('app.edit_setting'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.general_settings ')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.edit_setting')</a>
    </li>
@endsection
@else
    @section('page-title', trans('app.add_settings'))
@section('page-heading', trans('app.add_settings'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.general_settings')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.add_settings')</a>
    </li>
@endsection
@endif


@section('css_after')
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/select2/css/select2.min.css">
@stop

@section('content')
    @if($edit)
        {!! Form::open(['route' => ['settings.update', $setting->id], 'method' => 'PUT', 'files' => false, 'id' => 'city-form']) !!}
    @else
        {!! Form::open(['route' => 'settings.store', 'files' => false, 'id' => 'city-form']) !!}
    @endif
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">
                    @lang('app.info_settings')
                </h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="setting_key">@lang('app.setting_key')</label>
                                    <input type="text" class="form-control" id="setting_key" name="key" placeholder="@lang('app.setting_key')" value="{{ $edit ? $setting->key : '' }}" {{ $edit ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="setting_display_name">@lang('app.display_name')</label>
                                    <input type="text" class="form-control" id="setting_display_name" name="display_name" placeholder="@lang('app.display_name')" value="{{ $edit ? $setting->display_name : '' }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="type">@lang('app.type')</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="text">نص</option>
                                    <option value="checkbox">خانة الاختيار (نعم,لا)</option>
                                    <option value="textarea">محتوى نصي كبير</option>
                                    <option value="number">رقم صحيح</option>
                                    <option value="decimal">رقم كسري</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 ml-auto">
                    <button type="submit" class="btn btn-primary">
                        @if($edit)
                            @lang('app.edit_setting')
                        @else
                            @lang('app.create_setting')
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    <br>
@stop

@section('js_after')
    <script src="{{url('/')}}/js/plugins/select2/js/select2.full.min.js"></script>
    <script>
        jQuery(function () {
            One.helpers(['select2']);
        });
    </script>

@stop
