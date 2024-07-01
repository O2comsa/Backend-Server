<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DictionaryStatus;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Dictionary;
use App\Models\LiveEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Yajra\DataTables\DataTables;
use App\Notifications\SuccessfullyGenerateCertificateNotification;
use App\Notifications\SuccessfullyGeneratedCertEmail;



class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:certificates.manage');
        $this->middleware('permission:certificates.add', ['only' => ['create']]);
        $this->middleware('permission:certificates.edit', ['only' => ['edit']]);
        $this->middleware('permission:certificates.delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('Admin.certificates.list');
    }

    public function getCertificates()
    {
        return Datatables::of(Certificate::latest())
            ->addIndexColumn()
            ->addColumn('action', function ($certificate) {
                $action = '';
                if (\Auth::user()->hasPermission('certificates.edit')) {
                    $action = '<a href="' . route('certificates.edit', $certificate->id) . '" class="btn btn-icon edit" title="' . trans('app.edit') . '" data-toggle="tooltip" data-placement="top"> <i class="fas fa-edit"></i></a>';
                }
                if (\Auth::user()->hasPermission('certificates.delete')) {
                    $action = $action . '<button type="button" id="Delete" data-id="' . $certificate->id . '" class="js-swal-confirm btn btn-light push mb-md-0" data-confirm-title="' . trans('app.delete') . '" data-confirm-text="' . trans('app.delete') . '" data-confirm-delete="' . trans('app.delete') . '" title="' . trans('app.delete') . '" data-toggle="tooltip" data-placement="top"><i class="fa fa-trash"></i></button>';;
                }
                return $action;
            })
            ->addColumn('user', function ($certificate) {
                return $certificate->user?->email;
            })
            ->addColumn('file_pdf', function ($certificate) {
                return '<a href="' . $certificate->file_pdf . '">حمل من هنا</a>';
            })
            ->addColumn('image', function ($certificate) {
                return '<a href="' . $certificate->image . '">حمل من هنا</a>';
            })
            ->addColumn('created_at', function ($certificate) {
                return $certificate->created_at->diffForHumans();
            })
            ->addColumn('updated_at', function ($certificate) {
                return $certificate->updated_at->diffForHumans();
            })
            ->rawColumns(['action', 'created_at', 'updated_at', 'file_pdf','image'])
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

        $liveEvents = LiveEvent::pluck('name', 'id');
        $courses = Course::pluck('title', 'id');
        $users = User::pluck('email', 'id');

        return view('Admin.certificates.add-edit', compact('edit', 'users', 'liveEvents', 'courses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws CouldNotTakeBrowsershot
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'related_type' => "required|in:" . LiveEvent::class . ',' . Course::class,
            'live_event_id' => 'required_if:related_type,' . LiveEvent::class . '|integer|exists:live_events,id',
            'course_id' => 'required_if:related_type,' . Course::class . '|integer|exists:courses,id',
            'duration_hours' => 'required|integer',
            'duration_days' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $data['related_id'] = $request->get('related_type') == LiveEvent::class ? $request->get('live_event_id') : $request->get('course_id');

        $related = $request->get('related_type')::find($data['related_id']);

        $user = User::find($request->get('user_id'));

        $pdfHTML = view('certificate-pdf2', [
            'countDays' => $request->get('duration_days'),
                'courseName' => $related?->title ?? $related?->name,
                'days' => $request->get('duration_days'),
                'endDate' => $request->get('end_date'),
                'nationalId' => $user->national_id,
                'startDate' => $request->get('start_date'),
                'user_name' => $user->name,
                'hours' => $request->get("duration_hours")
            ])->render();

        $fileName = $request->get('user_id') . '_' . md5(now());

        $browserShot = new Browsershot();

         $browserShot->html($pdfHTML)->setNodeBinary('/opt/cpanel/ea-nodejs20/bin/node')->setNpmBinary('/opt/cpanel/ea-nodejs20/bin/npm')
            ->margins(10, 0, 10, 0)
            ->format('A4')
            ->showBackground()
            ->landscape()
            ->savePdf(public_path("upload/files/certificates/$fileName.pdf"));

        $browserShot->html($pdfHTML)->setNodeBinary('/opt/cpanel/ea-nodejs20/bin/node')->setNpmBinary('/opt/cpanel/ea-nodejs20/bin/npm')
            ->margins(10, 0, 10, 0)
            ->format('A4')
            ->showBackground()
            ->landscape()
            ->save(public_path("upload/files/certificates/$fileName.png"));

        $data['from'] = '';
        $data['file_pdf'] = "$fileName.pdf";
        $data['image'] = "$fileName.png";

        $certificate = Certificate::create($data);

        $certificate->user->notify(new SuccessfullyGenerateCertificateNotification($certificate));
        $certificate->user->notify(new SuccessfullyGeneratedCertEmail($certificate)); //send email with pdf
        

        return redirect()->route('certificates.index')->withSuccess(trans('app.success'));
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

        $liveEvents = LiveEvent::pluck('name', 'id');
        $courses = Course::pluck('title', 'id');
        $users = User::pluck('email', 'id');

        $certificate = Certificate::find($id);

        return view('Admin.certificates.add-edit', compact('edit', 'courses', 'users', 'liveEvents', 'certificate'));
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
            'user_id' => 'required|exists:users,id',
            'related_type' => "required|in:" . LiveEvent::class . ',' . Course::class,
            'live_event_id' => 'required_if:related_type,' . LiveEvent::class . '|integer|exists:live_events,id',
            'course_id' => 'required_if:related_type,' . Course::class . '|integer|exists:courses,id',
            'duration_hours' => 'required|integer',
            'duration_days' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $data['related_id'] = $request->get('related_type') == LiveEvent::class ? $request->get('live_event_id') : $request->get('course_id');

        $related = $request->get('related_type')::find($data['related_id']);

        $user = User::find($request->get('user_id'));

        $pdfHTML = view('certificate-pdf', [
        'countDays' => $request->get('duration_days'),
            'courseName' => $related?->title ?? $related?->name,
            'days' => $request->get('duration_days'),
            'endDate' => $request->get('end_date'),
            'nationalId' => $user->national_id,
            'startDate' => $request->get('start_date'),
            'user_name' => $user->name,
            'hours' => $request->get("duration_hours")
        ])->render();

        $fileName = $request->get('user_id') . '_' . md5(now());

        $browserShot = new Browsershot();

        $browserShot->html($pdfHTML)->setNodeBinary('/opt/cpanel/ea-nodejs20/bin/node')->setNpmBinary('/opt/cpanel/ea-nodejs20/bin/npm')
            ->margins(10, 0, 10, 0)
            ->format('A4')
            ->showBackground()
            ->landscape()
            ->savePdf(public_path("upload/files/certificates/$fileName.pdf"));

        $browserShot->html($pdfHTML)->setNodeBinary('/opt/cpanel/ea-nodejs20/bin/node')->setNpmBinary('/opt/cpanel/ea-nodejs20/bin/npm')
            ->margins(10, 0, 10, 0)
            ->format('A4')
            ->showBackground()
            ->landscape()
            ->save(public_path("upload/files/certificates/$fileName.png"));

        $data['from'] = '';
        $data['file_pdf'] = "$fileName.pdf";
        $data['image'] = "$fileName.png";

        Certificate::find($id)->update($data);

        return redirect()->route('certificates.index')->withSuccess(trans('app.success'));
    }


    public function delete(Request $request)
    {
        $certificate = Certificate::find($request->id);

        $image_path = '/upload/certificates/' . $certificate->file_pdf;  // the value is : localhost/project/image/filename.format
        if (\File::exists($image_path)) {
            \File::delete($image_path);
        }

        $certificate->delete();
    }
}
