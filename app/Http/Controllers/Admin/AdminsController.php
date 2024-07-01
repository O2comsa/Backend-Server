<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateDetailsRequest;
use App\Http\Requests\Admin\UpdateLoginDetailsRequest;
use App\Models\Admin;
use App\Models\Role;
use App\Services\Upload\AdminAvatarManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\Datatables\Datatables;

class AdminsController extends Controller
{
    /**
     * AdminsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admins.manage');
        $this->middleware('permission:admins.add', ['only' => ['create']]);
        $this->middleware('permission:admins.edit', ['only' => ['edit']]);
        $this->middleware('permission:admins.delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index()
    {
        return view('Admin.admins.list');
    }

    public function getAdmins()
    {
        return Datatables::of(Admin::latest())
            ->addIndexColumn()
            ->addColumn('action', function ($admin) {
                $action = '';
                if (\Auth::user()->hasPermission('admins.edit')) {
                    $action = '<a href="' . route('admins.edit', $admin->id) . '" class="btn btn-icon edit" title="' . trans('app.edit') . '" data-toggle="tooltip" data-placement="top"> <i class="fas fa-edit"></i></a>';
                }
                if (\Auth::user()->hasPermission('admins.delete')) {
                    $action .= '<button type="button" id="adminDelete" data-id="' . $admin->id . '" class="js-swal-confirm btn btn-light push mb-md-0" data-confirm-title="' . trans('app.delete') . '" data-confirm-text="' . trans('app.delete') . '" data-confirm-delete="' . trans('app.delete') . '" title="' . trans('app.delete') . '" data-toggle="tooltip" data-placement="top"><i class="fa fa-trash"></i></button>';
                }

                if ($admin->id == 1 || Admin::query()->count() == 1){
                    return  '';
                }

                return $action;
            })
            ->addColumn('created_at', function ($user) {
                return '<span>' . $user->created_at->diffForHumans() . '</span>';
            })
            ->addColumn('updated_at', function ($user) {
                return '<span>' . $user->updated_at->diffForHumans() . '</span>';
            })
            ->rawColumns(['action', 'created_at', 'updated_at'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $roles = Role::all();
        return view('Admin.admins.add',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateUserRequest $request
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $data = $request->all();
        $data['password'] = \Hash::make($request->password);
        $admin = Admin::create($data);
        $admin->syncRoles($request->roles);
        return redirect()->route('admins.list')->withSuccess(trans('app.admins_created'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $edit = true;
        $user = Admin::find($id);
        $roles = Role::all();
        return view('Admin.admins.edit', compact('edit', 'user','roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request)
    {
        Admin::find($request->id)->delete();
    }

    /**
     * Update user's avatar from uploaded image.
     *
     * @param AdminAvatarManager $avatarManager
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function updateAvatar(AdminAvatarManager $avatarManager, Request $request)
    {
        $this->validate($request, ['avatar' => 'image']);

        $name = $avatarManager->uploadAndCropAvatar(
            Admin::find($request->admin_id),
            $request->file('avatar'),
            $request->get('points')
        );

        if ($name) {
            Admin::find($request->admin_id)->update(['avatar' => $name]);
            return redirect()->route('admins.edit', $request->admin_id)->withSuccess(trans('app.avatar_changed'));
        }
        return redirect()->route('admins.edit', $request->admin_id)->withErrors(trans('app.avatar_not_changed'));
    }

    public function updateDetails(UpdateDetailsRequest $request)
    {
        $data = $request->except('admin_id');

        Admin::find($request->admin_id)->update($data);
        $admin = Admin::find($request->admin_id);
        $admin->syncRoles($request->roles);
        return redirect()->back()->withSuccess(trans('app.admin_updated'));
    }

    /**
     * Update user's avatar from some external source (Gravatar, Facebook, Twitter...)
     *
     * @param Request $request
     * @param AdminAvatarManager $avatarManager
     * @return mixed
     */
    public function updateAvatarExternal(Request $request, AdminAvatarManager $avatarManager)
    {
        $admin_id = $request->route('admin');
        $avatarManager->deleteAvatarIfUploaded(Admin::find($admin_id));

        Admin::find($admin_id)->update(['avatar' => $request->get('url')]);

        return redirect()->route('admins.edit', $admin_id)
            ->withSuccess(trans('app.avatar_changed'));
    }

    /**
     * Update user's login details.
     *
     * @param UpdateLoginDetailsRequest $request
     * @return mixed
     */
    public function updateLoginDetails(UpdateLoginDetailsRequest $request)
    {
        $data = $request->all();

        if (trim($data['password']) == '') {
            unset($data['password']);
            unset($data['password_confirmation']);
        }
        $data['password'] = \Hash::make($request->password);
        Admin::find($request->admin_id)->update($data);

        return redirect()->route('admins.edit', $request->admin_id)->withSuccess(trans('app.login_updated'));
    }
}
