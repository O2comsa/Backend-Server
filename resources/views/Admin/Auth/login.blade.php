@extends('Admin.layouts.auth')

@section('page-title', trans('app.login'))

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
                                    <h3 class="block-title">@lang('app.Sign_In')</h3>
                                    <div class="block-options">
                                        <a class="btn-block-option font-size-sm" href="{{ route('password.remind.get') }}">@lang('app.i_forgot_my_password')</a>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="p-sm-3 px-lg-4 py-lg-5">
                                        <h1 class="mb-2">{{ env('app_name') }}</h1>
                                        <p>@lang('app.login')</p>

                                        <!-- Sign In Form -->
                                        <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _es6/pages/op_auth_signin.js) -->
                                        <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                                        <form class="js-validation-signin" role="form" action="{{ route('admin.post.login') }}" method="POST" id="login-form" autocomplete="off">
                                            <input type="hidden" value="<?= csrf_token() ?>" name="_token">
                                            <div class="py-3">
                                                <div class="form-group">
                                                    <input type="email" class="form-control form-control-alt form-control-lg" id="email" name="email" {{ (env('APP_ENV') == 'local')? 'value=info@o2.com.sa':'' }}
                                                    placeholder="@lang('app.email_or_username')">
                                                </div>
                                                <div class="form-group">
                                                    <input type="password" class="form-control form-control-alt form-control-lg" id="password" name="password" {{ (env('APP_ENV') == 'local')? 'value=123456':'' }}
                                                    placeholder="@lang('app.password')">
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                                        <label class="custom-control-label font-w400" for="remember"> @lang('app.remember_me')</label>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-6 col-xl-5">
                                                    <button type="submit" class="btn btn-block btn-primary">
                                                        <i class="fa fa-fw fa-sign-in-alt mr-1"></i> @lang('app.log_in')
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
                    <strong>{{ env('APP_NAME') }}</strong> &copy; <span data-toggle="year-copy">{{date('YYYY')}}</span>
                </div>
            </div>
        </div>
    </main>
@stop

@section('js_after')
    <script type="text/javascript">
        $("#login-form").submit(function (e) {
            var $form = $(this);
            if (!$form.valid()) {
                return false;
            }
            as.btn.loading($("#btn-login"));
            return true;
        });
    </script>
    {!! JsValidator::formRequest('App\Http\Requests\Auth\LoginRequest', '#login-form') !!}
@stop
