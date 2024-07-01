<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class ArticlesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:articles.manage');
        $this->middleware('permission:articles.add', ['only' => ['create']]);
        $this->middleware('permission:articles.edit', ['only' => ['edit']]);
        $this->middleware('permission:articles.delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('Admin.articles.list');
    }

    public function getArticles()
    {
        return Datatables::of(Article::latest())
            ->addIndexColumn()
            ->addColumn('action', function ($category) {
                $action = '';
                if (\Auth::user()->hasPermission('articles.edit')) {
                    $action = '<a href="' . route('articles.edit', $category->id) . '" class="btn btn-icon edit" title="' . trans('app.edit') . '" data-toggle="tooltip" data-placement="top"> <i class="fas fa-edit"></i></a>';
                }
                if (\Auth::user()->hasPermission('articles.delete')) {
                    $action = $action . '<button type="button" id="Delete" data-id="' . $category->id . '" class="js-swal-confirm btn btn-light push mb-md-0" data-confirm-title="' . trans('app.delete') . '" data-confirm-text="' . trans('app.delete') . '" data-confirm-delete="' . trans('app.delete') . '" title="' . trans('app.delete') . '" data-toggle="tooltip" data-placement="top"><i class="fa fa-trash"></i></button>';;
                }
                return $action;
            })
            ->addColumn('created_at', function ($category) {
                return $category->created_at->diffForHumans();
            })
            ->addColumn('updated_at', function ($category) {
                return $category->updated_at->diffForHumans();
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
        return view('Admin.articles.add-edit', compact('edit'));
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
        ]);
        $data = $request->only(['title', 'description']);
        if ($files = $request->file('image')) {
            $destinationPath = public_path('/upload/images/articles'); // upload path
            $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $profileImage);
            $data['image'] = "$profileImage";
        }
        Article::create($data);
        return redirect()->route('articles.index')->withSuccess(trans('app.success'));
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
        $article = Article::find($id);
        return view('Admin.articles.add-edit', compact('edit', 'article'));
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
        ]);

        $data = $request->all();
        if ($request->has('image')) {
            if ($files = $request->file('image')) {
                $destinationPath = public_path('/upload/images/articles'); // upload path
                $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
                $files->move($destinationPath, $profileImage);
                $data['image'] = "$profileImage";
            }
        }

        Article::find($id)->update($data);

        return redirect()->route('articles.index')->withSuccess(trans('app.success'));
    }


    public function delete(Request $request)
    {
        $Article = Article::find($request->id);
        $image_path = '/upload/images/article/' . $Article->image;  // the value is : localhost/project/image/filename.format
        if (\File::exists($image_path)) {
            \File::delete($image_path);
        }
        $Article->delete();
    }
}
