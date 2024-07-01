@extends('Admin.layouts.auth')

@section('page-title', trans('app.reset_password'))

@section('content')
    <main id="main-container">
        <div class="bg-image" style="background-image: url('{{url('/')}}/media/photos/photo6@2x.jpg');">
            <div class="hero-static bg-white-95">
                <div class="content">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6 col-xl-4">
                            <!-- Sign In Block -->
                            <div class="block block-themed block-fx-shadow mb-0">
                                <div class="block-header">
                                    <h3 class="block-title"> @lang('app.forgot_your_password')</h3>
                                    <div class="block-options">
                                        <a class="btn-block-option font-size-sm" href="{{ route('login') }}">@lang('app.login')</a>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="p-sm-3 px-lg-4 py-lg-5">
                                        <h1 class="mb-2">{{ env('app_name') }}</h1>
                                        <p> @lang('app.forgot_your_password')</p>

                                        <!-- Sign In Form -->
                                        <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _es6/pages/op_auth_signin.js) -->
                                        <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                                        <form class="js-validation-signin" role="form" action="{{ route('password.remind.post') }}" method="POST" id="remind-password-form" autocomplete="off">
                                            <input type="hidden" value="<?= csrf_token() ?>" name="_token">

                                            <div class="py-3">
                                                <p class="text-muted mb-4 text-center font-weight-light">
                                                    @lang('app.please_provide_your_email_below')
                                                </p>
                                                <div class="form-group password-field my-3">
                                                    <label for="password" class="sr-only">@lang('app.email')</label>
                                                    <input type="email" name="email" id="email" class="form-control" placeholder="@lang('app.your_email')">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-6 col-xl-5">
                                                    <button type="submit" class="btn btn-block btn-primary">
                                                        <i class="fa fa-fw fa-sign-in-alt mr-1"></i>    @lang('app.reset_password')
                                                    </button>
                                                </div>
                                            </div>

                                        </form>
                                        <!-- END Sign In Form -->
                                    </div>
                                </div>
                            </div>

                            <!-- END Sign In Block -->
                        </div>
                    </div>
                </div>
                <div class="content content-full font-size-sm text-muted text-center">
                    <div class="row font-size-sm">
                        <div class="col-sm-12 order-sm-1 py-1 text-center text-sm-center">
                            <img style="max-width: 80px;max-height: 80px" src="{{ url('/assets/img/Logo-min.png') }}">
                            <br>
                            <strong>{{ env('APP_NAME') }}</strong> &copy; <span data-toggle="year-copy">{{date('YYYY')}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@stop

@section('js_after')
    {!! JsValidator::formRequest('App\Http\Requests\Auth\PasswordRemindRequest', '#remind-password-form') !!}
@stop
