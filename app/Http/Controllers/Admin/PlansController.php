<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PlanStatus;
use App\Http\Controllers\Controller;
use App\Models\Dictionary;
use App\Models\Plan;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PlansController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:plans.manage');
        $this->middleware('permission:plans.add', ['only' => ['create']]);
        $this->middleware('permission:plans.edit', ['only' => ['edit']]);
        $this->middleware('permission:plans.delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('Admin.plans.list');
    }

    public function getPlans()
    {
        return Datatables::of(Plan::query()->latest())
            ->addIndexColumn()
            ->addColumn('action', function ($plan) {
                $action = '';
                if (\Auth::user()->hasPermission('plans.edit')) {
                    $action = '<a href="' . route('plans.edit', $plan->id) . '" class="btn btn-icon edit" title="' . trans('app.edit') . '" data-toggle="tooltip" data-placement="top"> <i class="fas fa-edit"></i></a>';
                }
                if (\Auth::user()->hasPermission('plans.delete')) {
                    $action = $action . '<button type="button" id="Delete" data-id="' . $plan->id . '" class="js-swal-confirm btn btn-light push mb-md-0" data-confirm-title="' . trans('app.delete') . '" data-confirm-text="' . trans('app.delete') . '" data-confirm-delete="' . trans('app.delete') . '" title="' . trans('app.delete') . '" data-toggle="tooltip" data-placement="top"><i class="fa fa-trash"></i></button>';;
                }
                return $action;
            })
            ->addColumn('status', function ($plan) {
                return trans('app.' . $plan->status);
            })
            ->addColumn('created_at', function ($plan) {
                return $plan->created_at->diffForHumans();
            })
            ->addColumn('updated_at', function ($plan) {
                return $plan->updated_at->diffForHumans();
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
        $statuses = PlanStatus::lists();
        return view('Admin.plans.add-edit', compact('edit', 'statuses'));
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
            'name' => 'required',
            'credit' => 'required',
            'description' => 'required',
            'status' => 'required',
            'price' => 'required',
            'period' => 'required',
        ]);

        Plan::create($data);

        return redirect()->route('plans.index')->withSuccess(trans('app.success'));
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
        $plan = Plan::find($id);
        $statuses = PlanStatus::lists();
        return view('Admin.plans.add-edit', compact('edit', 'plan', 'statuses'));
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
        $data = $this->validate($request, [
            'title' => 'required',
            'name' => 'required',
            'credit' => 'required',
            'description' => 'required',
            'status' => 'required',
            'price' => 'required',
            'period' => 'required',
        ]);

        Plan::find($id)->update($data);

        return redirect()->route('plans.index')->withSuccess(trans('app.success'));
    }

    public function delete(Request $request)
    {
        $plan = Plan::find($request->id);

        $plan->delete();
    }
}
