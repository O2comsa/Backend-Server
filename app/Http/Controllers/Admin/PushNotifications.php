<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\FCMNotification;
use Google\Client as GoogleClient;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendNotificationToAllUsersJob;


class PushNotifications extends Controller
{
    use FCMNotification;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:pushNotifications.manage');
    }

    public function index()
    {
        return view('Admin.push_notifications.index');
    }

    public function store(Request $request)
    {
        PushNotifications::sendNotificationToAll($request->title, $request->message);
        return back()->withSuccess(trans('app.message_sended'));
    }

    //
    public static function sendMessage($message, $device_tokens, $data = null)
    {
        \Log::info($message . ' - ' . $device_tokens);

        if (!is_array($device_tokens)) {
            $device_tokens = [$device_tokens];
        }

        try {
            $content = ['en' => $message];
            $fields = array(
                'app_id' => env('ONE_SIGNAL_ID'),
                'include_player_ids' => $device_tokens,
                'contents' => $content,
                'ios_badgeType' => 'Increase',
                'ios_badgeCount' => 1
            );

            $fields = json_encode($fields);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . env('ONE_SIGNAL_KEY')
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            curl_close($ch);

            \Log::info('onesignal response' . ' - ' . $response);

            return $response;
        } catch (\Exception $exception) {
            \Log::debug($exception->getMessage());
        }
    }




    /* public static function sendNotificationToAll($title, $message)
    {
        try {
            $content = ['en' => $message];
            $headings = ['en' => $title];
            $fields = array(
                'app_id' => env('ONE_SIGNAL_ID'),
                'included_segments' => array(
                    'All',
                ),
                'contents' => $content,
                'headings' => $headings,
                'ios_badgeType' => 'Increase',
                'ios_badgeCount' => 1
            );

            $fields = json_encode($fields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . env('ONE_SIGNAL_KEY'),
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        } catch (\Exception $exception) {

        }
    
    } 
    */

    // public static function sendNotificationToAll($title, $message)
    // {
    //     try {
    //         // Fetch all users with non-null deviceToken
    //         $users = User::whereNotNull('device_token')->get();
    //         foreach($users as $user) {
    //             $title = $title;
    //             $description = $message;
    //             $projectId = "eshartiapp";
    
    //             $credentialsFilePath = Storage::path('json/file.json');
    //             $client = new GoogleClient();
    //             $client->setAuthConfig($credentialsFilePath);
    //             $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    //             $client->refreshTokenWithAssertion();
    //             $token = $client->getAccessToken();
    
    //             $access_token = $token['access_token'];
    
    //             $headers = [
    //                 "Authorization: Bearer $access_token",
    //                 'Content-Type: application/json'
    //             ];
    
    //             $data = [
    //                 "message" => [
    //                     "token" => $user->device_token,
    //                     "notification" => [
    //                         "title" => $title,
    //                         "body" => $description,
    //                     ],
    //                 ]
    //             ];
    //             $payload = json_encode($data);
    
    //             $ch = curl_init();
    //             curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
    //             curl_setopt($ch, CURLOPT_POST, true);
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    //             curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
    //             $response = curl_exec($ch);
    //             $err = curl_error($ch);
    //             curl_close($ch);

    //         }
     

    //         return "Notifications sent to all users with deviceToken.";
    //     } catch (\Exception $exception) {
    //         // Handle exceptions here
    //         return $exception->getMessage();
    //     }
    // }

    public static function sendNotificationToAll($title, $message)
    {
        User::whereNotNull('device_token')->chunk(100, function($users) use ($title, $message) {
            dispatch(new SendNotificationToAllUsersJob($users, $title, $message));
        });
        // SendNotificationToAllUsersJob::dispatch($title, $message);
    }

}
