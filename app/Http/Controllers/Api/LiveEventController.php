<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Paytabs;
use App\Models\LiveEvent;
use App\Helpers\ApiHelper;
use App\Models\Transaction;
use Spatie\FlareClient\Api;
use Termwind\Components\Li;
use Illuminate\Http\Request;
use App\Services\Zoom\ZoomService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Paytabs\PaytabService;
use App\Notifications\SuccessfullyBuyEvent;
use App\Notifications\SuccessfullySubscriptionLiveEventNotification;


class LiveEventController extends Controller
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
        $liveEvents = LiveEvent::active()->get();


        return ApiHelper::output($liveEvents);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $liveEvents = LiveEvent::query()->findOrFail($id);

        return ApiHelper::output($liveEvents);
    }

    // public function buyEvent(Request $request)
    // {
    //     // Validate request inputs
    //     ApiHelper::validate($request, [
    //         'liveEvent_id' => "required|exists:live_events,id",
    //     ]);

    //     // Fetch user and event ID from request
    //     $user = User::find($request->get('user_id'));
    //     $liveEventId = $request->get('liveEvent_id');
    //     $eventRow = LiveEvent::find($liveEventId);

    //     //if free
    //     if (!$eventRow->is_paid) {
    //         $attendeesNumber = DB::table('live_event_attendees')->where('live_event_id', $eventRow->id)->count();

    //         // Check seat availability if limited
    //         if ( $attendeesNumber >= $eventRow->number_of_seats) {
    //             // return;
    //             return ApiHelper::output('لا تستطيع الحجز الان لان كل المقاعد مكتملة', 0);
    //         }

    //         $eventRow->usersAttendee()->syncWithoutDetaching($user->id);

    //         Transaction::create([
    //             'user_id' => $user->id,
    //             'in' => 0,
    //             'out' => 0,
    //             'order_id' => 0,
    //             'balance' => 0,
    //             'note' => "القاموس مجاني - $eventRow->name ",
    //             'is_free' => 1,
    //         ]);

    //         // Send notifications
    //         $user->notify(new SuccessfullySubscriptionLiveEventNotification($eventRow));
    //         $user->notify(new SuccessfullyBuyEvent($eventRow));

    //         // Register for Zoom meeting if applicable
    //         $zoomService = new ZoomService();
    //         $zoomService->addMeetingRegistrant($eventRow->meeting->meeting_id, [
    //             'first_name' => $user->name,
    //             'last_name' => 'User',
    //             'email' => $user->email,
    //         ], $user->id);

    //         return ApiHelper::output(['message' => 'هذا القاموس مجانا ولا داعي للدفع']);
    //     }

    //     // Lock the event row for update to prevent simultaneous purchases
    //     $liveEvent = LiveEvent::find($liveEventId);

    //     $attendeesNumber = DB::table('live_event_attendees')->where('live_event_id', $liveEvent->id)->count();

    //     // Check seat availability if limited
    //     if ($attendeesNumber >= $liveEvent->number_of_seats) {
    //         // return;
    //         return ApiHelper::output('لا تستطيع الحجز الان لان كل المقاعد مكتملة', 0);
    //     }

    //     // $eventRow->usersAttendee()->syncWithoutDetaching($user->id);

    //     // Handle paid events
    //     $dateTime = time();
    //     $paymentPageResult = $this->paytabService->create_pay_page([
    //         "cart_description" => "اشتراك دورة : {$liveEvent->name}",
    //         "cart_id" => "{$user->id}-liveEvent-{$liveEventId}-{$dateTime}",
    //         "cart_amount" => $liveEvent->price,
    //         'customer_details' => [
    //             "name" => $user->name,
    //             "email" => $user->email,
    //             "ip" => $_SERVER['REMOTE_ADDR'],
    //         ],
    //     ]);

    //     // Check if payment page creation was successful
    //     if ($paymentPageResult->success) {
    //         if (isset($paymentPageResult->responseResult)) {
    //             $paymentPageResult->responseResult->payment_url = $paymentPageResult->responseResult->redirect_url;
    //         }

    //         // Store payment details in the database
    //         $paytab = \App\Models\Paytabs::query()->create([
    //             'payment_reference' => $paymentPageResult->responseResult->tran_ref,
    //             'user_id' => $user->id,
    //             'related_id' => $liveEvent->id,
    //             'create_response' => $paymentPageResult,
    //             'related_type' => LiveEvent::class,
    //         ]);

    //         return ApiHelper::output($paymentPageResult);
    //     } else {
    //         // Return error response if payment page creation failed
    //         return ApiHelper::output($paymentPageResult->errors, 0);
    //     }
    // }

    public function buyEvent(Request $request)
    {
        // التحقق من المدخلات
        ApiHelper::validate($request, [
            'liveEvent_id' => "required|exists:live_events,id",
        ]);

        // جلب المستخدم والحدث
        $user = User::find($request->get('user_id'));
        $liveEventId = $request->get('liveEvent_id');
        $liveEvent = LiveEvent::findOrFail($liveEventId);

        DB::beginTransaction();

        try {
            // قفل السجل لمنع التداخل أثناء تعديل الحدث
            $liveEvent = LiveEvent::findOrFail($liveEventId);

            // إذا كان الحدث مجاني
            if (!$liveEvent->is_paid) {
                // تحقق من عدد الحضور
                $attendeesNumber = DB::table('live_event_attendees')
                    ->where('live_event_id', $liveEvent->id)
                    ->where('is_confirmed', true)
                    ->count();

                if ($attendeesNumber >= $liveEvent->number_of_seats) {
                    DB::rollBack();
                    return ApiHelper::output('لا تستطيع الحجز الآن لأن كل المقاعد ممتلئة', 0);
                }

                // إضافة المستخدم كحضور
                $liveEvent->usersAttendee()->syncWithoutDetaching($user->id);

                // تسجيل الحضور في جدول `live_event_attendees` مع حالة مؤقتة
                DB::table('live_event_attendees')->updateOrInsert(
                    ['live_event_id' => $liveEvent->id, 'user_id' => $user->id],
                    [
                        'is_confirmed' => true, // تأكيد الحجز
                        'reserved_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                DB::commit();

                return ApiHelper::output('تم التسجيل بنجاح في الحدث المجاني.');
            }

            // إذا كان الحدث مدفوع
            $attendeesNumber = DB::table('live_event_attendees')
                ->where('live_event_id', $liveEvent->id)
                ->where('is_confirmed', true)
                ->count();

            if ($attendeesNumber >= $liveEvent->number_of_seats) {
                DB::rollBack();
                return ApiHelper::output('لا تستطيع الحجز الآن لأن كل المقاعد ممتلئة', 0);
            }

            // حجز مقعد مؤقت قبل الدفع
            $liveEvent->usersAttendee()->syncWithoutDetaching($user->id);

            // تحديث جدول `live_event_attendees` بالحجز المؤقت
            DB::table('live_event_attendees')->updateOrInsert(
                ['live_event_id' => $liveEvent->id, 'user_id' => $user->id],
                [
                    'is_confirmed' => false, // حجز مؤقت
                    'reserved_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::commit();

            // إرسال طلب إلى بوابة الدفع
            $dateTime = time();
            $paymentPageResult = $this->paytabService->create_pay_page([
                "cart_description" => "اشتراك دورة : {$liveEvent->name}",
                "cart_id" => "{$user->id}-liveEvent-{$liveEventId}-{$dateTime}",
                "cart_amount" => $liveEvent->price,
                'customer_details' => [
                    "name" => $user->name,
                    "email" => $user->email,
                    "ip" => $_SERVER['REMOTE_ADDR'],
                ],
            ]);

            if ($paymentPageResult->success) {
                if (isset($paymentPageResult->responseResult)) {
                    $paymentPageResult->responseResult->payment_url = $paymentPageResult->responseResult->redirect_url;
                }

                // Store payment details in the database
                $paytab = \App\Models\Paytabs::query()->create([
                    'payment_reference' => $paymentPageResult->responseResult->tran_ref,
                    'user_id' => $user->id,
                    'related_id' => $liveEvent->id,
                    'create_response' => $paymentPageResult,
                    'related_type' => LiveEvent::class,
                ]);

                return ApiHelper::output($paymentPageResult);
            } else {
                // Return error response if payment page creation failed
                return ApiHelper::output($paymentPageResult->errors, 0);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error buying event: ' . $e->getMessage());
            return ApiHelper::output('حدث خطأ أثناء العملية', 0);
        }
    }

    private function releaseSeat($eventId, $userId)
    {
        // حذف الحجز المؤقت في حال فشل الدفع
        DB::table('live_event_attendees')
            ->where('live_event_id', $eventId)
            ->where('user_id', $userId)
            ->where('is_confirmed', false)
            ->delete();
    }
}
