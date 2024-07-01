<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UpdateProfileDetailsRequest;
use App\Http\Requests\Admin\UpdateProfileLoginDetailsRequest;
use App\Models\Admin;
use App\Services\Upload\AdminAvatarManager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * @var User
     */
    protected $theUser;

    /**
     * UsersController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $this->theUser = \Auth::user();
            return $next($request);
        });
    }

    /**
     * Display user's profile page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = $this->theUser;
        $edit = true;

        return view('Admin.admins.profile', compact('user', 'edit'));
    }

    /**
     * Update profile details.
     *
     * @param UpdateProfileDetailsRequest $request
     * @return mixed
     */
    public function updateDetails(UpdateProfileDetailsRequest $request)
    {
        Admin::find($request->admin_id)->update($request->except('role_id', 'status'));

        return redirect()->back()->withSuccess(trans('app.profile_updated_successfully'));
    }

    /**
     * Upload and update user's avatar.
     *
     * @param Request $request
     * @param AdminAvatarManager $avatarManager
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateAvatar(Request $request, AdminAvatarManager $avatarManager)
    {
        $this->validate($request, [
            'avatar' => 'image'
        ]);

        $name = $avatarManager->uploadAndCropAvatar(
            Admin::find($this->theUser->id),
            $request->file('avatar'),
            $request->get('points')
        );

        if ($name) {
            return $this->handleAvatarUpdate($name);
        }

        return redirect()->route('profile')
            ->withErrors(trans('app.avatar_not_changed'));
    }

    /**
     * Update avatar for currently logged in user
     * and fire appropriate event.
     *
     * @param $avatar
     * @return mixed
     */
    private function handleAvatarUpdate($avatar)
    {
        Admin::where('id',$this->theUser->id)->update(['avatar' => $avatar]);
        return redirect()->route('profile')->withSuccess(trans('app.avatar_changed'));
    }

    /**
     * Update user's avatar from external location/url.
     *
     * @param Request $request
     * @param AdminAvatarManager $avatarManager
     * @return mixed
     */
    public function updateAvatarExternal(Request $request, AdminAvatarManager $avatarManager)
    {
        $avatarManager->deleteAvatarIfUploaded(Admin::find($this->theUser->id));

        return $this->handleAvatarUpdate($request->get('url'));
    }

    /**
     * Update user's login details.
     *
     * @param UpdateProfileLoginDetailsRequest $request
     * @return mixed
     */
    public function updateLoginDetails(UpdateProfileLoginDetailsRequest $request)
    {
        $data = $request->except('role', 'status');

        // If password is not provided, then we will
        // just remove it from $data array and do not change it
        if (trim($data['password']) == '') {
            unset($data['password']);

            unset($data['password_confirmation']);
        }

        Admin::find($request->admin_id)->update($data);

        return redirect()->route('profile')->withSuccess(trans('app.login_updated'));
    }

}
