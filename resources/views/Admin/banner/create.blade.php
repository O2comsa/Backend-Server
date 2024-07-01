@extends('Admin.layouts.app')

@if($edit)
    @section('page-title', trans('app.edit_banner'))
@section('page-heading', trans('app.edit_banner'))
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.banner')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.edit_banner')</a>
    </li>
@endsection
@else
    @section('page-title', trans('app.add_banner'))
@section('page-heading', trans('app.add_banner'))
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.banner')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.add_banner')</a>
    </li>
@endsection
@endif

@section('css_after')
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/magnific-popup/magnific-popup.css">
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/select2/css/select2.min.css">

@stop

@section('content')
    @if($edit)
        {!! Form::open(['route' => ['banner.update',$banner->id],'method'=>'PUT', 'files' => true, 'id' => 'banner-form']) !!}
    @else
        {!! Form::open(['route' => 'banner.store', 'files' => true, 'id' => 'banner-form']) !!}
    @endif
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-content block-content-full">
                @if($edit)
                    <div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                        <div class="form-group">
                            <img style="max-width: 700px" src="{{ $banner->image }}">
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <label for="">File Select</label>
                    <input {{ $edit ? '':'required' }} type="file" class="form-control" name="image">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        @if($edit)
                            @lang('app.edit_banner')
                        @else
                            @lang('app.add_banner')
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
    <!-- Page JS Plugins -->
    <script src="{{url('/')}}/js/plugins/magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="{{url('/')}}/js/plugins/select2/js/select2.full.min.js"></script>

    <!-- Page JS Helpers (Magnific Popup Plugin) -->
    <script>jQuery(function () {
            One.helpers('magnific-popup');
        });</script>
    <script>
        jQuery(function () {
            One.helpers(['select2']);
        });
    </script>
@stop
