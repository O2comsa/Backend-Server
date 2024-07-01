@extends('Admin.layouts.app')
@if($edit)
    @section('page-title', trans('app.edit_courses'))
@section('page-heading', trans('app.edit_courses'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.courses')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.edit_courses')</a>
    </li>
@endsection
@else
    @section('page-title', trans('app.add_courses'))
@section('page-heading', trans('app.add_courses'))
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.courses')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.add_courses')</a>
    </li>
@endsection
@endif

@section('content')
    @if($edit)
        {!! Form::open(['route' => ['courses.update', $course->id], 'method' => 'PUT', 'files' => true, 'id' => 'courses-form']) !!}
    @else
        {!! Form::open(['route' => 'courses.store', 'files' => true, 'id' => 'courses-form']) !!}
    @endif
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">
                    @lang('app.info_courses')
                </h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-4">
                        <p class="font-size-sm text-muted">
                            @lang('app.info_courses')
                        </p>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            @if($edit)
                                <input hidden name="course_id" value="{{$course->id}}">
                            @endif
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">@lang('app.title')</label>
                                    <input type="text" class="form-control" id="title"
                                           name="title" placeholder="@lang('app.title')" value="{{ $edit ? $course->title : old('title') }}">
                                </div>
                                @if($edit)
                                    <div class="form-group">
                                        <label for="image">@lang('app.image')</label>
                                        <img width="500px" height="300px" src="{{  $course->image}}">
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="image">@lang('app.image')</label>
                                    <input {{ $edit ? '' : 'required' }}  type="file" class="form-control" name="image">
                                </div>

                                <div class="form-group">
                                    <label for="description">@lang('app.description')</label>
                                    {!! Form::textarea('description', $edit ? $course->description : old('description'),['class' => 'form-control', 'id' => 'description']) !!}
                                </div>
                                <div class="form-group">
                                    <label>@lang('app.related_courses')</label>
                                    @if($courses->count() > 0)
                                        @foreach($courses as $related_course)
                                            <div class="custom-control custom-checkbox mb-1" >
                                                <input class="custom-control-input" type="checkbox" value="{{ $related_course->id }}" id="related_course-{{ $related_course->id }}"
                                                       name="related_course[]" {{ $related_course->status != \App\Helpers\CourseStatus::ACTIVE ? 'disable' : '' }} {{ $edit && $course->courses->contains($related_course->id) ? 'checked' : ''}}>
                                                <label class="custom-control-label"
                                                       for="related_course-{{ $related_course->id }}"> {{ $related_course->title }} {{ $related_course->status != \App\Helpers\CourseStatus::ACTIVE ? (' - '. trans('app.disable')) : '' }}</label>
                                            </div>
                                        @endforeach
                                    @else
                                        <label class="form-check-label" for="example-checkbox-default1">@lang('app.no_related_courses')</label>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="d-block">@lang('app.is_free_courses')</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="is_free" name="free" value="1" {{ ($edit && $course->free == 1 ) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_free"> @lang('app.free_course')</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="is_paid" name="free" value="0" {{ ($edit && $course->free == 0 ) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_paid"> @lang('app.paid_course')</label>
                                    </div>
                                </div>
                                <div class="form-group" id="div_price" style="{{ ($edit && $course->free == 0) ? '' : 'display:none' }}">
                                    <label for="price">@lang('app.price')</label>
                                    {!! Form::number('price', $edit ? $course->price : old('price'),['class' => 'form-control', 'id' => 'price']) !!}
                                </div>
                                <div class="form-group">
                                    <label for="status">@lang('app.status')</label>
                                    {!! Form::select('status', $statuses, $edit ? $course->status : old('status'),['class' => 'form-control', 'id' => 'status']) !!}
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-md-3 ml-auto">
                    <button type="submit" class="btn btn-primary">
                        @if($edit)
                            @lang('app.edit_courses')
                        @else
                            @lang('app.add_courses')
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
    <script>
        $(document).on('click', '#is_free', function (e) {
            $('#div_price').hide();
        });
        $(document).on('click', '#is_paid', function (e) {
            $('#div_price').show();
        });
    </script>
@stop
