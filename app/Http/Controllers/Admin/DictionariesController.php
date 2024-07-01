<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CourseStatus;
use App\Helpers\DictionaryStatus;
use App\Models\Course;
use App\Models\Dictionary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class DictionariesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dictionaries.manage');
        $this->middleware('permission:dictionaries.add', ['only' => ['create']]);
        $this->middleware('permission:dictionaries.edit', ['only' => ['edit']]);
        $this->middleware('permission:dictionaries.delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('Admin.dictionaries.list');
    }

    public function getDictionaries()
    {
        return Datatables::of(Dictionary::latest())
            ->addIndexColumn()
            ->addColumn('action', function ($course) {
                $action = '';
                if (\Auth::user()->hasPermission('dictionaries.edit')) {
                    $action = '<a href="' . route('dictionaries.edit', $course->id) . '" class="btn btn-icon edit" title="' . trans('app.edit') . '" data-toggle="tooltip" data-placement="top"> <i class="fas fa-edit"></i></a>';
                }
                if (\Auth::user()->hasPermission('dictionaries.delete')) {
                    $action = $action . '<button type="button" id="Delete" data-id="' . $course->id . '" class="js-swal-confirm btn btn-light push mb-md-0" data-confirm-title="' . trans('app.delete') . '" data-confirm-text="' . trans('app.delete') . '" data-confirm-delete="' . trans('app.delete') . '" title="' . trans('app.delete') . '" data-toggle="tooltip" data-placement="top"><i class="fa fa-trash"></i></button>';;
                }
                return $action;
            })
            ->addColumn('is_paid', function ($course) {
                return $course->is_paid == 1 || !empty($course->price) ? trans('app.yes') : trans('app.no');
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
        $statuses = DictionaryStatus::lists();
        return view('Admin.dictionaries.add-edit', compact('edit', 'statuses'));
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
        $data = $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image',
            'file_pdf' => 'required|mimes:pdf',
            'status' => 'required',
            'price' => $request->get('is_paid') ? 'required' : 'nullable',
            'is_paid' => 'required|boolean',
        ]);

        if ($files = $request->file('file_pdf')) {
            $destinationPath = public_path('/upload/dictionaries'); // upload path
            $file_pdf = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $file_pdf);
            $data['file_pdf'] = $file_pdf;
        }

        if ($files = $request->file('image')) {
            $destinationPath = public_path('/upload/images/dictionaries'); // upload path
            $courseImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $courseImage);
            $data['image'] = $courseImage;
        }

        Dictionary::create($data);
        return redirect()->route('dictionaries.index')->withSuccess(trans('app.success'));
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
        $dictionary = Dictionary::find($id);
        $statuses = DictionaryStatus::lists();
        return view('Admin.dictionaries.add-edit', compact('edit', 'dictionary', 'statuses'));
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
            'image' => 'image',
            'file_pdf' => 'mimes:pdf',
            'status' => 'required',
            'price' => $request->get('is_paid') ? 'required' : 'nullable',
            'is_paid' => 'required|boolean',
        ]);

        $data = $request->except(['image', 'file_pdf']);

        if ($request->hasFile('file_pdf')) {
            $file_pdf = $request->file('file_pdf');
            $destinationPath = public_path('/upload/dictionaries'); // upload path
            $FileName = date('YmdHis') . "." . $file_pdf->getClientOriginalExtension();
            $file_pdf->move($destinationPath, $FileName);
            $data['file_pdf'] = $FileName;
        }

        if ($request->file('image') && !empty($request->file('image'))) {
            $files = $request->file('image');
            $destinationPath = public_path('/upload/images/dictionaries'); // upload path
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $profileImage);
            $data['image'] = $profileImage;
        }

        Dictionary::find($id)->update($data);

        return redirect()->route('dictionaries.index')->withSuccess(trans('app.success'));
    }


    public function delete(Request $request)
    {
        $dictionary = Dictionary::find($request->id);
        $image_path = '/upload/dictionaries/' . $dictionary->file_pdf;  // the value is : localhost/project/image/filename.format
        if (\File::exists($image_path)) {
            \File::delete($image_path);
        }
        $image = '/upload/images/dictionaries/' . $dictionary->image;  // the value is : localhost/project/image/filename.format
        if (\File::exists($image)) {
            \File::delete($image);
        }
        $dictionary->delete();
    }
}
