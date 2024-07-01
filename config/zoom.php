<?php

return [
    'account_id' => env('ZOOM_ACCOUNT_ID'),
    'client_id' => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),
    'cache_token' => env('ZOOM_CACHE_TOKEN', true),
    'base_url' => 'https://api.zoom.us/v2/',
    'authentication_method' => 'Oauth', // Only Oauth compatible at present
    'max_api_calls_per_request' => '5', // how many times can we hit the api to return results for an all() request

    'base_zoom_url' => 'https://zoom.us/',
    'redirect_uri' => env('ZOOM_REDIRECT_URI'),
    'credential_path' => env('ZOOM_CREDENTIAL_PATH', '/storage/zoom/zoom_credential.json'),
    'setting' => \App\Models\ZoomGlobalSetting::class,
];
