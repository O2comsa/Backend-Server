<div class="row">
    <div class="col-md-12">
        @if($edit)
            <input hidden name="user_id" value="{{$user->id}}">
        @endif
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">@lang('app.name')</label>
                <input type="text" class="form-control" id="name"
                       name="name" placeholder="@lang('app.name')" value="{{ $edit ? $user->name : old('name') }}">
            </div>
            <div class="form-group">
                <label for="status">@lang('app.status')</label>
                {!! Form::select('status', $statuses, $edit ? $user->status : old('status'),
                    ['class' => 'form-control', 'id' => 'status', $profile ? 'disabled' : '']) !!}
            </div>
        </div>
    </div>
</div>
<div class="row">
    @if ($edit)
        <div class="col-md-3 ml-auto">
            <button type="submit" class="btn btn-primary" id="update-details-btn">
                <i class="fa fa-refresh"></i>
                @lang('app.update_details')
            </button>
        </div>
    @endif
</div>
