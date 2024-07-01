@extends('admin.layouts.app')

@section('page-title', trans('app.info_transaction'))
@section('page-heading', trans('app.info_transaction'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.info_transaction')</a>
    </li>
@endsection

@section('content')
    <!-- Page Content -->
    <div class="content">
        <!-- Invoice -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">#{{$paytabs->payment_reference}}</h3>
                <div class="block-options">
                    <!-- Print Page functionality is initialized in Helpers.print() -->
                    <button type="button" class="btn-block-option" onclick="One.helpers('print');">
                        <i class="si si-printer mr-1"></i> طباعة
                    </button>
                </div>
            </div>
            <div class="block-content">
                <div class="p-sm-4 p-xl-7">
                    <!-- Invoice Info -->
                    <div class="row mb-4">
                        <!-- Company Info -->
                        <div class="col-6 font-size-sm">
                            <p class="h3">@lang('app.bill_info')</p>
                            <address>
                                <strong>@lang('app.name'): </strong>{{ $paytabs->user->name ?? ''}}<br>
                                <strong>@lang('app.payment_reference'): </strong>{{ $paytabs->payment_reference ?? ''}}<br>
                                <strong>@lang('app.course'): </strong>{{ $paytabs->course->title ?? '' }}<br>
                            </address>
                        </div>
                    </div>
                    <!-- END Invoice Info -->

                    <p class="h3">@lang('app.create_response')</p>
                    <p style="direction: ltr">
                        <code>
                            @json($paytabs->create_response,true)
                        </code>
                    </p>


                    <p class="h3">@lang('app.response_payment')</p>
                    <p style="direction: ltr">
                        <code>
                            @json($paytabs->verify_payment_response,true)
                        </code>
                    </p>

                    <!-- Footer -->
                    <p class="font-size-sm text-muted text-center py-3 my-3 border-top">
                    </p>
                    <!-- END Footer -->
                </div>
            </div>
        </div>
        <!-- END Invoice -->
    </div>
    <br>
@stop
