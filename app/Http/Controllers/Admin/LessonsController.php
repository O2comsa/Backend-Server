<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CourseStatus;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class LessonsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:lessons.manage');
        $this->middleware('permission:lessons.add', ['only' => ['create']]);
        $this->middleware('permission:lessons.edit', ['only' => ['edit']]);
        $this->middleware('permission:lessons.delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('Admin.lessons.list');
    }

    public function getLessons()
    {
        return Datatables::of(Lesson::whereHas('course')->latest())
            ->addIndexColumn()
            ->addColumn('action', function ($lesson) {
                $action = '';
                if (\Auth::user()->hasPermission('lessons.edit')) {
                    $action = '<a href="' . route('lessons.edit', $lesson->id) . '" class="btn btn-icon edit" title="' . trans('app.edit') . '" data-toggle="tooltip" data-placement="top"> <i class="fas fa-edit"></i></a>';
                }
                if (\Auth::user()->hasPermission('lessons.delete')) {
                    $action = $action . '<button type="button" id="Delete" data-id="' . $lesson->id . '" class="js-swal-confirm btn btn-light push mb-md-0" data-confirm-title="' . trans('app.delete') . '" data-confirm-text="' . trans('app.delete') . '" data-confirm-delete="' . trans('app.delete') . '" title="' . trans('app.delete') . '" data-toggle="tooltip" data-placement="top"><i class="fa fa-trash"></i></button>';;
                }
                return $action;
            })
            ->addColumn('course_title', function ($lesson) {
                return $lesson->course->title;
            })
            ->addColumn('status', function ($lesson) {
                return trans('app.' . $lesson->status);
            })
            ->addColumn('created_at', function ($lesson) {
                return $lesson->created_at->diffForHumans();
            })
            ->addColumn('updated_at', function ($lesson) {
                return $lesson->updated_at->diffForHumans();
            })
            ->rawColumns(['action', 'created_at', 'updated_at', 'course_title', 'status'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $courses = Course::pluck('title', 'id');
        $edit = false;
        $statuses = CourseStatus::lists();
        return view('Admin.lessons.add-edit', compact('edit', 'courses', 'statuses'));
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
            'video' => 'required|mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,qt',
            'lesson_time' => 'required',
            'status' => 'required',
            'course_id' => 'required|exists:courses,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $data = $request->except(['video','image']);
        if ($files = $request->file('video')) {
            $destinationPath = public_path('/upload/lessons'); // upload path
            $video = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $video);
            $data['video'] = $video;
        }
        if ($files = $request->file('image')) {
            $destinationPath = public_path('/upload/images/lessons'); // upload path
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $profileImage);
            $data['image'] = "$profileImage";
        }
        Lesson::create($data);
        return redirect()->route('lessons.index')->withSuccess(trans('app.success'));
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
        $courses = Course::pluck('title', 'id');
        $edit = true;
        $lesson = Lesson::find($id);
        $statuses = CourseStatus::lists();
        return view('Admin.lessons.add-edit', compact('edit', 'courses', 'lesson', 'statuses'));
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
            'video' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,qt',
            'lesson_time' => 'required',
            'status' => 'required',
            'course_id' => 'required|exists:courses,id',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->except(['video','image']);

        if ($request->file('video') && !empty($request->file('video'))) {
            $files = $request->file('video');
            $destinationPath = public_path('/upload/lessons'); // upload path
            $video = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $video);
            $data['video'] = $video;
        } else {
            $video = explode('lessons/', Lesson::find($id)->video)[1];
            $data['video'] = $video;
        }
        if ($request->has('image')) {
            if ($files = $request->file('image')) {
                $destinationPath = public_path('/upload/images/lessons'); // upload path
                $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
                $files->move($destinationPath, $profileImage);
                $data['image'] = "$profileImage";
            }
        }

        Lesson::find($id)->update($data);

        return redirect()->route('lessons.index')->withSuccess(trans('app.success'));
    }


    public function delete(Request $request)
    {
        $lesson = Lesson::find($request->id);
        $path = '/upload/lessons/' . $lesson->video;  // the value is : localhost/project/image/filename.format
        if (\File::exists($path)) {
            \File::delete($path);
        }
        $path = '/upload/images/lessons/' . $lesson->image;  // the value is : localhost/project/image/filename.format
        if (\File::exists($path)) {
            \File::delete($path);
        }
        $lesson->delete();
    }
}
