@extends('Admin.layouts.app')

@section('page-title', trans('app.my_profile'))
@section('page-heading', trans('app.my_profile'))

@section('content')
    <!-- Page Content -->
    <!-- Bootstrap Tabs (data-toggle="tabs" is initialized in Helpers.coreBootstrapTabs()) -->
    <div class="content">
        <div class="row">
            <div class="col-lg-8">
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
                            {!! Form::open(['route' => 'profile.update.details', 'method' => 'PUT', 'id' => 'details-form']) !!}
                            @include('Admin.admins.partials.details', ['profile' => true])
                            {!! Form::close() !!}
                        </div>
                        <div class="tab-pane fade px-2" id="login-details" role="tabpanel" aria-labelledby="nav-profile-tab">
                            {!! Form::open(['route' => 'profile.update.login-details', 'method' => 'PUT', 'id' => 'login-details-form']) !!}
                            @include('Admin.admins.partials.auth')
                            {!! Form::close() !!}
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        {!! Form::open(['route' => 'profile.update.avatar', 'files' => true, 'id' => 'avatar-form']) !!}
                        @include('Admin.admins.partials.avatar', ['updateUrl' => route('profile.update.avatar-external')])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js_after')
    <script src="{{ url('assets/js/app.js') }}"></script>
    {{ HTML::style('assets/css/croppie.css') }}
    {{ HTML::script('assets/js/croppie.js') }}
    {!! HTML::script('assets/js/btn.js') !!}
    {!! HTML::script('assets/js/profile.js') !!}
    {!! JsValidator::formRequest('App\Http\Requests\Admin\UpdateDetailsRequest', '#details-form') !!}
    {!! JsValidator::formRequest('App\Http\Requests\Admin\UpdateProfileLoginDetailsRequest', '#login-details-form') !!}
@stop
