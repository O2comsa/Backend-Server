@extends('Admin.layouts.app')
@if($edit)
    @section('page-title', trans('app.edit_lessons'))
@section('page-heading', trans('app.edit_lessons'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.lessons')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.edit_lessons')</a>
    </li>
@endsection
@else
    @section('page-title', trans('app.add_lessons'))
@section('page-heading', trans('app.add_lessons'))
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.lessons')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.add_lessons')</a>
    </li>
@endsection
@endif
@section('css_after')
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/select2/css/select2.min.css">
@stop
@section('content')
    @if($edit)
        {!! Form::open(['route' => ['lessons.update', $lesson->id], 'method' => 'PUT', 'files' => true, 'id' => 'lessons-form']) !!}
    @else
        {!! Form::open(['route' => 'lessons.store', 'files' => true, 'id' => 'lessons-form']) !!}
    @endif
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">
                    @lang('app.info_lessons')
                </h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-4">
                        <p class="font-size-sm text-muted">
                            @lang('app.info_lessons')
                        </p>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            @if($edit)
                                <input hidden name="lesson_id" value="{{$lesson->id}}">
                            @endif
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">@lang('app.title')</label>
                                    <input type="text" class="form-control" id="title"
                                           name="title" placeholder="@lang('app.title')" value="{{ $edit ? $lesson->title : old('title') }}">
                                </div>
                                @if($edit)
                                    <video width="320" height="240" controls>
                                        <source src="{{  $lesson->video}}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif

                                <div class="form-group">
                                    <label for="video">@lang('app.video')</label>
                                    <input {{ $edit ? '' : 'required' }}  type="file" class="form-control" name="video">
                                </div>

                                @if($edit)
                                    <div class="form-group">
                                        <label for="image">@lang('app.image')</label>
                                        <img width="500px" height="300px" src="{{  $lesson->image}}">
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="image">@lang('app.image')</label>
                                    <input {{ $edit ? '' : 'required' }}  type="file" class="form-control" name="image">
                                </div>

                                <div class="form-group">
                                    <label for="lesson_time">@lang('app.lesson_time')</label>
                                    {!! Form::text('lesson_time', $edit ? $lesson->lesson_time : old('lesson_time'),['class' => 'form-control', 'id' => 'lesson_time']) !!}
                                </div>
                                <div class="form-group">
                                    <label for="courses">@lang('app.courses')</label>
                                    {!! Form::select('course_id', $courses, $edit ? $lesson->course_id : old('course_id'),['class' => 'form-control select2', 'id' => 'course_id']) !!}
                                </div>
                                <div class="form-group">
                                    <label for="status">@lang('app.status')</label>
                                    {!! Form::select('status', $statuses, $edit ? $lesson->status : old('status'),['class' => 'form-control', 'id' => 'status']) !!}
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-md-3 ml-auto">
                    <button type="submit" class="btn btn-primary">
                        @if($edit)
                            @lang('app.edit_lessons')
                        @else
                            @lang('app.add_lessons')
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
