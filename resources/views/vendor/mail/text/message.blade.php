@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ env('APP_NAME') }}
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            حقوق النشر محفوظة لتطبيق إشارتي © {{ date('Y') }}  . بواسطة <i class="mdi mdi-heart text-danger"></i>  <a href="https://o2.com.sa/ar/" target="_blank" class="text-reset">أكسجين التقنية</a>.
        @endcomponent
    @endslot
@endcomponent
