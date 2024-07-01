<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Helpers\UserStatus;
use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /// Register
    public function register(Request $request)
    {
        ApiHelper::validate($request, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'national_id' => 'nullable|unique:users,national_id',
            'profile_picture' => 'image',
            'mobile' => 'nullable|numeric|unique:users,mobile'
        ], [
            'email.unique' => ' البريد الالكتروني مستخدم مسبقاً، ارجو تسجيل الدخول أو استخدام لبريد الكتروني آخر لتسجيل جديد',
            'mobile.unique' => 'رقم الجوال مسجل مسبقاً، ارجو تسجيل الدخول أو استخدام رقم جوال آخر لتسجيل جديد',
        ]);

        $data = $request->only(['name', 'email', 'password', 'national_id', 'mobile']);

        if ($files = $request->file('profile_picture')) {
            $destinationPath = public_path('/upload/images/users'); // upload path
            $Image = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $Image);
            $data['profile_picture'] = $Image;
        }

        $user = User::create($data + ['status' => UserStatus::ACTIVE]);
        $token = $user->createToken(env('APP_NAME'))->accessToken;

        if ($request->has('device_token')) {
            $user->update([
                'device_token' => $request->get('device_token')
            ]);
        }

        return ApiHelper::output(['user' => $user, 'token' => $token]);
    }

    // Login
    public function login(Request $request)
    {
        ApiHelper::validate($request, ['email' => 'required|email', 'password' => 'required|min:6']);
        $email = $request->email;
        $password = $request->password;
        $user = User::where('email', $email)->first();
        if (empty($user)) {
            return ApiHelper::output(trans('app.login_error'), 0);
        }
        if ($user->status != UserStatus::ACTIVE) {
            $data = trans('app.cannot_login') . trans('app.' . $user->status);
            return ApiHelper::output($data, 0);
        }
        if (!Hash::check($password, $user->password)) {
            return ApiHelper::output(trans('app.login_error'), 0);
        }
        $token = $user->createToken(env('APP_NAME'))->accessToken;

        if ($request->has('device_token')) {
            $user->update([
                'device_token' => $request->get('device_token')
            ]);
        }

        return ApiHelper::output(['user' => $user, 'token' => $token]);
    }


    public function logout(Request $request)
    {
        ApiHelper::validate($request, [
            'user_id' => 'required',
        ]);
        $request->user()->token()->revoke();
        User::where('id', $request->user_id)->update(['device_token' => null]);
        return ApiHelper::output(trans('app.logout_succeed'));
    }

    public function profile(Request $request)
    {
        $user = User::find(\Auth::guard('api')->user()->id);
        return ApiHelper::output($user);
    }

    public function checkEmail(Request $request)
    {
        ApiHelper::validate($request, [
            'email' => 'required|email',
        ]);
        $exist = User::where('email', $request->email)->count();
        if ($exist > 0) {
            return ApiHelper::output(trans('app.email_exists'), 0);
        } else {
            return ApiHelper::output(trans('app.true_email'));
        }
    }

    public function updateProfile(Request $request)
    {
        ApiHelper::validate($request, [
            'email' => 'email|unique:users,email,' . \request()->user_id,
            'password' => 'min:6',
            'national_id' => 'nullable|unique:users,national_id,' . \request()->user_id,
            'profile_picture' => 'image',
            'mobile' => 'nullable|unique:users,mobile,' . \request()->user_id,
        ]);

        $data = $request->only(['name', 'email', 'mobile', 'device_token', 'national_id', 'mobile']);

        if ($request->hasFile('profile_picture') && $files = $request->file('profile_picture')) {
            $destinationPath = public_path('/upload/images/users'); // upload path
            $Image = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $Image);
            $data['profile_picture'] = $Image;
        }

        User::where('id', \Auth::guard('api')->user()->id)
            ->update($data);

        return ApiHelper::output(trans('app.profile_updated_succeed'));
    }
    
    
    public function deleteAccount(Request $request)
    {
        ApiHelper::validate($request, [
            'user_id' => 'required',
        ]);
        $request->user()->token()->revoke();
        User::where('id', $request->user_id)->forceDelete();

        return response()->json(['message' => 'Account deleted successfully']);
        
    }

    public function deleteAllWithDeletedAt(){
        User::whereNotNull('deleted_at')->forceDelete();
        return response()->json(['message' => 'All deleted accounts deleted successfully']);
    }

    public function updateProfilePicture(Request $request)
    {
        ApiHelper::validate($request, [
            'profile_picture' => 'image',
        ]);

        $data = $request->only(['profile_picture']);

        if ($request->hasFile('profile_picture') && $files = $request->file('profile_picture')) {
            $destinationPath = public_path('/upload/images/users'); // upload path
            $Image = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $Image);
            $data['profile_picture'] = $Image;
        }

        User::where('id', \Auth::guard('api')->user()->id)
            ->update($data);

        return ApiHelper::output(trans('app.profile_updated_succeed'));
    }


    public function registerDevice(Request $request)
    {
        ApiHelper::validate($request, ['user_id' => 'required', 'device_token' => 'required']);

        User::query()
            ->where('device_token', $request->token)
            ->where('id', '!=', $request->user_id)
            ->update(['device_token' => null]);

        $user = User::find(\Auth::guard('api')->user()->id)
            ->update([
                'device_token' => $request->device_token
            ]);

        return ApiHelper::output($user);
    }

    public static function revokeToken($user)
    {
        $userTokens = User::find($user->id)->tokens;
        foreach ($userTokens as $old_token) {
            $old_token->revoke();
        }
        User::find($user->id)->update(['device_token' => null]);
    }
}
