@extends('Admin.layouts.app')

@section('page-title', trans('app.add_admins'))
@section('page-heading', trans('app.create_new_admins'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.users')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.add_admins')</a>
    </li>
@endsection


@section('css_after')
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/select2/css/select2.min.css">
@stop

@section('content')

    {!! Form::open(['route' => 'admins.store', 'files' => true, 'id' => 'user-form']) !!}
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">  @lang('app.user_details')</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-3">
                        <p class="font-size-sm text-muted">
                        @lang('app.user_general_information')
                        </p>
                    </div>
                    <div class="col-lg-8 col-xl-8">
                        @include('Admin.admins.partials.details', ['edit' => false, 'profile' => false])
                    </div>
                </div>
            </div>
        </div>

        <div class="block">
            <div class="block-header">
                <h3 class="block-title">   @lang('app.login_details')</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-4">
                        <p class="font-size-sm text-muted">
                        @lang('app.user_login_information')
                        </p>
                    </div>
                    <div class="col-lg-8 col-xl-5">
                        @include('Admin.admins.partials.auth', ['edit' => false])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 ml-auto">
            <button type="submit" class="btn btn-primary">
                @lang('app.create_user')
            </button>
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
    {!! HTML::script('assets/js/profile.js') !!}
    {!! JsValidator::formRequest('App\Http\Requests\Admin\CreateUserRequest', '#user-form') !!}
@stop
