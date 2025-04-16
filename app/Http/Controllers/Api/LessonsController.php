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
    // public function complete($id, Request $request)
    // {
    //     $lesson = Lesson::find($id);

    //     if (auth('api')->check()) {
    //         $lesson->userCompleted()->syncWithoutDetaching(\request()->get('user_id'));

    //         $lessons = $lesson->course->lessons();
    //         $lessonsCount = $lessons->count();

    //         $completedLessons = DB::table('users_complete_lessons')
    //             ->where('user_id', \request()->get('user_id'))
    //             ->whereIn('Lesson_id', $lessons->pluck('id'))
    //             ->latest();

    //         $isUserGenerated = auth('api')
    //             ->user()
    //             ->certificates()
    //             ->where('related_type', 'App\Models\Course')
    //             ->where('related_id', $lesson->course_id)
    //             ->exists();

    //         if ($lessonsCount == $completedLessons->count() && !$isUserGenerated) {
    //             try {
    //                 $data['related_id'] = $lesson->course_id;
    //                 $data['related_type'] = Course::class;
    //                 $data['user_id'] = $request->get('user_id');

    //                 $data['duration_hours'] = Carbon::parse($completedLessons->latest()->first()->created_at)->diffInHours($completedLessons->oldest()->first()->created_at);
    //                 $data['duration_days'] = Carbon::parse($completedLessons->latest()->first()->created_at)->diffInDays($completedLessons->oldest()->first()->created_at);
    //                 $data['start_date'] = Carbon::parse($completedLessons->oldest()->first()->created_at)?->format('Y/m/d') ?? now();
    //                 $data['end_date'] = Carbon::parse($completedLessons->latest()->first()->created_at)?->format('Y/m/d') ?? now();

    //                 $related = Course::find($data['related_id']);

    //                 $user = User::find($request->get('user_id'));

    //                 $pdfHTML = view('certificate-pdf', [
    //                     'countDays' => $data['duration_days'],
    //                     'courseName' => $related?->title ?? $related?->name,
    //                     'days' => '',
    //                     'endDate' => $data['end_date'],
    //                     'nationalId' => $user->national_id,
    //                     'startDate' => $data['start_date'],
    //                     'user_name' => $user->name,
    //                     'hours' => $data['duration_hours']
    //                 ])->render();

    //                 $fileName = $request->get('user_id') . '_' . md5(now());

    //                 $browserShot = new Browsershot();

    //                 $browserShot->html($pdfHTML)->setNodeBinary('/opt/cpanel/ea-nodejs20/bin/node')->setNpmBinary('/opt/cpanel/ea-nodejs20/bin/npm')
    //                     ->margins(10, 0, 10, 0)
    //                     ->format('A4')
    //                     ->showBackground()
    //                     ->landscape()
    //                     ->savePdf(public_path("upload/files/certificates/$fileName.pdf"));

    //                 $browserShot->html($pdfHTML)->setNodeBinary('/opt/cpanel/ea-nodejs20/bin/node')->setNpmBinary('/opt/cpanel/ea-nodejs20/bin/npm')
    //                     ->margins(10, 0, 10, 0)
    //                     ->format('A4')
    //                     ->showBackground()
    //                     ->landscape()
    //                     ->save(public_path("upload/files/certificates/$fileName.png"));

    //                 $data['from'] = '';
    //                 $data['file_pdf'] = "$fileName.pdf";
    //                 $data['image'] = "$fileName.png";

    //                 $certificate = Certificate::create($data);

    //                 try {
    //                     $user->notify(new SuccessfullyGenerateCertificateNotification($certificate));
    //                     $user->notify(new SuccessfullyGeneratedCertEmail($certificate)); //send email with pdf
    //                 } catch (\Exception $exception) {
    //                     Log::error($exception);
    //                 }

    //             } catch (\Exception $exception) {
    //                 Log::error($exception);
    //             }
    //         }
    //     }

    //     return ApiHelper::output(trans('app.success'));
    // }
    public function complete($id, Request $request)
    {
        // Find the lesson
        $lesson = Lesson::find($id);
        if (!$lesson) {
            return ApiHelper::output(trans('app.lesson_not_found'), 0);
        }

        // Check if user is authenticated
        if (!auth('api')->check()) {
            return ApiHelper::output(trans('app.unauthenticated'), 0);
        }

        // Validate user_id
        $userId = $request->get('user_id');
        if (auth('api')->id() !== (int) $userId) {
            return ApiHelper::output(trans('app.unauthorized'), 0);
        }

        // Fetch the last completed lesson for the user
        $lastCompletedLesson = DB::table('users_complete_lessons')
            ->where('user_id', $userId)
            ->where('lesson_id', $id) // Fixed: Use where instead of whereIn
            ->latest('created_at')
            ->first();

        // Check if at least 1 hour has passed since the last completion
        if ($lastCompletedLesson) {
            $lastCompletedAt = Carbon::parse($lastCompletedLesson->created_at);
            $now = Carbon::now();
            if ($now->diffInHours($lastCompletedAt) < 1) {
                return ApiHelper::output(trans('app.complete_lesson_soon'), 0);
            }
        }

        // Mark the lesson as completed
        $lesson->userCompleted()->syncWithoutDetaching($userId);

        // Get all lessons in the course
        $lessons = $lesson->course->lessons()->get();
        $lessonsCount = $lessons->count();

        // Count completed lessons for the user in the course
        $completedLessonsCount = DB::table('users_complete_lessons')
            ->where('user_id', $userId)
            ->whereIn('lesson_id', $lessons->pluck('id')) // Fixed: Use get() to resolve query
            ->count();

        // Check if certificate already exists
        $isUserGenerated = auth('api')
            ->user()
            ->certificates()
            ->where('related_type', Course::class)
            ->where('related_id', $lesson->course_id)
            ->exists();

        if ($lessonsCount === $completedLessonsCount) {
            Log::info("message email send successfully");
            try {
                // Fetch completion timestamps
                $completionDates = DB::table('users_complete_lessons')
                    ->where('user_id', $userId)
                    ->whereIn('lesson_id', $lessons->pluck('id'))
                    ->pluck('created_at');

                if ($completionDates->isEmpty()) {
                    Log::error('No completion dates found for certificate generation');
                    return ApiHelper::output(trans('app.error'), 0);
                }

                $firstCompletedAt = Carbon::parse($completionDates->min());
                $lastCompletedAt = Carbon::parse($completionDates->max());

                // Prepare certificate data
                $data = [
                    'related_id' => $lesson->course_id,
                    'related_type' => Course::class,
                    'user_id' => $userId,
                    'duration_hours' => $lastCompletedAt->diffInHours($firstCompletedAt),
                    'duration_days' => $lastCompletedAt->diffInDays($firstCompletedAt),
                    'start_date' => $firstCompletedAt->format('Y/m/d'),
                    'end_date' => $lastCompletedAt->format('Y/m/d'),
                ];

                $related = Course::find($data['related_id']);
                $user = User::find($userId);

                if (!$related || !$user) {
                    Log::error('Course or user not found for certificate generation');
                    return ApiHelper::output(trans('app.error'), 0);
                }

                // Render certificate HTML
                $pdfHTML = view('certificate-pdf', [
                    'countDays' => $data['duration_days'],
                    'courseName' => $related->title ?? $related->name ?? 'Unknown Course',
                    'days' => $data['duration_days'],
                    'endDate' => $data['end_date'],
                    'nationalId' => $user->national_id ?? 'N/A',
                    'startDate' => $data['start_date'],
                    'user_name' => $user->name ?? 'Unknown User',
                    'hours' => $data['duration_hours'],
                ])->render();

                // Ensure certificate directory exists
                $directory = public_path('upload/files/certificates');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $fileName = "{$userId}_" . md5(now());

                // Generate PDF and image with a single Browsershot instance
                $browserShot = new Browsershot();
                $browserShot->html($pdfHTML)
                    ->setNodeBinary('/opt/cpanel/ea-nodejs20/bin/node')
                    ->setNpmBinary('/opt/cpanel/ea-nodejs20/bin/npm')
                    ->margins(10, 0, 10, 0)
                    ->format('A4')
                    ->showBackground()
                    ->landscape();

                $browserShot->savePdf("{$directory}/{$fileName}.pdf");
                $browserShot->save("{$directory}/{$fileName}.png");

                // Save certificate data
                $data['from'] = '';
                $data['file_pdf'] = "{$fileName}.pdf";
                $data['image'] = "{$fileName}.png";

                // Create certificate within a transaction
                $certificate = DB::transaction(function () use ($data) {
                    return Certificate::create($data);
                });

                // Notify user
                try {
                    $user->notify(new SuccessfullyGenerateCertificateNotification($certificate));
                    $user->notify(new SuccessfullyGeneratedCertEmail($certificate));
                } catch (\Exception $exception) {
                    Log::error("Notification error: {$exception->getMessage()}");
                }
            } catch (\Exception $exception) {
                Log::error("Certificate generation error: {$exception->getMessage()}");
                return ApiHelper::output(trans('app.error'), 0);
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
