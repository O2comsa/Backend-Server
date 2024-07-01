<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Models\Banner;
use App\Http\Controllers\Controller;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $banners = Banner::select('id', 'image')->latest()->get();
        return ApiHelper::output($banners);
    }
}
