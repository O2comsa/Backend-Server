<div class="bg-body-light">
    <div class="content content-full">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <h1 class="flex-sm-fill h3 my-2">@yield('page-heading')</h1>
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb ">
                    <li class="breadcrumb-item">
                        <a class="link-fx" href="{{route('dashboard')}}">@lang('app.dashboard')</a>
                    </li>
                    @hasSection('breadcrumb')
                        @yield('breadcrumb')
{{--                        @for($i = 1 ; $i <= count(Request::segments()) ; $i++)--}}
{{--                            @if($i < count(Request::segments()) && $i > 0)--}}
{{--                                <li class="breadcrumb-item" aria-current="page">--}}
{{--                                    <a class="link-fx" href="#">{{ucwords(Str::replaceArray('-',[' '],Request::segment($i)))}}</a>--}}
{{--                                </li>--}}
{{--                            @else--}}
{{--                                <li class="breadcrumb-item active" aria-current="page">--}}
{{--                                    <a class="link-fx" href="">{{ucwords(Str::replaceArray('-',[' '],Request::segment($i)))}}</a>--}}
{{--                                </li>--}}
{{--                            @endif--}}
{{--                        @endfor--}}
                    @endif
                </ol>
            </nav>
        </div>
    </div>
</div>
