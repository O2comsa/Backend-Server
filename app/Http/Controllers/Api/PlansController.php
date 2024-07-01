<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Services\Paytabs\PaytabService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PlansController extends Controller
{
    /**
     * @var PaytabService
     */
    private $paytabService;

    public function __construct()
    {
        $this->paytabService = new PaytabService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = Plan::query()
            ->where('status', Plan::ACTIVE_STATUS)
            ->get();

        return ApiHelper::output($plans);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $plans = Plan::query()->findOrFail($id);

        return ApiHelper::output($plans);
    }

    public function buyPlan(Request $request)
    {
        ApiHelper::validate($request, [
            'plan_id' => "required|exists:plans,id",
        ]);

        $plan = Plan::query()->findOrFail($request->get('plan_id'));

        $user = User::find($request->get('user_id'));

        $dateTime = time();

        $result = $this->paytabService->create_pay_page([
            "cart_description" => "اشتراك في باقة دعم فني : {$plan->name}",
            "cart_id" => "{$user->id}-plan-{$request->get('plan_id')}-{$dateTime}",
            "cart_amount" => $plan->price,
            'customer_details' => [
                "name" => $user->name,
                "email" => $user->email,
                "ip" => $_SERVER['REMOTE_ADDR']
            ]
        ]);

        Log::info('Payment ', (array)$result);

        if ($result->success) {

            if (isset($result->responseResult)) {
                $result->responseResult->payment_url = $result->responseResult->redirect_url;
            }

            \App\Models\Paytabs::query()
                ->create([
                    'payment_reference' => $result->responseResult->tran_ref,
                    'user_id' => $request->get('user_id'),
                    'related_id' => $plan->id,
                    'create_response' => $result,
                    'related_type' => Plan::class
                ]);

            return ApiHelper::output($result);
        } else {
            return ApiHelper::output($result->errors, 0);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function myPlans(Request $request)
    {
        $plans = Plan::query()
            ->whereHas('users', function ($query) use ($request) {
                $query->where('user_id', $request->get('user_id'));
            })
            ->where('status', Plan::ACTIVE_STATUS)
            ->get();

        return ApiHelper::output($plans);
    }
}
