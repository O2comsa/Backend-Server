<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CourseStatus;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class CoursesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:courses.manage');
        $this->middleware('permission:courses.add', ['only' => ['create']]);
        $this->middleware('permission:courses.edit', ['only' => ['edit']]);
        $this->middleware('permission:courses.delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('Admin.courses.list');
    }

    public function getCourses()
    {
        return Datatables::of(Course::latest())
            ->addIndexColumn()
            ->addColumn('action', function ($course) {
                $action = '';
                if (\Auth::user()->hasPermission('courses.edit')) {
                    $action = '<a href="' . route('courses.edit', $course->id) . '" class="btn btn-icon edit" title="' . trans('app.edit') . '" data-toggle="tooltip" data-placement="top"> <i class="fas fa-edit"></i></a>';
                }
                if (\Auth::user()->hasPermission('courses.delete')) {
                    $action = $action . '<button type="button" id="Delete" data-id="' . $course->id . '" class="js-swal-confirm btn btn-light push mb-md-0" data-confirm-title="' . trans('app.delete') . '" data-confirm-text="' . trans('app.delete') . '" data-confirm-delete="' . trans('app.delete') . '" title="' . trans('app.delete') . '" data-toggle="tooltip" data-placement="top"><i class="fa fa-trash"></i></button>';;
                }
                return $action;
            })
            ->addColumn('status', function ($course) {
                return trans('app.' . $course->status);
            })
            ->addColumn('created_at', function ($course) {
                return $course->created_at->diffForHumans();
            })
            ->addColumn('updated_at', function ($course) {
                return $course->updated_at->diffForHumans();
            })
            ->rawColumns(['action', 'created_at', 'updated_at'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $edit = false;
        $statuses = CourseStatus::lists();
        $courses = Course::all();
        return view('Admin.courses.add-edit', compact('edit', 'statuses', 'courses'));
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
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'status' => 'required',
            'price' => $request->get('free') ? 'nullable' : 'required',
            'free' => 'required|boolean',
        ]);
        if ($files = $request->file('image')) {
            $destinationPath = public_path('/upload/images/courses'); // upload path
            $courseImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $courseImage);
        }
        Course::create($request->except('image') + ['image' => $courseImage])->courses()->sync($request->get('related_course'));
        return redirect()->route('courses.index')->withSuccess(trans('app.success'));
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $edit = true;
        $course = Course::find($id);
        $statuses = CourseStatus::lists();
        $courses = Course::where('id', '!=', $id)->get();
        return view('Admin.courses.add-edit', compact('edit', 'course', 'statuses', 'courses'));
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
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required',
            'price' => $request->get('free') ? 'nullable' : 'required',
            'free' => 'required|boolean',
        ]);

        $data = $request->except('image');
        if ($request->file('image') && !empty($request->file('image'))) {
            $files = $request->file('image');
            $destinationPath = public_path('/upload/images/courses'); // upload path
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $profileImage);
        } else {
            $profileImage = explode('courses/', Course::find($id)->image)[1];
        }
        $data['image'] = $profileImage;
        if ($data['free']){
            $data['price'] = null;
        }
        Course::find($id)->update($data);
        Course::find($id)->courses()->sync($request->get('related_course'));
        return redirect()->route('courses.index')->withSuccess(trans('app.success'));
    }


    public function delete(Request $request)
    {
        $course = Course::find($request->id);
        $image_path = '/upload/images/courses/' . $course->image;  // the value is : localhost/project/image/filename.format
        if (\File::exists($image_path)) {
            \File::delete($image_path);
        }
        $course->delete();
    }
}
