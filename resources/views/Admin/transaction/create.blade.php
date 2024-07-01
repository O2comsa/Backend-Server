@extends('Admin.layouts.app')

@section('page-title', trans('app.add_transaction'))
@section('page-heading', trans('app.add_transaction'))

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <a class="link-fx" href="">@lang('app.add_transaction')</a>
    </li>
@endsection


@section('css_after')
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/select2/css/select2.min.css">
@stop

@section('content')
    {!! Form::open(['route' => 'transactions.store', 'files' => false, 'id' => 'transactions-form']) !!}
    <!-- Page Content -->
    <div class="content">
        <!-- Alternative Style -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">
                    @lang('app.info_transactions')
                </h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="type">العملية</label>
                            <select class="form-control" id="type" name="type">
                                <option value="in">ايداع</option>
                                <option value="out">سحب</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="user_id">@lang('app.user')</label>
                            <select class="js-select2 form-control" id="user_id" name="user_id">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" >{{ $user->name  .' | ' . $user->email .' | ' . $user->mobile }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="value" class="sr-only">@lang('app.value')</label>
                            <input type="number" name="value" id="value" class="form-control" placeholder="@lang('app.value')">
                        </div>
                    </div>
                </div>
                <div class="col-md-3 ml-auto">
                    <button type="submit" class="btn btn-primary">
                        @lang('app.create_transactions')
                    </button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    <br>
@stop

@section('js_after')
    <script src="{{url('/')}}/js/plugins/select2/js/select2.full.min.js"></script>
    <script>
        jQuery(function () {
            One.helpers(['select2']);
        });
    </script>
@stop
