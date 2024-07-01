<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Dictionary;
use App\Models\LiveEvent;
use App\Models\Paytabs;
use App\Models\Plan;
use App\Models\Transaction;
use App\Notifications\SuccessfullyBuyDictionaryNotification;
use App\Notifications\SuccessfullyBuyPlanNotification;
use App\Notifications\SuccessfullyPaymentNotification;
use App\Notifications\SuccessfullySubscriptionCourseNotification;
use App\Notifications\SuccessfullySubscriptionLiveEventNotification;
use App\Services\Paytabs\PaytabService;
use App\Services\Zoom\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaytabsController extends Controller
{
    /**
     * @var PaytabService
     */
    private $paytabService;

    public function __construct()
    {
        $this->paytabService = new PaytabService();
    }

    public function verify_payment(Request $request)
    {
        Log::info('verify_payment before request', $request->all());

        ApiHelper::validate($request, [
            'tran_ref' => "required_without_all:tranRef|exists:paytabs,payment_reference",
            'tranRef' => "required_without_all:tran_ref|exists:paytabs,payment_reference",
        ]);

        Log::info('verify_payment request', $request->all());

        $data = $this->paytabService->verify_payment($request->all());

        Log::info('check $data', (array)$data);

        $this->successPayment($data, $request);

        return response()->json($data);
    }

    public function successPayment($data, $request)
    {
        if ($data->success) {
            $paytabs = Paytabs::where('payment_reference', $request->get('tran_ref') ?? $request->get('tranRef'))->first();

            Log::info('check $paytabs->paid', (array)$paytabs);

            if (!$paytabs->paid) {

                $related = $paytabs->related;

                if ($related instanceof Course) {
                    Course::find($paytabs->related_id)
                        ->subscription()
                        ->syncWithoutDetaching($paytabs->user_id);

                    try {

                        $paytabs->user->notify(new SuccessfullySubscriptionCourseNotification($paytabs->related));
                    } catch (\Exception $exception) {
                    }

                } elseif ($related instanceof LiveEvent) {
                    LiveEvent::find($paytabs->related_id)
                        ->usersAttendee()
                        ->syncWithoutDetaching($paytabs->user_id);

                    try {
                        $serve = new ZoomService();

                        $serve->addMeetingRegistrant($paytabs->related->meeting->meeting_id, [
                            'first_name' => $paytabs->user->name,
                            'last_name' => ' User',
                            'email' => $paytabs->user->email
                        ], $paytabs->user_id);

                        $paytabs->user->notify(new SuccessfullySubscriptionLiveEventNotification($paytabs->related));
                    } catch (\Exception $exception) {
                    }

                } else if ($related instanceof Dictionary) {
                    Dictionary::find($paytabs->related_id)
                        ->users()
                        ->syncWithoutDetaching($paytabs->user_id);

                    try {
                        $paytabs->user->notify(new SuccessfullyBuyDictionaryNotification($paytabs->related));
                    } catch (\Exception $exception) {
                    }

                } else if ($related instanceof Plan) {
                    Plan::find($paytabs->related_id)
                        ->users()
                        ->syncWithoutDetaching($paytabs->user_id);

                    try {
                        $paytabs->user->notify(new SuccessfullyBuyPlanNotification($paytabs->related));
                    } catch (\Exception $exception) {
                    }

                }

                $in = 0;

                if ($data->success) {
                    if (!empty($data->responseResult->cart_amount)) {
                        $in = $data->responseResult->cart_amount;
                    } elseif (!empty($data->cart_amount)) {
                        $in = $data->cart_amount;
                    }
                }

                $transactionName = $paytabs?->related?->name ?? $paytabs?->related?->title;

                $transaction = Transaction::query()
                    ->create([
                        'user_id' => $paytabs->user_id,
                        'balance' => 0,
                        'in' => $in,
                        'out' => 0,
                        'note' => "تحصيل قمية اشتراك : {$transactionName}"
                    ]);

                $paytabs->update(['verify_payment_response' => $data->responseResult, 'paid' => $data->success, 'transaction_id' => $transaction->id]);

                try {
                    $transaction->user->notify(new SuccessfullyPaymentNotification($paytabs));
                } catch (\Exception $exception) {
                }
            }
        }
    }

    public function successfully_payment(Request $request)
    {
        $data = null;

        if ($request->hasAny(['tran_ref', 'tranRef'])) {
            Log::info('verify_payment before request', $request->all());

            ApiHelper::validate($request, [
                'tran_ref' => "required_without_all:tranRef|exists:paytabs,payment_reference",
                'tranRef' => "required_without_all:tran_ref|exists:paytabs,payment_reference",
            ]);

            Log::info('verify_payment request', $request->all());

            $data = $this->paytabService->verify_payment($request->all());

            Log::info('check $data', (array)$data);

            $this->successPayment($data, $request);
        }

        if ($data && $data->success) {
            return view('Paytabs.thanks');
        } else {
            return redirect()->route('paytabs.fail_payment');
        }
    }

    public function fail_payment(Request $request)
    {
        $data = null;

        if ($request->hasAny(['tran_ref', 'tranRef'])) {
            Log::info('verify_payment before request', $request->all());

            ApiHelper::validate($request, [
                'tran_ref' => "required_without_all:tranRef|exists:paytabs,payment_reference",
                'tranRef' => "required_without_all:tran_ref|exists:paytabs,payment_reference",
            ]);

            Log::info('verify_payment request', $request->all());

            $data = $this->paytabService->verify_payment($request->all());

            Log::info('check $data', (array)$data);

            $this->successPayment($data, $request);
        }

        if ($data && $data->success) {
            return view('Paytabs.thanks');
        } else {
            return view('Paytabs.fail');
        }
    }
}
