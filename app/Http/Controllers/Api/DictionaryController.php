<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Helpers\DictionaryStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Dictionary;
use App\Models\User;
use App\Notifications\SuccessfullyBuyDictionaryNotification;
use App\Services\Paytabs\PaytabService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DictionaryController extends Controller
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
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $dictionaries = Dictionary::query()
            ->where('status', DictionaryStatus::ACTIVE)
            ->select([
                'id',
                'title',
                'description',
                'file_pdf',
                'image',
                'is_paid',
                'price',
                'status',
                'created_at'
            ])
            ->latest()
            ->paginate();

        return ApiHelper::output($dictionaries);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dictionary = Dictionary::query()->findOrFail($id);

        return ApiHelper::output($dictionary);
    }

    /**
     * set lesson to viewed.
     *
     * @param int $id
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function bookmark($id)
    {
        $dictionary = Dictionary::find($id);

        if (auth('api')->check()) {
            $dictionary?->bookmarks()->toggle(\request()->get('user_id'));
        }

        return ApiHelper::output(trans('app.success'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function buyDictionary(Request $request)
    {
        ApiHelper::validate($request, [
            'dictionary_id' => "required|exists:dictionaries,id",
        ]);

        $dictionary = Dictionary::query()->findOrFail($request->get('dictionary_id'));

        if (!$dictionary->is_paid || empty($dictionary->price)) {
            $dictionary->users()->syncWithoutDetaching(auth('api')->user()->id);

            auth('api')->user()->notify(new SuccessfullyBuyDictionaryNotification($dictionary));

            return ApiHelper::output(['message' => 'هذا القاموس مجانا ولا داعي للدفع']);
        }

        $user = User::find($request->get('user_id'));

        $dateTime = time();

        $result = $this->paytabService->create_pay_page([
            "cart_description" => "اشتراك قاموس : {$dictionary->name}",
            "cart_id" => "{$user->id}-dictionary-{$request->get('dictionary_id')}-{$dateTime}",
            "cart_amount" => $dictionary->price,
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
                    'related_id' => $dictionary->id,
                    'create_response' => $result,
                    'related_type' => Dictionary::class
                ]);

            return ApiHelper::output($result);
        } else {
            return ApiHelper::output($result->errors, 0);
        }
    }

    /**
     * set lesson to viewed.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listBookmarked(Request $request)
    {
        $Dictionaries = Dictionary::query()
            ->whereHas('bookmarks', function ($query) use ($request) {
                $query->where('user_id', $request->get('user_id'));
            })
            ->get();

        return ApiHelper::output($Dictionaries);
    }

    /**
     * set lesson to viewed.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myDictionary(Request $request)
    {
        $Dictionaries = Dictionary::query()
            ->whereHas('users', function ($query) {
                $query->where('user_id', \request()->get('user_id'));
            })
            ->get();

        return ApiHelper::output($Dictionaries);
    }
}
