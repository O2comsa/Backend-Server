<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\DocBlock\Tags\See;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:settings.manage');
    }

    /**
     * Display general settings page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $settings = Setting::all();
        return view('Admin.settings.index', compact('settings'));
    }

    public function create()
    {
        $edit = false;
        return view('Admin.settings.create', compact('edit'));
    }

    public function edit($id)
    {
        $edit = true;
        $setting = Setting::find($id);
        return view('Admin.settings.create', compact('edit', 'setting'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'key' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|unique:settings,key',
            'display_name' => 'required'
        ]);
        if ($request->type == 'checkbox') {
            Setting::create($request->all() + ['value' => 0]);
        }elseif ($request->type == 'number' || 'decimal' ) {
            Setting::create($request->all() + ['value' => 0]);
        }
        else {
            Setting::create($request->all() + ['value' => 'فارغ']);
        }
        return redirect()->route('settings.index')->withSuccess(trans('app.settings_create'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'key' => 'required|regex:/^[a-zA-Z0-9\-_\.]+$/|unique:settings,key,'.$id,
            'display_name' => 'required'
        ]);

        if ($request->type == 'checkbox') {
            Setting::find($id)->update($request->all() + ['value' => 0]);
        } else {
            Setting::find($id)->update($request->all());
        }
        return redirect()->route('settings.index')->withSuccess(trans('app.settings_updated'));
    }

    /**
     * Handle application settings update.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function updateAll(Request $request)
    {
        foreach (Setting::all() as $setting) {
            if ($setting->type == 'checkbox') {
                if ($request->has($setting->key)) {
                    Setting::where('key', $setting->key)->update(['value' => $request->get($setting->key)]);
                } else {
                    Setting::where('key', $setting->key)->update(['value' => 0]);
                }
            } else {
                if ($request->has($setting->key)) {
                    Setting::where('key', $setting->key)->update(['value' => $request->get($setting->key)]);
                }
            }
        }
        return back()->withSuccess(trans('app.settings_updated'));
    }
}
