@extends('Admin.layouts.app')
@if($edit)
    @section('page-title', trans('app.edit_live-event'))
    @section('page-heading', trans('app.edit_live-event'))

    @section('breadcrumb')
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.live-event')</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.edit_live-event')</a>
        </li>
    @endsection
@else
    @section('page-title', trans('app.add_live-event'))
    @section('page-heading', trans('app.add_live-event'))
    @section('breadcrumb')
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.live-event')</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.add_live-event')</a>
        </li>
    @endsection
@endif

@section('content')
    @if($edit)
        {!! Form::open(['route' => ['live-event.update', $liveEvent->id], 'method' => 'PUT', 'files' => true, 'id' => 'live-event-form']) !!}
    @else
        {!! Form::open(['route' => 'live-event.store', 'files' => true, 'id' => 'live-event-form']) !!}
    @endif
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">
                    @lang('app.info_live-event')
                </h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    {{-- <div class="col-lg-2">
                        <p class="font-size-sm text-muted">
                            @lang('app.info_live-event')
                        </p>
                    </div> --}}
                    <div class="col-lg-8 border p-2">
                        <div class="row">
                            @if($edit)
                                <input hidden name="liveEvent_id" value="{{$liveEvent->id}}">
                            @endif
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">@lang('app.name')</label>
                                    <input type="text" class="form-control" id="name"
                                           name="name" placeholder="@lang('app.name')"
                                           value="{{ $edit ? $liveEvent->name : old('name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="event_presenter">@lang('app.event_presenter')</label>
                                    <input type="text" class="form-control" id="event_presenter"
                                           name="event_presenter" placeholder="@lang('app.event_presenter')"
                                           value="{{ $edit ? $liveEvent->event_presenter : old('event_presenter') }}">
                                </div>
                                @if($edit)
                                    <div class="form-group">
                                        <label for="image">@lang('app.image')</label>
                                        <img width="500px" height="300px" src="{{ $liveEvent->image }}" alt="">
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="image">@lang('app.image')</label>
                                    <input {{ $edit ? '' : 'required' }}  type="file" class="form-control" name="image">
                                </div>
                                <hr>

                                <div class="form-group">
                                    <label for="description">@lang('app.description')</label>
                                    {!! Form::textarea('description', $edit ? $liveEvent->description : old('description'),['class' => 'form-control', 'id' => 'description']) !!}
                                </div>

                                <div class="form-group">
                                    <label for="duration_event">@lang('app.duration_event')</label>
                                    {!! Form::text('duration_event', $edit ? $liveEvent->duration_event : old('duration_event'),['class' => 'form-control', 'id' => 'duration_event']) !!}
                                </div>

                                <div class="form-group">
                                    <label for="event_at">@lang('app.event_at')</label>
                                    <input type="datetime-local" class="js-flatpickr form-control bg-white"
                                           style="direction: ltr"
                                           id="event_at" name="event_at" data-enable-time="true"
                                           data-week-start="1" data-autoclose="true" data-today-highlight="true"
                                           data-date-format="Y-m-d h:s a" placeholder="Y-m-d h:s a"
                                           value="{{ $edit ? $liveEvent->event_at : old('event_at',\Illuminate\Support\Carbon::now()->format('Y-m-d h:i a')) }}">
                                </div>

                                <div class="form-group">
                                    <label class="d-block">@lang('app.is_free_live_event')</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="is_free" name="is_paid"
                                               value="0" {{ ($edit && $liveEvent->is_paid == 0) || old('is_paid') ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                               for="is_free"> @lang('app.free_live_event')</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="is_paid" name="is_paid"
                                               value="1" {{ ($edit && $liveEvent->is_paid == 1 ) || old('is_paid') ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                               for="is_paid"> @lang('app.paid_live_event')</label>
                                    </div>
                                </div>
                                <div class="form-group" id="div_price"
                                     style="{{ ($edit && $liveEvent->is_paid || old('is_paid') == 1) ? '' : 'display:none' }}">
                                    <label for="price">@lang('app.price')</label>
                                    {!! Form::number('price', $edit ? $liveEvent->price : old('price'),['class' => 'form-control', 'id' => 'price']) !!}
                                </div>
                                <div class="form-group">
                                    <label for="number_of_seats">@lang('app.number_of_seats')</label>
                                    {!! Form::number('number_of_seats', $edit ? $liveEvent->number_of_seats : old('number_of_seats'),['class' => 'form-control', 'id' => 'number_of_seats']) !!}
                                </div>
                                <div class="form-group">
                                    <label for="status">@lang('app.status')</label>
                                    {!! Form::select('status', $statuses, $edit ? $liveEvent->status : old('status'),['class' => 'form-control', 'id' => 'status']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($edit)
                        <div class="col-lg-4 border p-2 rounded">
                            <label for="admin_name mt-2">@lang('app.zoom_info')</label>
                            <br>
                            @if($liveEvent->meeting)
                                <div class="form-check  mt-4">
                                    <a class="form-check-label" href="{{ $liveEvent->meeting->start_url }}">Start
                                        Zoom Link</a>
                                </div>
                                <hr>
                                <div class="form-check ">
                                    <a class="form-check-label" href="{{ $liveEvent->meeting->join_url }}">Join
                                        Zoom Link</a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-md-3 ml-auto ">
                    <button type="submit" class="btn btn-primary">
                        @if($edit)
                            @lang('app.editLiveEvent')
                        @else
                            @lang('app.addLiveEvent')
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
    <script src="{{url('/')}}/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page JS Plugins -->
    <script src="{{url('/')}}/js/plugins/es6-promise/es6-promise.auto.min.js"></script>
    <script src="{{url('/')}}/js/plugins/sweetalert2/sweetalert2.min.js"></script>

    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery.maskedinput/jquery.maskedinput.min.js') }}"></script>
    <script src="{{ asset('js/plugins/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        jQuery(function () {
            One.helpers(['flatpickr', 'datepicker']);
        });

        $(document).on('click', '#is_free', function (e) {
            $('#div_price').hide();
        });
        $(document).on('click', '#is_paid', function (e) {
            $('#div_price').show();
        });
    </script>
@stop

@section('css_after')
    @parent

    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/ion-rangeslider/css/ion.rangeSlider.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/dropzone/dist/min/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/flatpickr/flatpickr.min.css') }}">

    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
@stop
