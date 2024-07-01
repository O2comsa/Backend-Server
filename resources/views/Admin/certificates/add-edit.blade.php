@extends('Admin.layouts.app')
@if($edit)
    @section('page-title', trans('app.edit_certificates'))
    @section('page-heading', trans('app.edit_certificates'))

    @section('breadcrumb')
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.certificates')</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.edit_certificates')</a>
        </li>
    @endsection
@else
    @section('page-title', trans('app.add_certificates'))
    @section('page-heading', trans('app.add_certificates'))
    @section('breadcrumb')
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.certificates')</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.add_certificates')</a>
        </li>
    @endsection
@endif

@section('content')
    @if($edit)
        {!! Form::open(['route' => ['certificates.update', $certificate->id], 'method' => 'PUT', 'files' => true, 'id' => 'certificates-form']) !!}
    @else
        {!! Form::open(['route' => 'certificates.store', 'files' => true, 'id' => 'certificates-form']) !!}
    @endif
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">
                    @lang('app.info_certificates')
                </h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-4">
                        <p class="font-size-sm text-muted">
                            @lang('app.info_certificates')
                        </p>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="user_id">@lang('app.users')</label>
                                    {!! Form::select('user_id', $users, $edit ? $certificate->user_id : old('user_id'),['class' => 'form-control select2', 'id' => 'user_id']) !!}
                                </div>

                                <div class="form-group">
                                    <label for="duration_hours">@lang('app.duration_hours')</label>
                                    {!! Form::number('duration_hours', $edit ? $certificate->duration_hours : old('duration_hours'),['class' => 'form-control', 'id' => 'duration_hours']) !!}
                                </div>
                                <div class="form-group">
                                    <label for="duration_days">@lang('app.duration_days')</label>
                                    {!! Form::number('duration_days', $edit ? $certificate->duration_days : old('duration_days'),['class' => 'form-control', 'id' => 'duration_days']) !!}
                                </div>

                                <div class="form-group">
                                    <label for="start_date">@lang('app.start_date')</label>
                                    {!! Form::date('start_date', $edit ? $certificate->start_date : old('start_date'),['class' => 'form-control', 'id' => 'start_date']) !!}
                                </div>
                                <div class="form-group">
                                    <label for="end_date">@lang('app.end_date')</label>
                                    {!! Form::date('end_date', $edit ? $certificate->end_date : old('end_date'),['class' => 'form-control', 'id' => 'end_date']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="d-block">@lang('app.related_type')</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="is_LiveEvent" name="related_type"
                                               value="{{ \App\Models\LiveEvent::class }}" {{ ($edit && $certificate->related_type == \App\Models\LiveEvent::class ) || old('related_type') == \App\Models\LiveEvent::class ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_LiveEvent"> @lang('app.is_LiveEvent')</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="is_course" name="related_type"
                                               value="{{ \App\Models\Course::class }}" {{ ($edit && $certificate->related_type == \App\Models\Course::class ) || old('related_type') == \App\Models\Course::class  ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_course"> @lang('app.is_course')</label>
                                    </div>
                                </div>

                                <div class="form-group" id="live_event_div">
                                    <label for="live_event_id">@lang('app.live_events')</label>
                                    {!! Form::select('live_event_id', $liveEvents, $edit ? $certificate->related_id : old('related_id'),['class' => 'form-control', 'id' => 'live_event_id']) !!}
                                </div>

                                <div class="form-group" id="course_div">
                                    <label for="course_id">@lang('app.courses')</label>
                                    {!! Form::select('course_id', $courses, $edit ? $certificate->related_id : old('related_id'),['class' => 'form-control', 'id' => 'course_id']) !!}
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-3 ml-auto">
                    <button type="submit" class="btn btn-primary">
                        @if($edit)
                            @lang('app.edit_certificates')
                        @else
                            @lang('app.add_certificates')
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    <br>
@stop

@section('css_after')
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/select2/css/select2.min.css">
@stop
@section('js_after')
    <script src="{{url('/')}}/js/plugins/select2/js/select2.full.min.js"></script>
    <script>
        jQuery(function () {
            One.helpers(['select2']);
        });

        $('#user_id').select2({});

        $(document).ready(function () {
            @if($edit && $certificate->related_type == \App\Models\LiveEvent::class)
            is_LiveEvent();
            @elseif($edit && $certificate->related_type == \App\Models\Course::class)
            is_course();
            @endif
        });

        $(document).on('click', '#is_LiveEvent', function (e) {
            is_LiveEvent();
        });

        function is_LiveEvent() {
            $('#course_div').hide();
            $('#live_event_div').show();
            $('#course_id').disabled();

            $('#live_event_id').disabled(false);
        }

        $(document).on('click', '#is_course', function (e) {
            is_course();
        });

        function is_course() {
            $('#course_div').show();
            $('#live_event_div').hide();
            $('#live_event_id').disabled();

            $('#course_id').disabled(false);
        }
    </script>
@stop
