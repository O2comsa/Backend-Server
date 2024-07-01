<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;


class PushNotifications extends Controller
{
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
        \Log::info($message .' - '. $device_tokens);

        if (!is_array($device_tokens)){
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

        public static function sendNotificationToAll($title, $message)
    {
        try {
            // Fetch all users with non-null deviceToken
            $users = User::whereNotNull('device_token')->get();
            
            foreach ($users as $user) {
                $data = [
                    'notification' => [
                        'title' => $title,
                        'body' => $message,
                    ],
                    'to' => $user->device_token, // Target individual device token
                ];

                $dataString = json_encode($data);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: key=' . 'AAAATb_rC30:APA91bEhDZDQwQJTmvDN7NL0IoLsQ8txcwdI_DjRzAjIIztMKFNLC0EnZ735KY4JGgRWIcFWE49W0qBI54-vJTErx8CvqgNoVYGw0PX268HPPcsSAoJUv1SDprlQ4SzaokxOsTRWIqsD', // Your server key
                    'Content-Type: application/json',
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($ch);
                curl_close($ch);
                // Optionally, you can log or handle the response here
                // Log::info('Notification sent: ' . $response);
            }

            return "Notifications sent to all users with deviceToken.";
        } catch (\Exception $exception) {
            // Handle exceptions here
            return $exception->getMessage();
        }
    }
}
