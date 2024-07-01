<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Helpers\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Course;
use App\Models\Dictionary;
use App\Models\Lesson;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        ApiHelper::validate($request, [
            'keyword' => 'required',
        ]);

        if (strlen($request->get('keyword')) < 3) {
            return ApiHelper::output([
                'articles' => [],
                'courses' => [],
                'dictionaries' => [],
                'lessons' => [],
            ]);
        }

        $articles = Article::query()
            ->where('title', 'like', "%{$request->keyword}%")
            ->orWhere('description', 'like', "%{$request->keyword}%")
            ->select(['id', 'title', 'description', 'image'])
            ->latest()
            ->get();

        $courses = Course::query()
            ->where('title', 'like', "%{$request->keyword}%")
            ->orWhere('description', 'like', "%{$request->keyword}%")
            ->select(['id', 'image', 'title', 'description', 'price', 'free'])
            ->where('status', CourseStatus::ACTIVE)
            ->withCount('lessons')
            ->whereHas('lessons')
            ->latest()
            ->get();

        $dictionaries = Dictionary::query()
            ->where('title', 'like', "%{$request->keyword}%")
            ->orWhere('description', 'like', "%{$request->keyword}%")
            ->select(['id', 'title', 'description', 'file_pdf', 'image'])
            ->latest()
            ->get();

        $lessons = Lesson::query()
            ->where('title', 'like', "%{$request->keyword}%")
            ->select(['id', 'title', 'video', 'lesson_time', 'image'])
            ->where('status', CourseStatus::ACTIVE)
            ->get();

        return ApiHelper::output([
            'articles' => $articles,
            'courses' => $courses,
            'dictionaries' => $dictionaries,
            'lessons' => $lessons,
        ]);
    }
}
