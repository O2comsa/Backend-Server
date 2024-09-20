<?php

namespace App\Jobs;

use Exception;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Google\Client as GoogleClient;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendNotificationToAllUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $message;

    public function __construct($title, $message)
    {
        $this->title = $title;
        $this->message = $message;
    }
    

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Fetch all users with non-null deviceToken
            $users = User::whereNotNull('device_token')->get();
            foreach ($users as $user) {
                $projectId = "eshartiapp";
    
                $credentialsFilePath = Storage::path('json/file.json');
                $client = new GoogleClient();
                $client->setAuthConfig($credentialsFilePath);
                $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                $client->refreshTokenWithAssertion();
                $token = $client->getAccessToken();
    
                $access_token = $token['access_token'];
    
                $headers = [
                    "Authorization: Bearer $access_token",
                    'Content-Type: application/json'
                ];
    
                $data = [
                    "message" => [
                        "token" => $user->device_token,
                        "notification" => [
                            "title" => $this->title,
                            "body" => $this->message,
                        ],
                    ]
                ];
                $payload = json_encode($data);
    
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $response = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);

                if ($err) {
                    throw new Exception('CURL Error: ' . $err);
                }
            }

            return "Notifications sent to all users with deviceToken.";
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
