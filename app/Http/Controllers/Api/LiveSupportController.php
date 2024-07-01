<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Helpers\LiveSupportRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\LiveSupportRequest;
use App\Models\Plan;
use Illuminate\Http\Request;

class LiveSupportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ApiHelper::output([
            'status' => true,
            'currentRequest' => $this->currentLiveSupportRequest()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (count($this->currentLiveSupportRequest())) {
            return ApiHelper::output('لديك طلب جاري بالفعل', 0);
        }

        $liveSupport = LiveSupportRequest::query()
            ->create([
                'user_id' => auth('api')->user()->id,
                'status' => LiveSupportRequestStatus::WAITING_STATUS,
                'plan_id' => Plan::first()?->id,
                'duration' => 0
            ]);

        return ApiHelper::output($liveSupport);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $liveSupport = LiveSupportRequest::query()->findOrFail($id);

        return ApiHelper::output($liveSupport);
    }

    public function currentLiveSupportRequest()
    {
        return LiveSupportRequest::query()
            ->where('user_id', auth('api')->user()->id)
            ->where(function ($query) {
                $query
                    ->where('status', LiveSupportRequestStatus::WAITING_STATUS)
                    ->orWhere('status', LiveSupportRequestStatus::ACCEPTED_STATUS)
                    ->orWhere('status', LiveSupportRequestStatus::IN_PROGRESS_STATUS);
            })
            ->get();
    }
}
