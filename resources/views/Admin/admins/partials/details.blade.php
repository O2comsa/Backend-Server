<div class="row">
    @if($edit)
        <input hidden id="admin_id" name="admin_id" value="{{$user->id}}">
    @endif
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">@lang('app.name')</label>
            <input type="text" class="form-control" id="name"
                   name="name" placeholder="@lang('app.name')" value="{{ $edit ? $user->name : '' }}">
        </div>
    </div>
    <div class="col-md-6">
        <label>@lang('app.roles')</label>
        @if($profile)
            <label>@lang('app.can_not_change_role')</label>
        @else
            @foreach($roles as $role)
                <div class="custom-control custom-checkbox custom-control-primary mb-1">
                    <input type="checkbox" class="custom-control-input" id="{{ $role->name }}" name="roles[]"
                           value="{{ $role->name }}" {{ $edit ?  ($user->hasRole($role->name) ? 'checked' : '') : '' }} >
                    <label class="custom-control-label" for="{{ $role->name }}">{{ $role->display_name }}</label>
                </div>
            @endforeach
        @endif
    </div>
    @if ($edit)
        <div class="col-md-3 ml-auto">
            <button type="submit" class="btn btn-primary" id="update-details-btn">
                <i class="fa fa-refresh"></i>
                @lang('app.update_details')
            </button>
        </div>
    @endif
</div>
