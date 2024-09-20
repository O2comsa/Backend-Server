<?php

namespace App\Traits;

use Exception;
use Google\Client;

trait FCMNotification
{

    public function getAccessToken($serviceAccountPath)
    {
        $client = new Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->useApplicationDefaultCredentials();
        $token = $client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
    }

    public function sendMessage($accessToken, $projectId, $message)
    {
        $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
        
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $message]));
        $response = curl_exec($ch);

        if ($response === false) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        $result = json_decode($response, true);
        return $result;
    }

    public function sendNotification($token, $title, $body)
    {
        
        $serviceAccountPath = 'firebase-service-account.json'; //-- Path to file (public folder)
        $projectId = env('FCM_PROJECT_ID');

        $message = [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];
        try {
            $accessToken = $this->getAccessToken($serviceAccountPath);
            
            $response = $this->sendMessage($accessToken, $projectId, $message);
            
            echo 'Message sent successfully: ' . print_r($response, true);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

}

