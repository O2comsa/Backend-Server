<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\Admin;
use App\Notifications\ResetPasswordAdmin;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordRemindRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use Password;

class PasswordController extends Controller
{
    use ResetsPasswords;
    protected $guard = 'admin';
    protected $broker = 'admins';

    /**
     * Create a new password controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest:admin');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword()
    {
        return view('Admin.Auth.password.remind');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param PasswordRemindRequest $request
     * @param UserRepository $users
     * @return \Illuminate\Http\Response
     */
    public function sendPasswordReminder(PasswordRemindRequest $request)
    {
        $user = Admin::where('email', $request->email)->first();

        $token = Password::broker('admins')->getRepository()->create($user);

        $user->notify(new ResetPasswordAdmin($token));

        return redirect()->route('password.remind.get')->with('success', trans('app.password_reset_email_sent'));
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string $token
     * @return \Illuminate\Contracts\View\View
     * @throws NotFoundHttpException
     */
    public function getReset($token = null)
    {
        if (is_null($token)) {
            throw new NotFoundHttpException;
        }

        return view('Admin.Auth.password.reset')->with('token', $token);
    }

    /**
     * Reset the given user's password.
     *
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\Response|RedirectResponse
     */
    public function postReset(PasswordResetRequest $request)
    {
        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');
        $user = Admin::where('email', $request->email)->first();


        $response = Password::broker('admins')->reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case \Illuminate\Support\Facades\Password::PASSWORD_RESET:
                return redirect()->route('admin.login')->with('success', trans($response));

            default:
                return redirect()->back()->withInput($request->only('email'))->withErrors(['email' => trans($response)]);
        }
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = bcrypt($password);
        $user->save();
    }
}
