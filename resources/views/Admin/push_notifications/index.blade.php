@extends('Admin.layouts.app')

@section('page-title', trans('app.PushNotifications'))
@section('page-heading', trans('app.PushNotifications'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.PushNotifications')</a>
    </li>
@endsection

@section('content')
    {!! Form::open(['route' => 'pushNotifications.store', 'id' => 'PushNotifications-form']) !!}
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title"> @lang('app.settings')</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-8">
                        <div class="form-group">
                            <label for="start_day">@lang('app.title')</label>
                            {!! Form::text('title', '' , ['id' => 'title','placeholder'=> trans('app.title') , 'class' => 'form-control' , 'required' => true]) !!}
                        </div>
                        <div class="form-group">
                            <label for="start_day">@lang('app.message')</label>
                            {!! Form::textarea('message', '' , ['id' => 'message', 'rows' => 4, 'cols' => 54, 'style' => 'resize:none','placeholder'=> trans('app.message') , 'class' => 'form-control' , 'required' => true]) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 ml-auto">
                        <button type="submit" class="btn btn-primary">
                            @lang('app.send')
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <!-- END Alternative Style -->
    </div>

    {{ Form::close() }}
@stop
