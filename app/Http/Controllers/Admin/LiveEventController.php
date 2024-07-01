<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\LiveEvent;
use App\Services\Zoom\ZoomService;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LiveEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:live-event.manage');
        $this->middleware('permission:live-event.add', ['only' => ['create']]);
        $this->middleware('permission:live-event.edit', ['only' => ['edit']]);
        $this->middleware('permission:live-event.delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('Admin.live-event.list');
    }

    public function getLiveEvent()
    {
        return Datatables::of(LiveEvent::latest())
            ->addIndexColumn()
            ->addColumn('action', function ($liveEvent) {
                $action = '';
                if (\Auth::user()->hasPermission('live-event.edit')) {
                    $action = '<a href="' . route('live-event.edit', $liveEvent->id) . '" class="btn btn-icon edit" title="' . trans('app.edit') . '" data-toggle="tooltip" data-placement="top"> <i class="fas fa-edit"></i></a>';
                }
                if (\Auth::user()->hasPermission('live-event.delete')) {
                    $action = $action . '<button type="button" id="Delete" data-id="' . $liveEvent->id . '" class="js-swal-confirm btn btn-light push mb-md-0" data-confirm-title="' . trans('app.delete') . '" data-confirm-text="' . trans('app.delete') . '" data-confirm-delete="' . trans('app.delete') . '" title="' . trans('app.delete') . '" data-toggle="tooltip" data-placement="top"><i class="fa fa-trash"></i></button>';;
                }
                return $action;
            })
            ->addColumn('is_paid', function ($course) {
                return $course->is_paid == 1 || !empty($course->price) ? trans('app.yes') : trans('app.no');
            })
            ->addColumn('status', function ($liveEvent) {
                return trans('app.' . $liveEvent->status);
            })
            ->addColumn('created_at', function ($liveEvent) {
                return $liveEvent->created_at->diffForHumans();
            })
            ->addColumn('updated_at', function ($liveEvent) {
                return $liveEvent->updated_at->diffForHumans();
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
        $statuses = EventStatus::lists();
        return view('Admin.live-event.add-edit', compact('edit', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws GuzzleException
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required',
            'event_presenter' => 'required',
            'description' => 'required',
            'duration_event' => 'required',
            'event_at' => 'required|date',
            'status' => 'required',
            'is_paid' => 'required|boolean',
            'price' => (boolean)$request->get('is_paid') ? 'required' : 'nullable',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'number_of_seats' => 'required|numeric',
        ]);

        $data['image'] = '';
        $data['agenda'] = [
            \Str::random(),
            \Str::random(),
            \Str::random(),
        ];

        if ($files = $request->file('image')) {
            $destinationPath = public_path('/upload/images/live-events/'); // upload path
            $courseImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $files->move($destinationPath, $courseImage);
            $data['image'] = $courseImage;
        }

        $liveEvent = LiveEvent::create($data);

        $serve = new ZoomService();

        $meeting = $serve->createMeeting([
            "agenda" => "ندوة مباشرة بعنوان : {$liveEvent->name} مقدمها: {$liveEvent->event_presenter} يوم {$liveEvent->event_at}",
            "topic" => "ندوة مباشرة بعنوان : {$liveEvent->name} مقدمها: {$liveEvent->event_presenter} يوم {$liveEvent->event_at}",
            "type" => 2, // 1 => instant, 2 => scheduled, 3 => recurring with no fixed time, 8 => recurring with fixed time
            "duration" => 60, // in minutes
            "timezone" => 'Asia/Riyadh', // set your timezone
            "password" => \Str::random(10),
            "start_time" => Carbon::parse($request->get('event_at'))->toDateTimeString(), // set your start time
            "pre_schedule" => false,  // set true if you want to create a pre-scheduled meeting
        ], $liveEvent);

        return redirect()->route('live-event.index')->withSuccess(trans('app.success'));
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
        $liveEvent = LiveEvent::find($id);
        $statuses = EventStatus::lists();
        return view('Admin.live-event.add-edit', compact('edit', 'liveEvent', 'statuses'));
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
            'name' => 'required',
            'event_presenter' => 'required',
            'description' => 'required',
            'duration_event' => 'required',
            'event_at' => 'required|date',
            'status' => 'required',
            'is_paid' => 'required|boolean',
            'price' => (boolean)$request->get('is_paid') ? 'required' : 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'number_of_seats' => 'required|numeric',
        ]);

        $data = $request->all();

        if ($request->has('image')) {
            if ($files = $request->file('image')) {
                $destinationPath = public_path('/upload/images/live-events'); // upload path
                $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
                $files->move($destinationPath, $profileImage);
                $data['image'] = "$profileImage";
            }
        }

        $liveEvent = LiveEvent::find($id);

        $liveEvent->update($data);

        $serve = new ZoomService();

        if ($liveEvent->meeting) {
            $meeting = $serve->updateMeeting($liveEvent->meeting->meeting_id, [
                "agenda" => "ندوة مباشرة بعنوان : {$liveEvent->name} مقدمها: {$liveEvent->event_presenter} يوم {$liveEvent->event_at}",
                "topic" => "ندوة مباشرة بعنوان : {$liveEvent->name} مقدمها: {$liveEvent->event_presenter} يوم {$liveEvent->event_at}",
                "type" => 2, // 1 => instant, 2 => scheduled, 3 => recurring with no fixed time, 8 => recurring with fixed time
                "duration" => 60, // in minutes
                "timezone" => 'Asia/Riyadh', // set your timezone
                "password" => \Str::random(10),
                "start_time" => Carbon::parse($liveEvent->event_at)->toDateTimeString(), // set your start time
                "pre_schedule" => false,  // set true if you want to create a pre-scheduled meeting
            ], $liveEvent);

        } else {
            $meeting = $serve->createMeeting([
                "agenda" => "ندوة مباشرة بعنوان : {$liveEvent->name} مقدمها: {$liveEvent->event_presenter} يوم {$liveEvent->event_at}",
                "topic" => "ندوة مباشرة بعنوان : {$liveEvent->name} مقدمها: {$liveEvent->event_presenter} يوم {$liveEvent->event_at}",
                "type" => 2, // 1 => instant, 2 => scheduled, 3 => recurring with no fixed time, 8 => recurring with fixed time
                "duration" => 60, // in minutes
                "timezone" => 'Asia/Riyadh', // set your timezone
                "password" => \Str::random(10),
                "start_time" => Carbon::parse($liveEvent->event_at)->toDateTimeString(), // set your start time
                "pre_schedule" => false,  // set true if you want to create a pre-scheduled meeting
            ], $liveEvent);
        }

        return redirect()->route('live-event.index')->withSuccess(trans('app.success'));
    }

    public function delete(Request $request)
    {
        $liveEvent = LiveEvent::find($request->id);

        $image_path = '/upload/images/live-events/' . $liveEvent->image;  // the value is : localhost/project/image/filename.format
        if (\File::exists($image_path)) {
            \File::delete($image_path);
        }

        $liveEvent->delete();
    }
}
