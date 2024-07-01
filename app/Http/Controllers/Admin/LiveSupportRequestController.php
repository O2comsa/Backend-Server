<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LiveSupportRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\LiveSupportRequest;
use App\Models\User;
use App\Services\Zoom\ZoomService;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LiveSupportRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected ZoomService $zoomService)
    {
        $this->middleware('auth');
        $this->middleware('permission:live-support-request.manage');
        $this->middleware('permission:live-support-request.add', ['only' => ['create']]);
        $this->middleware('permission:live-support-request.edit', ['only' => ['edit']]);
        $this->middleware('permission:live-support-request.delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Admin.live-support-request.list');
    }

    public function getLiveSupportRequest()
    {
        return Datatables::of(LiveSupportRequest::latest())
            ->addIndexColumn()
            ->addColumn('action', function ($liveSupportRequest) {
                $action = '';
                if (\Auth::user()->hasPermission('live-support-request.edit')) {
                    $action = '<a href="' . route('live-support-request.edit', $liveSupportRequest->id) . '" class="btn btn-icon edit" title="' . trans('app.edit') . '" data-toggle="tooltip" data-placement="top"> <i class="fas fa-edit"></i></a>';
                }
//                if (\Auth::user()->hasPermission('live-support-request.delete')) {
//                    $action = $action . '<button type="button" id="Delete" data-id="' . $liveSupportRequest->id . '" class="js-swal-confirm btn btn-light push mb-md-0" data-confirm-title="' . trans('app.delete') . '" data-confirm-text="' . trans('app.delete') . '" data-confirm-delete="' . trans('app.delete') . '" title="' . trans('app.delete') . '" data-toggle="tooltip" data-placement="top"><i class="fa fa-trash"></i></button>';;
//                }
                return $action;
            })
            ->addColumn('user_name', function ($liveSupportRequest) {
                $link = route('users.edit', $liveSupportRequest->user_id);

                return "<a href='{$link}'>{$liveSupportRequest->user->name}</a>";
            })
            ->addColumn('admin_name', function ($liveSupportRequest) {
                return $liveSupportRequest->admin?->name;
            })
            ->addColumn('status', function ($liveSupportRequest) {
                return trans('app.' . $liveSupportRequest->status);
            })
            ->addColumn('created_at', function ($liveSupportRequest) {
                return $liveSupportRequest->created_at->diffForHumans();
            })
            ->addColumn('updated_at', function ($liveSupportRequest) {
                return $liveSupportRequest->updated_at->diffForHumans();
            })
            ->rawColumns(['action', 'created_at', 'updated_at', 'user_name', 'admin_name'])
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

        $statuses = LiveSupportRequestStatus::lists();

        $users = User::pluck('email', 'id');

        $admins = Admin::pluck('email', 'id');

        return view('Admin.live-support-request.add-edit', compact('edit', 'statuses', 'admins', 'users'));
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
            'user_id' => 'required',
            'admin_id' => 'required',
            'plan_id' => 'required',
            'status' => 'required',
            'duration' => 'required',
        ]);

        LiveSupportRequest::create($request->all());

        return redirect()->route('live-support-request.index')->withSuccess(trans('app.success'));
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

        $statuses = LiveSupportRequestStatus::lists();

        $users = User::pluck('email', 'id');

        $admins = Admin::pluck('email', 'id');

        $liveSupportRequest = LiveSupportRequest::query()->findOrFail($id);

        return view('Admin.live-support-request.add-edit', compact('edit', 'statuses', 'admins', 'users', 'liveSupportRequest'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws GuzzleException
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required',
        ]);

        $LiveSupportRequest = LiveSupportRequest::query()->find($id);

        $LiveSupportRequest->update($request->only(['status']) + ['admin_id' => auth('admin')->user()->id]);

        if ($request->get('status') == LiveSupportRequestStatus::ACCEPTED_STATUS) {
            $serve = new ZoomService();

            try {
                $meeting = $serve->createMeeting([
                    "agenda" => "طلب دعم فني من المستخدم {$LiveSupportRequest->user->name} باشراف {$LiveSupportRequest->admin->name}",
                    "topic" => "طلب دعم فني من المستخدم {$LiveSupportRequest->user->name} باشراف {$LiveSupportRequest->admin->name}",
                    "type" => 2, // 1 => instant, 2 => scheduled, 3 => recurring with no fixed time, 8 => recurring with fixed time
                    "duration" => 60, // in minutes
                    "timezone" => 'Asia/Riyadh', // set your timezone
                    "password" => \Str::random(10),
                    "start_time" => Carbon::now()->addSeconds(5)->toDateTimeString(), // set your start time
                    "pre_schedule" => false,  // set true if you want to create a pre-scheduled meeting
                ], $LiveSupportRequest);

                $serve->addMeetingRegistrant($meeting['data']->meeting_id, [
                    'first_name' => auth('admin')->user()->name,
                    'last_name' => ' Admin',
                    'email' => 'tariq.ayman94@gmail.com'//auth('admin')->user()->email
                ], auth('admin')->user()->id);

                $serve->addMeetingRegistrant($meeting['data']->meeting_id, [
                    'first_name' => $LiveSupportRequest->user->name,
                    'last_name' => ' User',
                    'email' => $LiveSupportRequest->user->email
                ], $LiveSupportRequest->user->id);

                $LiveSupportRequest->update(['status' => LiveSupportRequestStatus::IN_PROGRESS_STATUS]);
            } catch (\Exception $exception) {
                return redirect()->route('live-support-request.edit', $id)->withErrors($exception->getMessage());
            }
        }

        return redirect()->route('live-support-request.edit', $id)->withSuccess(trans('app.success'));
    }


    public function delete(Request $request)
    {
        $liveSupportRequest = LiveSupportRequest::find($request->id);

        $liveSupportRequest->delete();
    }
}
