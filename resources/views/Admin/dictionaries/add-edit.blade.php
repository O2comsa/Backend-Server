@extends('Admin.layouts.app')
@if($edit)
    @section('page-title', trans('app.edit_dictionaries'))
@section('page-heading', trans('app.edit_dictionaries'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.dictionaries')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.edit_dictionaries')</a>
    </li>
@endsection
@else
    @section('page-title', trans('app.add_dictionaries'))
@section('page-heading', trans('app.add_dictionaries'))
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.dictionaries')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.add_dictionaries')</a>
    </li>
@endsection
@endif

@section('content')
    @if($edit)
        {!! Form::open(['route' => ['dictionaries.update', $dictionary->id], 'method' => 'PUT', 'files' => true, 'id' => 'dictionaries-form']) !!}
    @else
        {!! Form::open(['route' => 'dictionaries.store', 'files' => true, 'id' => 'dictionaries-form']) !!}
    @endif
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">
                    @lang('app.info_dictionaries')
                </h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-4">
                        <p class="font-size-sm text-muted">
                            @lang('app.info_dictionaries')
                        </p>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            @if($edit)
                                <input hidden name="dictionary_id" value="{{$dictionary->id}}">
                            @endif
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">@lang('app.title')</label>
                                    <input type="text" class="form-control" id="title"
                                           name="title" placeholder="@lang('app.title')" value="{{ $edit ? $dictionary->title : old('title') }}">
                                </div>
                                @if($edit)
                                    <div class="form-group">
                                        <label for="image">@lang('app.image')</label>
                                        <img width="500px" height="300px" src="{{ $dictionary->image }}"  alt="">
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="image">@lang('app.image')</label>
                                    <input {{ $edit ? '' : 'required' }}  type="file" class="form-control" name="image">
                                </div>
                                <hr>
                                @if($edit)
                                    <div class="form-group">
                                        <label for="image">@lang('app.file_pdf')</label>
                                        <a href="{{$dictionary->file_pdf  }}">@lang('app.view')</a>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="file_pdf">@lang('app.file_pdf')</label>
                                    <input {{ $edit ? '' : 'required' }}  type="file" class="form-control" name="file_pdf">
                                </div>

                                <div class="form-group">
                                    <label for="description">@lang('app.description')</label>
                                    {!! Form::textarea('description', $edit ? $dictionary->description : old('description'),['class' => 'form-control', 'id' => 'description']) !!}
                                </div>

                                <div class="form-group">
                                    <label class="d-block">@lang('app.is_free_dictionary')</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="is_free" name="is_paid" value="0" {{ ($edit && $dictionary->is_paid == 0) || old('is_paid') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_free"> @lang('app.free_dictionary')</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="is_paid" name="is_paid" value="1" {{ ($edit && $dictionary->is_paid == 1 ) || old('is_paid') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_paid"> @lang('app.paid_dictionary')</label>
                                    </div>
                                </div>
                                <div class="form-group" id="div_price" style="{{ ($edit && $dictionary->is_paid || old('is_paid') == 1) ? '' : 'display:none' }}">
                                    <label for="price">@lang('app.price')</label>
                                    {!! Form::number('price', $edit ? $dictionary->price : old('price'),['class' => 'form-control', 'id' => 'price']) !!}
                                </div>
                                <div class="form-group">
                                    <label for="status">@lang('app.status')</label>
                                    {!! Form::select('status', $statuses, $edit ? $dictionary->status : old('status'),['class' => 'form-control', 'id' => 'status']) !!}
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-md-3 ml-auto">
                    <button type="submit" class="btn btn-primary">
                        @if($edit)
                            @lang('app.edit_dictionaries')
                        @else
                            @lang('app.add_dictionaries')
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
    <script>
        $(document).on('click', '#is_free', function (e) {
            $('#div_price').hide();
        });
        $(document).on('click', '#is_paid', function (e) {
            $('#div_price').show();
        });
    </script>
@stop
