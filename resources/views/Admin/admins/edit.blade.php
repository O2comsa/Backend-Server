@extends('Admin.layouts.app')

@section('page-title', trans('app.edit_user'))
@section('page-heading', $user->name)
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.users')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.edit_user')</a>
    </li>
@endsection

@section('css_after')
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/select2/css/select2.min.css">
@stop

@section('content')
    <!-- Page Content -->
    <!-- Bootstrap Tabs (data-toggle="tabs" is initialized in Helpers.coreBootstrapTabs()) -->
    <div class="content">
        <!-- Block Tabs -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Block Tabs Alternative Style -->
                <div class="block">
                    <ul class="nav nav-tabs nav-tabs-alt" data-toggle="tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active"
                               id="details-tab"
                               data-toggle="tab"
                               href="#details"
                               role="tab"
                               aria-controls="home"
                               aria-selected="true">
                                <i class="fa fa-user"></i> @lang('app.user_details')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               id="authentication-tab"
                               data-toggle="tab"
                               href="#login-details"
                               role="tab"
                               aria-controls="home"
                               aria-selected="true">
                                <i class="fa fa-lock"></i> @lang('app.login_details')
                            </a>
                        </li>
                    </ul>
                    <div class="block-content tab-content">
                        <div class="tab-pane fade show active px-2" id="details" role="tabpanel" aria-labelledby="nav-home-tab">
                            {!! Form::open(['route' => ['admins.update.details', $user->id], 'method' => 'PUT', 'id' => 'details-form']) !!}
                            @include('Admin.admins.partials.details', ['profile' => false])
                            {!! Form::close() !!}
                        </div>

                        <div class="tab-pane fade px-2" id="login-details" role="tabpanel" aria-labelledby="nav-profile-tab">
                            {!! Form::open(['route' => ['admins.update.login-details', $user->id], 'method' => 'PUT', 'id' => 'login-details-form']) !!}
                            @include('Admin.admins.partials.auth')
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                <!-- END Block Tabs Alternative Style -->
            </div>

            <div class="col-lg-4">
                <div class="block">
                    {!! Form::open(['route' => ['admins.update.avatar', $user->id], 'files' => true, 'id' => 'avatar-form']) !!}
                    @include('Admin.admins.partials.avatar', ['updateUrl' => route('admins.update.avatar.external', $user->id)])
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@stop

@section('js_after')
    <script src="{{url('/')}}/js/plugins/select2/js/select2.full.min.js"></script>
    <script>
        jQuery(function () {
            One.helpers(['select2']);
        });
    </script>
    {{ HTML::style('assets/css/croppie.css') }}
    {{ HTML::script('assets/js/croppie.js') }}
    {{ HTML::script('assets/js/app.js') }}
    {!! HTML::script('assets/js/btn.js') !!}
    {!! HTML::script('assets/js/profile.js') !!}
    {!! JsValidator::formRequest('App\Http\Requests\Admin\UpdateDetailsRequest', '#details-form') !!}
    {!! JsValidator::formRequest('App\Http\Requests\Admin\UpdateLoginDetailsRequest', '#login-details-form') !!}

    <script>
        if("{{$edit}}"){
            $(document).ready(function(){
                $("#birth_date").val("{{$edit ? Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : ''}}");
            });
        }
    </script>
@stop
