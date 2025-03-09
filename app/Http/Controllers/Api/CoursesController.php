<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Helpers\CourseStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CoursesResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $courses = Course::select(['id', 'image', 'title', 'description', 'price', 'free'])
            ->where('status', CourseStatus::ACTIVE);

        if ($request->has('keyword')) {
            $courses = $courses->where(function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->keyword}%")
                    ->orWhere('description', 'like', "%{$request->keyword}%");
            });
        }

        $courses = $courses->withCount('lessons')
            ->whereHas('lessons')
            ->latest()
            ->paginate(10);
        return ApiHelper::output($courses);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return CoursesResource
     */
    public function show($id)
    {
        $course = Course::select(['id', 'title', 'description', 'image', 'price', 'free'])
            ->where('id', $id)
            ->where('status', CourseStatus::ACTIVE)
            ->with(['lessons', 'bookmarks', 'courses', 'lessons:id,title,image,course_id'])
            ->withCount('lessons')
            ->whereHas('lessons')
            ->first();

        if (!isset($course)) {
            return ApiHelper::output(trans('app.404'), 0);
        }

        return new CoursesResource($course);
    }

    public function myCourses(Request $request)
    {
        $courses = Course::query()
            ->whereHas('subscription', function ($query) {
                $query->where('users.id', \request()->get('user_id'));
            })
            ->select(['id', 'image', 'title', 'description', 'price', 'free'])
            ->where('status', CourseStatus::ACTIVE);


        if ($request->has('keyword')) {
            $courses = $courses->where(function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->keyword}%")
                    ->orWhere('description', 'like', "%{$request->keyword}%");
            });
        }

        $courses = $courses
            ->whereHas('lessons')
            ->withCount('lessons')
            ->latest()
            ->paginate(10);

        return ApiHelper::output($courses);
    }

    public function bookmark($id)
    {
        $course = Course::find($id);

        if (auth('api')->check()) {
            $course?->bookmarks()->toggle(\request()->get('user_id'));
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
        $articles = Course::query()
            ->whereHas('bookmarks', function ($query) use ($request) {
                $query->where('user_id', $request->get('user_id'));
            })
            ->get();

        return ApiHelper::output($articles);
    }
}
