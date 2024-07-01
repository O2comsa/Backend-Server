<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $settings = Setting::select(['key','value','type'])->get();
        return ApiHelper::output($settings);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        //
        try {
            $settings = Setting::where('key',$id)->select(['key','value','type'])->firstOrFail();
        }catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $settings = 'Invalid key.';
        }
        return ApiHelper::output($settings);
    }

}
