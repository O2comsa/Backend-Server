<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\LiveEvent;
use App\Models\User;
use App\Notifications\SuccessfullySubscriptionLiveEventNotification;
use App\Services\Paytabs\PaytabService;
use App\Services\Zoom\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\FlareClient\Api;
use Termwind\Components\Li;
use App\Notifications\SuccessfullyBuyEvent;

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
        $liveEvents = LiveEvent::query()
            ->active()
            ->withAvailableSeats()
            ->get();

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
        ApiHelper::validate($request, [
            'liveEvent_id' => "required|exists:live_events,id",
        ]);

        $liveEvent = LiveEvent::query()->findOrFail($request->get('liveEvent_id'));
        
        if($liveEvent->number_of_seats){
            if($liveEvent->number_of_seats <= $liveEvent->usersAttendee()->count()){
                return ApiHelper::output( 'لا تسطيع الحجز الان لان كل المقاعد مكتملة', 0);
            }    
        }

        // edit now $argv

        if (!$liveEvent->is_paid || empty($liveEvent->price)) {
            $liveEvent->usersAttendee()->syncWithoutDetaching($request->get('user_id'));

            auth('api')->user()->notify(new SuccessfullySubscriptionLiveEventNotification($liveEvent));
            $user = User::find($request->get('user_id'));
            $user->notify(new SuccessfullyBuyEvent($liveEvent));
            $serve = new ZoomService();

            $serve->addMeetingRegistrant($liveEvent->meeting->meeting_id, [
                'first_name' => auth('api')->user()->name,
                'last_name' => ' User',
                'email' => auth('api')->user()->email
            ], $request->get('user_id'));

            return ApiHelper::output(['message' => 'هذا القاموس مجانا ولا داعي للدفع']);
        }

        $user = User::find($request->get('user_id'));

        $dateTime = time();

        $result = $this->paytabService->create_pay_page([
            "cart_description" => "اشتراك ندوة : {$liveEvent->name}",
            "cart_id" => "{$user->id}-liveEvent-{$request->get('liveEvent_id')}-{$dateTime}",
            "cart_amount" => $liveEvent->price,
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

          $paytab =   \App\Models\Paytabs::query()
                ->create([
                    'payment_reference' => $result->responseResult->tran_ref,
                    'user_id' => $request->get('user_id'),
                    'related_id' => $liveEvent->id,
                    'create_response' => $result,
                    'related_type' => LiveEvent::class
                ]);

                if($paytab){
                    if($paytab->verify_payment_response){
                        $user->notify(new SuccessfullyBuyEvent($liveEvent));
                    }
                    
                }
              
                
            return ApiHelper::output($result);
        } else {
            return ApiHelper::output($result->errors, 0);
        }
    }
}
