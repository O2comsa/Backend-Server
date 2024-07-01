@extends('Admin.layouts.app')
@if($edit)
    @section('page-title', trans('app.edit_plans'))
    @section('page-heading', trans('app.edit_plans'))

    @section('breadcrumb')
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.plans')</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.edit_plans')</a>
        </li>
    @endsection
@else
    @section('page-title', trans('app.add_plans'))
    @section('page-heading', trans('app.add_plans'))
    @section('breadcrumb')
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.plans')</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <a class="link-fx" href="">@lang('app.add_plans')</a>
        </li>
    @endsection
@endif

@section('content')
    @if($edit)
        {!! Form::open(['route' => ['plans.update', $plan->id], 'method' => 'PUT', 'files' => true, 'id' => 'plans-form']) !!}
    @else
        {!! Form::open(['route' => 'plans.store', 'files' => true, 'id' => 'plans-form']) !!}
    @endif
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">
                    @lang('app.info_plans')
                </h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-lg-4">
                        <p class="font-size-sm text-muted">
                            @lang('app.info_plans')
                        </p>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            @if($edit)
                                <input hidden name="plan_id" value="{{$plan->id}}">
                            @endif
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">@lang('app.name')</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="@lang('app.name')" value="{{ $edit ? $plan->name : old('name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="title">@lang('app.title')</label>
                                    <input type="text" class="form-control" id="title"
                                           name="title" placeholder="@lang('app.title')" value="{{ $edit ? $plan->title : old('title') }}">
                                </div>
                                <div class="form-group">
                                    <label for="description">@lang('app.description')</label>
                                    {!! Form::textarea('description', $edit ? $plan->description : old('description'),['class' => 'form-control', 'id' => 'description']) !!}
                                </div>

                                <div class="form-group">
                                    <label for="credit">@lang('app.credit')</label>
                                    {!! Form::number('credit', $edit ? $plan->credit : old('credit'),['class' => 'form-control', 'id' => 'credit']) !!}
                                </div>

                                <div class="form-group">
                                    <label for="period">@lang('app.period') / @lang('app.days')</label>
                                    {!! Form::number('period', $edit ? $plan->period : old('period'),['class' => 'form-control', 'id' => 'period']) !!}
                                </div>
                                <div class="form-group">
                                    <label for="price">@lang('app.price')</label>
                                    {!! Form::number('price', $edit ? $plan->price : old('price'),['class' => 'form-control', 'id' => 'price']) !!}
                                </div>
                                <div class="form-group">
                                    <label for="status">@lang('app.status')</label>
                                    {!! Form::select('status', $statuses, $edit ? $plan->status : old('status'),['class' => 'form-control', 'id' => 'status']) !!}
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-md-3 ml-auto">
                    <button type="submit" class="btn btn-primary">
                        @if($edit)
                            @lang('app.edit_plans')
                        @else
                            @lang('app.add_plans')
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    <br>
@stop
