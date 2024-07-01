<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:banner.manage');
        $this->middleware('permission:banner.add', ['only' => ['create']]);
        $this->middleware('permission:banner.edit', ['only' => ['edit']]);
        $this->middleware('permission:banner.delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('Admin.banner.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $edit = false;
        return view('Admin.banner.create', compact('edit'));
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
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
         ]);
        if ($files = $request->file('image')) {
            $destinationPath = public_path('/upload/images/banner'); // upload path
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $profileImage);
            $data['image'] = "$profileImage";
        }
        Banner::create($data);
        return redirect()->route('banner.index')->withSuccess(trans('app.Successfully Save Your Image file.'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        //
        $edit = true;
        $banner = Banner::find($id);
        return view('Admin.banner.create', compact( 'edit', 'banner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();
        if ($request->has('image')) {
            if ($files = $request->file('image')) {
                $destinationPath = public_path('/upload/images/banner'); // upload path
                $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
                $files->move($destinationPath, $profileImage);
                $data['image'] = "$profileImage";
            }
        }

        Banner::find($id)->update($data);
        return redirect()->route('banner.index')->withSuccess(trans('app.Successfully Save Your Image file.'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $banner = Banner::find($request->id);
        $image_path = '/upload/images/banner/' . $banner->image;  // the value is : localhost/project/image/filename.format
        if (\File::exists($image_path)) {
            \File::delete($image_path);
        }
        $banner->delete();
    }
}
