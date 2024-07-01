@extends('Admin.layouts.app')

@section('page-title', trans('app.general_settings'))
@section('page-heading', trans('app.general_settings'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.general_settings')</a>
    </li>
@endsection

@section('content')
    {!! Form::open(['route' => 'settings.general.update', 'id' => 'general-settings-form']) !!}
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title"> @lang('app.settings')</h3>
                <div class="col-md-3 ml-auto">
                    <a href="{{ route('settings.create') }}" class="btn btn-primary">
                        @lang('app.settings_create')
                    </a>
                </div>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-6">
                        @foreach($settings as $key => $setting)
                            <div class="form-group">
                                <label for="{{ $setting->key }}">{{ $setting->display_name }}</label>
                                <a href="{{ route('settings.edit',$setting->id)  }}"> | @lang('app.edit')</a>
                                @if($setting->type == 'text')
                                    <input type="text" class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" placeholder="{{ $setting->display_name }}"
                                           value="{{$setting->value }}">
                                @elseif($setting->type == 'checkbox')
                                    <div class="custom-control custom-switch custom-control-lg mb-2">
                                        <input type="checkbox" name="{{$setting->key }}" id="checkbox-{{$setting->key }}" class="custom-control-input"
                                               value="{{$setting->value}}" {{ $setting->value  ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="checkbox-{{$setting->key }}">{{$setting->display_name }}</label>
                                    </div>
                                @elseif($setting->type == 'textarea')
                                    <textarea id="{{$setting->key }}" rows="4" cols="54" style="resize:none" placeholder="{{$setting->display_name }}" class="form-control"
                                              name="{{$setting->key }}">{{$setting->value }}</textarea>
                                @elseif($setting->type == 'number')
                                    <input type="number" class="form-control" id="{{$setting->key }}" name="{{$setting->key }}" placeholder="{{$setting->display_name }}"
                                           value="{{$setting->value }}">
                                @elseif($setting->type == 'decimal')
                                    <input type="number" step="any" class="form-control" id="{{$setting->key }}" name="{{$setting->key }}" placeholder="{{$setting->display_name }}"
                                           value="{{$setting->value }}">
                                @endif
                            </div>
                            <hr>
                        @endforeach
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 ml-auto">
                        <button type="submit" class="btn btn-primary">
                            @lang('app.update_settings')
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <!-- END Alternative Style -->
    </div>

    {{ Form::close() }}
@stop

@section('js_after')
    <script type="text/javascript">
        $('[id^="checkbox-"]').click(function () {
            if ($(this).is(':checked')) {
                //$('#form-Sub').show();
                $(this).val(1);
                console.log(12);
            } else {
                // $('#form-Sub').hide();
                $(this).val(0);
                console.log(13);

            }
        });
        // $(function () {
        //     $('#checkSub').click(function () {
        //         if ($(this).is(':checked')) {
        //             $('#form-Sub').show();
        //         } else {
        //             $('#form-Sub').hide();
        //         }
        //     });
        // });
    </script>
@stop
