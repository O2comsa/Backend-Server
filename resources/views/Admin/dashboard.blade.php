@extends('Admin.layouts.app')

@section('page-title', trans('app.dashboard'))
@section('page-heading', trans('app.dashboard'))

@section('content')
    <div class="bg-image overflow-hidden" {{--style="background-image: url('{{url('/') }}/media/photos/SpecialOffers-1.jpg');"--}}>
        <div class="bg-primary-dark-op">
            <div class="content content-narrow content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mt-5 mb-2 text-center text-sm-left">
                    <div class="flex-sm-fill">
                        <h1 class="font-w600 text-white mb-0 invisible" data-toggle="appear">@lang('app.dashboard')</h1>
                        <br>
                        <h2 class="h4 font-w400 text-white-75 mb-0 invisible" data-toggle="appear"
                            data-timeout="250">@lang('app.welcome') {{ strtoupper(Auth::guard('admin')->User()->name)}}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Hero -->
    <!-- Page Content -->
    <div class="content content-narrow">
        <div class="row">
            <div class="col-6 col-md-3 col-lg-6 col-xl-3">
                <a class="block block-rounded block-link-pop border-left border-primary border-4x" href="javascript:void(0)">
                    <div class="block-content block-content-full">
                        <div class="font-size-sm font-w600 text-uppercase text-muted">@lang('app.total_users')</div>
                        <div class="font-size-h2 font-w400 text-dark">{{ number_format($usersStats['total']) }}</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-6 col-xl-3">
                <a class="block block-rounded block-link-pop border-left border-primary border-4x" href="javascript:void(0)">
                    <div class="block-content block-content-full">
                        <div class="font-size-sm font-w600 text-uppercase text-muted">@lang('app.new_users_this_month')</div>
                        <div class="font-size-h2 font-w400 text-dark">{{ number_format($usersStats['new']) }}</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-6 col-xl-3">
                <a class="block block-rounded block-link-pop border-left border-primary border-4x" href="javascript:void(0)">
                    <div class="block-content block-content-full">
                        <div class="font-size-sm font-w600 text-uppercase text-muted">@lang('app.active_users')</div>
                        <div class="font-size-h2 font-w400 text-dark">{{ number_format($usersStats['active']) }}</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg-6 col-xl-3">
                <a class="block block-rounded block-link-pop border-left border-primary border-4x" href="javascript:void(0)">
                    <div class="block-content block-content-full">
                        <div class="font-size-sm font-w600 text-uppercase text-muted">@lang('app.banned_users')</div>
                        <div class="font-size-h2 font-w400 text-dark">{{ number_format($usersStats['banned']) }}</div>
                    </div>
                </a>
            </div>


            <div class="col-6 col-md-3 col-lg-6 col-xl-3">
                <a class="block block-rounded block-link-pop border-left border-primary border-4x" href="javascript:void(0)">
                    <div class="block-content block-content-full">
                        <div class="font-size-sm font-w600 text-uppercase text-muted">@lang('app.transaction_stats_total')</div>
                        <div class="font-size-h2 font-w400 text-dark">{{ number_format($TransactionStats['total']) }}</div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-3 col-lg-6 col-xl-3">
                <a class="block block-rounded block-link-pop border-left border-primary border-4x" href="javascript:void(0)">
                    <div class="block-content block-content-full">
                        <div class="font-size-sm font-w600 text-uppercase text-muted">@lang('app.transaction_stats_new')</div>
                        <div class="font-size-h2 font-w400 text-dark">{{ number_format($TransactionStats['new']) }}</div>
                    </div>
                </a>
            </div>

        </div>
        <!-- END Stats -->

        <div class="row">
            <div class="col-lg-12">
                <div class="block block-rounded block-mode-loading-oneui">
                    <div class="block-header">
                        <h3 class="block-title">@lang('app.users_registration_history')</h3>
                    </div>
                    <div class="block-content p-0 bg-body-light text-center">
                        <!-- Chart.js is initialized in js/pages/be_pages_dashboard.min.js which was auto compiled from _es6/pages/be_pages_dashboard.js) -->
                        <!-- For more info and examples you can check out http://www.chartjs.org/docs/ -->
                        <div class="pt-3" style="height: 360px;">
                            {!! $usersChart->container() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="block block-rounded block-mode-loading-oneui">
                    <div class="block-header">
                        <h3 class="block-title">@lang('app.transaction_history')</h3>
                    </div>
                    <div class="block-content p-0 bg-body-light text-center">
                        <!-- Chart.js is initialized in js/pages/be_pages_dashboard.min.js which was auto compiled from _es6/pages/be_pages_dashboard.js) -->
                        <!-- For more info and examples you can check out http://www.chartjs.org/docs/ -->
                        <div class="pt-3" style="height: 360px;">
                            {!! $transactionChart->container() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dashboard Charts -->
    </div>
    <!-- END Page Content -->
@stop



@section('js_after')
    {!! $usersChart->script() !!}
    {!! $transactionChart->script() !!}
    {{--    {!! $sumTransactionChart->script() !!}--}}

    <script src="{{url('/')}}/js/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
    <!-- Page JS Plugins -->
    <script src="{{url('/')}}/js/plugins/easy-pie-chart/jquery.easypiechart.min.js"></script>
    <script src="{{url('/')}}/js/plugins/chart.js/Chart.bundle.min.js"></script>
@stop
