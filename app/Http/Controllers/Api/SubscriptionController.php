<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Notifications\SuccessfullySubscriptionCourseNotification;
use App\Services\Paytabs\PaytabService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * @var PaytabService
     */
    private $paytabService;

    public function __construct()
    {
        $this->paytabService = new PaytabService();
    }

    public function freeCourseSubscription(Request $request)
    {
        ApiHelper::validate($request, [
            'course_id' => "required|exists:courses,id",
        ]);
        $course = Course::find($request->get('course_id'));

        if ($course->subscribed) {
            return ApiHelper::output(trans('app.it_is_not_possible_to_participate_in_a_previously_subscribed_course'), 0);
        }

        if ($course->free) {
            if ($course->eligible) {
                $course->subscription()->syncWithoutDetaching($request->get('user_id'));

                auth('api')->user()->notify(new SuccessfullySubscriptionCourseNotification($course));

                return ApiHelper::output(trans('app.success_subscribed'));
            }
            return ApiHelper::output(trans('app.can_not_subscription_course_not_eligible'), 0);
        }
        return ApiHelper::output(trans('app.can_not_subscription_course_not_free'), 0);
    }

    public function paidCourseSubscription(Request $request)
    {
        ApiHelper::validate($request, [
            'course_id' => "required|exists:courses,id",
        ]);

        $course = Course::find($request->get('course_id'));

        if (!$course){
            return ApiHelper::output('بيانات خاطئة', 0);
        }

        if ($course?->subscribed) {
            return ApiHelper::output(trans('app.it_is_not_possible_to_participate_in_a_previously_subscribed_course'), 0);
        }

        if (!$course?->free) {
            if ($course->eligible) {
                $user = User::find($request->get('user_id'));

                $dateTime = time();

                $result = $this->paytabService->create_pay_page([
                    "cart_description" => "اشتراك في دورة : {$course->name}",
                    "cart_id" => "{$user->id}-course-{$request->get('course_id')}-{$dateTime}",
                    "cart_amount" => $course->price,
                    'customer_details' => [
                        "name" => $user->name,
                        "email" => $user->email,
                        "ip" => $_SERVER['REMOTE_ADDR']
                    ]
                ]);

                \Log::info('Payment ', (array)$result);

                if ($result->success) {

                    if (isset($result->responseResult)) {
                        $result->responseResult->payment_url = $result->responseResult->redirect_url;
                    }

                    \App\Models\Paytabs::query()
                        ->create([
                            'payment_reference' => $result->responseResult->tran_ref,
                            'user_id' => $request->get('user_id'),
                            'related_id' => $course->id,
                            'create_response' => $result,
                            'related_type' => Course::class
                        ]);

                    return ApiHelper::output($result);
                } else {
                    return ApiHelper::output($result->errors, 0);
                }
            }
            return ApiHelper::output(trans('app.can_not_subscription_course_not_eligible'), 0);
        }
        return ApiHelper::output(trans('app.can_not_subscription_course_is_free'), 0);
    }
}
