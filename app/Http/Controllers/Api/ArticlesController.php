<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $articles = Article::query()
            ->select(['id', 'title', 'description', 'image']);

        if ($request->has('keyword')) {
            $articles = $articles->where(function ($query) use ($request) {
                $query->where('title', 'like', "%{$request->keyword}%")
                    ->orWhere('description', 'like', "%{$request->keyword}%");
            });
        }

        $articles = $articles->latest()
            ->paginate(10);

        return ApiHelper::output($articles);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ApiHelper::output(Article::select(['id', 'title', 'description', 'image'])->find($id));
    }

    public function latest()
    {
        return ApiHelper::output(Article::select(['id', 'title', 'description', 'image'])->latest()->limit(10)->get());
    }

    /**
     * set lesson to viewed.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookmark($id)
    {
        $article = Article::find($id);

        if (auth('api')->check()) {
            $article?->bookmarks()->toggle(\request()->get('user_id'));
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
        $articles = Article::query()
            ->whereHas('bookmarks', function ($query) use ($request) {
                $query->where('user_id', $request->get('user_id'));
            })
            ->get();

        return ApiHelper::output($articles);
    }
}
