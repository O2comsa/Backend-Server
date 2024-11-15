<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
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

    public function buyEvent(Request $request)
    {
        // Validate request inputs
        ApiHelper::validate($request, [
            'liveEvent_id' => "required|exists:live_events,id",
        ]);

        // Fetch user and event ID from request
        $user = User::find($request->get('user_id'));
        $liveEventId = $request->get('liveEvent_id');
        $eventRow = LiveEvent::find($liveEventId);
        if (!$eventRow->is_paid || empty($eventRow->price)) {
            // Check seat availability if limited
            if ($eventRow->number_of_seats && $eventRow->number_of_seats <= $eventRow->usersAttendee()->count()) {
                return;
                return ApiHelper::output('لا تستطيع الحجز الان لان كل المقاعد مكتملة', 0);
            }
            Transaction::create([
                'user_id' => $user->id,
                'in' => 0,
                'out' => 0,
                'order_id' => 0,
                'balance' => 0,
                'note' => 'القاموس مجاني',
                'is_free' => 1,
            ]);
        }

        return DB::transaction(function () use ($liveEventId, $user, $request) {
            // Lock the event row for update to prevent simultaneous purchases
            $liveEvent = LiveEvent::query()->where('id', $liveEventId)->lockForUpdate()->firstOrFail();

            // Check seat availability if limited
            if ($liveEvent->number_of_seats && $liveEvent->number_of_seats <= $liveEvent->usersAttendee()->count()) {
                return;
                return ApiHelper::output('لا تستطيع الحجز الان لان كل المقاعد مكتملة', 0);
            }

            // Handle free events
            if (!$liveEvent->is_paid || empty($liveEvent->price)) {
                // Add user to event attendees without detaching others
                $liveEvent->usersAttendee()->syncWithoutDetaching($user->id);

                // Send notifications
                $user->notify(new SuccessfullySubscriptionLiveEventNotification($liveEvent));
                $user->notify(new SuccessfullyBuyEvent($liveEvent));

                // Register for Zoom meeting if applicable
                $zoomService = new ZoomService();
                $zoomService->addMeetingRegistrant($liveEvent->meeting->meeting_id, [
                    'first_name' => $user->name,
                    'last_name' => 'User',
                    'email' => $user->email,
                ], $user->id);

                return ApiHelper::output(['message' => 'هذا القاموس مجانا ولا داعي للدفع']);
            }

            // Handle paid events
            $dateTime = time();
            $paymentPageResult = $this->paytabService->create_pay_page([
                "cart_description" => "اشتراك ندوة : {$liveEvent->name}",
                "cart_id" => "{$user->id}-liveEvent-{$liveEventId}-{$dateTime}",
                "cart_amount" => $liveEvent->price,
                'customer_details' => [
                    "name" => $user->name,
                    "email" => $user->email,
                    "ip" => $_SERVER['REMOTE_ADDR'],
                ],
            ]);

            // Check if payment page creation was successful
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
        });
    }
}
