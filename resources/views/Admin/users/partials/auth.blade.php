@if($edit)
    <input hidden name="user_id" value="{{$user->id}}">
@endif
<div class="form-group">
    <label for="email">@lang('app.email')</label>
    <input type="email" class="form-control" id="email"
           name="email" placeholder="@lang('app.email')" value="{{ $edit ? $user->email : old('email') }}">
</div>
{{--<div class="form-group">--}}
{{--    <label for="mobile">@lang('app.mobile')</label>--}}
{{--    <input type="number" class="form-control" id="mobile"--}}
{{--           name="mobile" placeholder="@lang('app.mobile')" value="{{ $edit ? $user->mobile : old('mobile') }}">--}}
{{--</div>--}}
<div class="form-group">
    <label for="national_id">رقم الهوية الوطنية</label>
    <input type="text" class="form-control" id="national_id"
           name="national_id" placeholder="1223345678" value="{{ $edit ? $user->national_id : old('national_id') }}">
</div>
<div class="form-group">
    <label for="password">{{ $edit ? trans("app.new_password") : trans('app.password') }}</label>
    <input type="password" class="form-control" id="password"
           name="password" @if ($edit) placeholder="@lang('app.leave_blank_if_you_dont_want_to_change')" @endif>
</div>
<div class="form-group">
    <label for="password_confirmation">{{ $edit ? trans("app.confirm_new_password") : trans('app.confirm_password') }}</label>
    <input type="password" class="form-control" id="password_confirmation"
           name="password_confirmation" @if ($edit) placeholder="@lang('app.leave_blank_if_you_dont_want_to_change')" @endif>
</div>
@if ($edit)
    <button type="submit" class="btn btn-primary mt-2" id="update-login-details-btn">
        <i class="fa fa-refresh"></i>
        @lang('app.update_details')
    </button>
@endif
