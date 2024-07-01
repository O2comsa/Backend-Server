<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class PermissionsController extends Controller
{
    /**
     * PermissionsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:permissions.manage');
//        $this->middleware('permission:permissions.add', ['only' => ['create']]);
        $this->middleware('permission:permissions.edit', ['only' => ['edit']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //
        $roles = Role::all();
        $permissions = Permission::all();

        return view('Admin.permission.index', compact('roles', 'permissions'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        //
        $edit = false;
        return view('Admin.permission.add-edit', compact('edit'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request,[
            'name' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|unique:permissions,name'
        ]);

        Permission::create($request->all());
        return redirect()->route('permission.index')->withSuccess(trans('app.permission_created_successfully'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        //
        $edit = true;
        $permission = Permission::find($id);
        return view('Admin.permission.add-edit', compact('edit', 'permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request,[
            'name' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|unique:roles,name,'.$id
        ]);

        Permission::find($id)->update($request->all());
        return redirect()->route('permission.index')->withSuccess(trans('app.permission_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Permission::find($id)->delete();
        return redirect()->route('permission.index')
            ->withSuccess(trans('app.permission_deleted_successfully'));
    }

    /**
     * Update permissions for each role.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function saveRolePermissions(Request $request)
    {
        $roles = $request->get('roles');

        $allRoles = Role::pluck('id', 'id');

        foreach ($allRoles as $roleId) {
            $permissions = \Arr::get($roles, $roleId, []);
            $role = Role::find($roleId);
            $role->syncPermissions($permissions);
        }
        Cache::flush();

        return redirect()->route('permission.index')
            ->withSuccess(trans('app.permissions_saved_successfully'));
    }
}
