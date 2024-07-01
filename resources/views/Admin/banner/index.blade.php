@extends('Admin.layouts.app')

@section('page-title', trans('app.banner'))
@section('page-heading', trans('app.banner'))

@section('css_after')
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/magnific-popup/magnific-popup.css">
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/sweetalert2/sweetalert2.min.css">
@stop

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.banner')</a>
    </li>
@endsection

@section('content')
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">
                </h3>
                <div class="col-md-3 ml-auto">
                    @if(Auth::user()->hasPermission('banner.add'))
                        <a href="{{ route('banner.create') }}" class="btn btn-primary">
                            @lang('app.add_banner')
                        </a>
                    @endif
                </div>
            </div>
            <div class="block-content block-content-full">
                <h2 class="content-heading">@lang('app.banner')</h2>
                <div class="row gutters-tiny items-push js-gallery push">
                    @if($banners->count())
                        @foreach($banners as $banner)
                            <div class="col-md-6 col-lg-4 col-xl-3 animated fadeIn">
                                <div class="options-container fx-item-rotate-r">
                                    <img class="img-fluid options-item" src="{{ $banner->image}}" alt="">
                                    <div class="options-overlay bg-black-75">
                                        <div class="options-overlay-content">
                                            {{--<h3 class="h4 font-w400 text-white mb-1">Image Caption</h3>--}}
                                            {{--<h4 class="h6 font-w400 text-white-75 mb-3">Some extra info</h4>--}}
                                            @if(Auth::user()->hasPermission('banner.edit'))
                                                <a class="btn btn-sm btn-primary" href="{{ route('banner.edit',$banner->id) }}">
                                                    <i class="fa fa-edit mr-1"></i> @lang('app.edit')
                                                </a>
                                            @endif
                                            <a class="btn btn-sm btn-primary img-lightbox" href="{{ $banner->image}}">
                                                <i class="fa fa-search-plus mr-1"></i> @lang('app.view')
                                            </a>
                                            @if(Auth::user()->hasPermission('banner.delete'))
                                                <button type="button" id="deleteBanner" data-id="{{$banner->id}}" class="js-swal-confirm btn btn-light push mb-md-0"
                                                        data-confirm-title="@lang('app.please_confirm')" data-confirm-text="@lang('app.are_you_sure_delete_user')"
                                                        data-confirm-delete="@lang('app.yes_delete_him')" title="@lang('app.delete_image')" data-toggle="tooltip" data-placement="top">
                                                    <i class="fa fa-trash"></i></button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <h1>@lang('app.not_found_banner')</h1>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <br>
@stop

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{url('/')}}/js/plugins/magnific-popup/jquery.magnific-popup.min.js"></script>
    <!-- Page JS Helpers (Magnific Popup Plugin) -->
    <script>jQuery(function () {
            One.helpers('magnific-popup');
        });
    </script>
    <!-- Page JS Plugins -->
    <script src="{{url('/')}}/js/plugins/es6-promise/es6-promise.auto.min.js"></script>
    <script src="{{url('/')}}/js/plugins/sweetalert2/sweetalert2.min.js"></script>

    <script type="text/javascript">
        $(document).on('click', '#deleteBanner', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            swal.fire({
                title: "{{ trans('app.Are you sure?') }}",
                text: "{{ trans('app.You will not be able to recover this!') }}",
                type: "warning",
                showCancelButton: !0,
                confirmButtonClass: "btn btn-danger m-1",
                cancelButtonClass: "btn btn-secondary m-1",
                confirmButtonText: "{{ trans('app.Yes, delete it!') }}",
                cancelButtonText: "{{ trans('app.close!') }}",
                html: !1,
                preConfirm: function (e) {
                    return new Promise(function (e) {
                        setTimeout(function () {
                            e()
                        }, 50)
                    })
                }
            }).then(function (e) {
                if (e.value) {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: "POST",
                        url: "{{route('banner.delete')}}",
                        data: {id: id},
                        success: function (data) {
                            swal.fire({
                                title: "{{trans('app.Deleted!')}}",
                                text: "{{ trans('app.has been deleted.') }}",
                                type: "success",
                                confirmButtonText: "{{trans('app.success')}}"
                            })
                            window.location.href = "";
                        }
                    });
                } else {
                    swal.fire({title: "{{trans('app.cancelled')}}", text: "{{trans('app.delete_canceled')}}", type: "error", confirmButtonText: "{{trans('app.close')}}"})
                }
            })
        });
    </script>
@stop
