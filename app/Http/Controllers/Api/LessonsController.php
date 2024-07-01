<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Helpers\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Notifications\SuccessfullyGenerateCertificateNotification;
use App\Notifications\SuccessfullyGeneratedCertEmail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

class LessonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        ApiHelper::validate($request, [
            'course_id' => 'required'
        ]);

        $lessons = Lesson::select(['id', 'title', 'video', 'lesson_time', 'image'])
            ->where('course_id', \request()->get('course_id'))
            ->where('status', CourseStatus::ACTIVE)
            ->get();

        return ApiHelper::output($lessons);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $lesson = Lesson::query()
            ->select([
                'id',
                'title',
                'video',
                'lesson_time',
                'course_id',
                'image'
            ])
            ->find($id);

        if (!$lesson) return ApiHelper::output([], 404);

        $lesson->next = Lesson::query()
            ->where('id', '>', $lesson->id)
            ->where('course_id', $lesson->course_id)
            ->orderBy('id', 'asc')
            ->first()
            ?->id;

        $lesson->previous = Lesson::query()
            ->where('id', '<', $lesson->id)
            ->where('course_id', $lesson->course_id)
            ->orderBy('id', 'asc')
            ->first()
            ?->id;

        if (auth('api')->check()) {
            $lesson->userShow()->syncWithoutDetaching(\request()->get('user_id'));
        }

        return ApiHelper::output($lesson);
    }

    /**
     * set lesson to viewed.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function view($id)
    {
        $lesson = Lesson::find($id);

        if (auth('api')->check()) {
            $lesson->userShow()->syncWithoutDetaching(\request()->get('user_id'));
        }

        return ApiHelper::output(trans('app.success'));
    }

    /**
     * set lesson to viewed.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete($id, Request $request)
    {
        $lesson = Lesson::find($id);

        if (auth('api')->check()) {
            $lesson->userCompleted()->syncWithoutDetaching(\request()->get('user_id'));

            $lessons = $lesson->course->lessons();
            $lessonsCount = $lessons->count();

            $completedLessons = DB::table('users_complete_lessons')
                ->where('user_id', \request()->get('user_id'))
                ->whereIn('Lesson_id', $lessons->pluck('id'))
                ->latest();

            $isUserGenerated = auth('api')
                ->user()
                ->certificates()
                ->where('related_type', 'App\Models\Course')
                ->where('related_id', $lesson->course_id)
                ->exists();

            if ($lessonsCount == $completedLessons->count() && !$isUserGenerated) {
                try {
                    $data['related_id'] = $lesson->course_id;
                    $data['related_type'] = Course::class;
                    $data['user_id'] = $request->get('user_id');

                    $data['duration_hours'] = Carbon::parse($completedLessons->latest()->first()->created_at)->diffInHours($completedLessons->oldest()->first()->created_at);
                    $data['duration_days'] = Carbon::parse($completedLessons->latest()->first()->created_at)->diffInDays($completedLessons->oldest()->first()->created_at);
                    $data['start_date'] = Carbon::parse($completedLessons->oldest()->first()->created_at)?->format('Y/m/d') ?? now();
                    $data['end_date'] = Carbon::parse($completedLessons->latest()->first()->created_at)?->format('Y/m/d') ?? now();

                    $related = Course::find($data['related_id']);

                    $user = User::find($request->get('user_id'));

                    $pdfHTML = view('certificate-pdf', [
                        'countDays' => $data['duration_days'],
                        'courseName' => $related?->title ?? $related?->name,
                        'days' => '',
                        'endDate' => $data['end_date'],
                        'nationalId' => $user->national_id,
                        'startDate' => $data['start_date'],
                        'user_name' => $user->name,
                        'hours' => $data['duration_hours']
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

                    try {
                        $user->notify(new SuccessfullyGenerateCertificateNotification($certificate));
                        $user->notify(new SuccessfullyGeneratedCertEmail($certificate)); //send email with pdf
                    } catch (\Exception $exception) {
                        Log::error($exception);
                    }

                } catch (\Exception $exception) {
                    Log::error($exception);
                }
            }
        }

        return ApiHelper::output(trans('app.success'));
    }

    /**
     * set lesson to viewed.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookmark($id)
    {
        $lesson = Lesson::find($id);

        if (auth('api')->check()) {
            $lesson?->bookmarks()->toggle(\request()->get('user_id'));
        }

        return ApiHelper::output(trans('app.success'));
    }

    /**
     * set lesson to viewed.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listBookmarked(Request $request)
    {
        $lessons = Lesson::query()
            ->whereHas('bookmarks', function ($query) use ($request) {
                $query->where('user_id', $request->get('user_id'));
            })
            ->get();

        return ApiHelper::output($lessons);
    }
}
