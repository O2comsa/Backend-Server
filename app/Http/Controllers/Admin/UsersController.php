<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SubscriptionStatus;
use App\Models\Category;
use App\Models\OTP;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\UserStatusChange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\UserStatus;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * UsersController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.manage');
        $this->middleware('permission:users.add', ['only' => ['create']]);
        $this->middleware('permission:users.edit', ['only' => ['edit']]);
        $this->middleware('permission:users.delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('Admin.users.list');
    }

    public function getUsers($user_status = null)
    {
        $users = User::query();

        if (isset($user_status) && $user_status != 'all') {
            $users = $users->where('status', $user_status);
        }

        $users = $users->latest();

        return Datatables::of($users)
            ->addIndexColumn()
            ->addColumn('action', function ($admin) {
                $action = '';
                if (\Auth::user()->hasPermission('users.edit')) {
                    $action = '<a href="' . route('users.edit', $admin->id) . '" class="btn btn-icon edit" title="' . trans('app.edit') . '" data-toggle="tooltip" data-placement="top"> <i class="fas fa-edit"></i></a>';
                }
                if (\Auth::user()->hasPermission('users.delete')) {
                    $action = $action . '<button type="button" id="adminDelete" data-id="' . $admin->id . '" class="js-swal-confirm btn btn-light push mb-md-0" data-confirm-title="' . trans('app.delete') . '" data-confirm-text="' . trans('app.delete') . '" data-confirm-delete="' . trans('app.delete') . '" title="' . trans('app.delete') . '" data-toggle="tooltip" data-placement="top"><i class="fa fa-trash"></i></button>';;
                }
                return $action;
            })
            ->addColumn('status', function ($user) {
                return trans('app.' . $user->status);
            })
            ->addColumn('changeStatus', function ($admin) {
                return '<select id="changeStatus"><option data-id="' . $admin->id . '" ' . (($admin->status == UserStatus::ACTIVE) ? 'selected' : '') . '  value="' . UserStatus::ACTIVE . '">' . trans('app.' . UserStatus::ACTIVE) .
                    '</option><option data-id="' . $admin->id . '" ' . (($admin->status == UserStatus::BANNED) ? 'selected' : '') . ' value="' . UserStatus::BANNED . '">' . trans('app.' . UserStatus::BANNED) . '</option></select>';
            })
            ->addColumn('created_at', function ($user) {
                return $user->created_at->diffForHumans();
            })
            ->addColumn('updated_at', function ($user) {
                return $user->updated_at->diffForHumans();
            })
            ->rawColumns(['changeStatus', 'status', 'action', 'created_at', 'updated_at'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $statuses = UserStatus::lists();
        return view('Admin.users.add', compact('statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        if (User::onlyTrashed()->where('email', $request->get('email'))->exists()) {
            User::onlyTrashed()->where('email', $request->get('email'))->restore();
            $user = User::where('email', $request->get('email'))->first();
            $request->request->add(['user_id' => $user->id]);
            $this->updateDetails($request);
            return redirect()->route('users.index')->withSuccess(trans('app.user_created'));
        }
        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'status' => 'required',
            'national_id' => 'required|unique:users,national_id',
        ]);

        $data = $request->only(['name', 'email', 'password', 'status','national_id']);
        User::create($data);
        return redirect()->route('users.index')->withSuccess(trans('app.user_created'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $edit = true;
        $statuses = UserStatus::lists();
        $user = User::where('id', $id)->first();
        return view('Admin.users.edit', compact('edit', 'user', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request)
    {
        User::find($request->id)->forceDelete();
    }


    public function updateDetails(Request $request)
    {
        $this->validate($request, [
            'email' => 'email|unique:users,email,' . $request->user_id,
            'national_id' => 'required|unique:users,national_id,' . \request()->user_id,
        ]);

        $data = $request->only(['name', 'email', 'mobile', 'status','national_id']);

        User::find($request->user_id)->update($data);
        return redirect()->back()->withSuccess(trans('app.user_updated'));
    }

    /**
     * Update user's login details.
     *
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateLoginDetails(Request $request)
    {
        $this->validate($request, [
            'email' => 'email|unique:users,email,' . $request->user_id,
            'national_id' => 'required|unique:users,national_id,' . \request()->user_id,
        ]);
        $data = $request->all();

//        if (trim($data['password']) == '') {
//            unset($data['password']);
//            unset($data['password_confirmation']);
//        }
//        $data['password'] = Hash::make($request->password);
        User::find($request->user_id)->update($data);

        return redirect()->route('users.edit', $request->user_id)->withSuccess(trans('app.login_updated'));
    }

    public function changeStatus(Request $request)
    {
        User::find($request->id)->update(['status' => $request->status]);
        \Notification::send(User::find($request->id), new \App\Notifications\UserStatusChange(User::find($request->id)));
    }

}
