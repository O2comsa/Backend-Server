@extends('Admin.layouts.app')
@if($edit)
    @section('page-title', trans('app.edit_live_support_request'))
    @section('page-heading', trans('app.edit_live_support_request'))

    @section('breadcrumb')
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.live_support_request')</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.edit_live_support_request')</a>
        </li>
    @endsection
@else
    @section('page-title', trans('app.add_live_support_request'))
    @section('page-heading', trans('app.add_live_support_request'))
    @section('breadcrumb')
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.live_support_request')</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.add_live_support_request')</a>
        </li>
    @endsection
@endif

@section('content')

    {!! Form::open(['route' => ['live-support-request.update', $liveSupportRequest->id], 'method' => 'PUT', 'files' => true, 'id' => 'live_support_request-form']) !!}

    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">
                    @lang('app.info_live_support_request')
                </h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-2">
                        <p class="font-size-sm text-muted">
                            @lang('app.info_live_support_request')
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            @if($edit)
                                <input hidden name="liveSupportRequest" value="{{$liveSupportRequest->id}}">
                            @endif
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="user">@lang('app.user')</label>
                                    <input type="text" class="form-control" id="user" disabled
                                           name="user" placeholder="@lang('app.user')"
                                           value="{{ $liveSupportRequest->user->name }}">
                                </div>

                                <div class="form-group">
                                    <label for="email">@lang('app.email')</label>
                                    <input type="text" class="form-control" id="email" disabled
                                           name="email" placeholder="@lang('app.email')"
                                           value="{{ $liveSupportRequest->user->email }}">
                                </div>

                                <hr>
                                <label for="admin_name">@lang('app.approved_by_admin')</label>

                                <div class="form-group">
                                    <label for="admin_email">@lang('app.admin_email')</label>
                                    <input type="text" class="form-control" id="admin_email" disabled
                                           name="admin_email" value="{{ $liveSupportRequest->admin?->email }}">
                                </div>


                                <div class="form-group">
                                    <label for="admin_name">@lang('app.admin_name')</label>
                                    <input type="text" class="form-control" id="admin_name" disabled
                                           name="admin_name" value="{{ $liveSupportRequest->admin?->name }}">
                                </div>

                                <div class="form-group">
                                    <label class="d-block">@lang('app.change_status') :
                                        ( {{ trans('app.'.$liveSupportRequest->status) }} )</label>
                                    @if($liveSupportRequest->status == \App\Helpers\LiveSupportRequestStatus::WAITING_STATUS)
                                        @foreach(\App\Helpers\LiveSupportRequestStatus::dashbaordLists() as $key => $list)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="status" name="status"
                                                       value="{{ $key }}">
                                                <label class="form-check-label" for="status"> {{ trans($list) }}</label>
                                            </div>
                                        @endforeach
                                    @elseif( in_array($liveSupportRequest->status,[\App\Helpers\LiveSupportRequestStatus::ACCEPTED_STATUS,\App\Helpers\LiveSupportRequestStatus::WAITING_STATUS,\App\Helpers\LiveSupportRequestStatus::IN_PROGRESS_STATUS]))
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="status" name="status"
                                                   value="{{ \App\Helpers\LiveSupportRequestStatus::COMPLETED_STATUS }}">
                                            <label class="form-check-label"
                                                   for="status"> {{ trans('app.'.\App\Helpers\LiveSupportRequestStatus::COMPLETED_STATUS) }}</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <label for="admin_name">@lang('app.zoom_info')</label>
                        @if( in_array($liveSupportRequest->status,[\App\Helpers\LiveSupportRequestStatus::ACCEPTED_STATUS,\App\Helpers\LiveSupportRequestStatus::WAITING_STATUS,\App\Helpers\LiveSupportRequestStatus::IN_PROGRESS_STATUS]) && $liveSupportRequest->meeting)
                            <div class="form-check form-check-inline">
                                <a class="form-check-label" href="{{ $liveSupportRequest->meeting->start_url }}">Start
                                    Zoom Link</a>
                            </div>
                            <div class="form-check form-check-inline">
                                <a class="form-check-label" href="{{ $liveSupportRequest->meeting->join_url }}">Join
                                    Zoom Link</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 ml-auto">
                    @if(!in_array($liveSupportRequest->status,[\App\Helpers\LiveSupportRequestStatus::COMPLETED_STATUS,\App\Helpers\LiveSupportRequestStatus::EXPIRED_STATUS]))
                        <button type="submit" class="btn btn-primary">
                            @if($edit)
                                @lang('app.edit_live_support_request')
                            @else
                                @lang('app.add_live_support_request')
                            @endif
                        </button>
                    @endif
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
