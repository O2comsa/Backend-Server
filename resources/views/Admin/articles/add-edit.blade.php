@extends('Admin.layouts.app')
@if($edit)
    @section('page-title', trans('app.edit_articles'))
@section('page-heading', trans('app.edit_articles'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.articles')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.edit_articles')</a>
    </li>
@endsection
@else
    @section('page-title', trans('app.add_articles'))
@section('page-heading', trans('app.add_articles'))
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.articles')</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.add_articles')</a>
    </li>
@endsection
@endif

@section('content')
    @if($edit)
        {!! Form::open(['route' => ['articles.update', $article->id], 'method' => 'PUT', 'files' => true, 'id' => 'articles-form']) !!}
    @else
        {!! Form::open(['route' => 'articles.store', 'files' => true, 'id' => 'articles-form']) !!}
    @endif
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">
                    @lang('app.info_article')
                </h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-4">
                        <p class="font-size-sm text-muted">
                            @lang('app.info_article')
                        </p>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            @if($edit)
                                <input hidden name="article_id" value="{{$article->id}}">
                            @endif
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">@lang('app.title')</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="@lang('app.title')" value="{{ $edit ? $article->title : old('title') }}">
                                </div>
                                <div class="form-group">
                                    <label for="description">@lang('app.description')</label>
                                    {!! Form::textarea('description', $edit ? $article->description : old('description'), ['class' => 'form-control', 'id' => 'description' , 'placeholder' => '']) !!}
                                </div>
                                @if($edit)
                                    <div class="form-group">
                                        <label for="image">@lang('app.image')</label>
                                        <img width="500px" height="300px" src="{{  $article->image}}">
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="image">@lang('app.image')</label>
                                    <input {{ $edit ? '' : 'required' }}  type="file" class="form-control" name="image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 ml-auto">
                    <button type="submit" class="btn btn-primary">
                        @if($edit)
                            @lang('app.edit_articles')
                        @else
                            @lang('app.add_articles')
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    <br>
@stop
