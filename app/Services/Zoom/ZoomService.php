<?php
// Copyright
declare(strict_types=1);


namespace App\Services\Zoom;

use App\Models\Meeting;
use App\Models\ZoomAccountsAccess;
use App\Models\ZoomMeeting;
use App\Models\ZoomRegistrant;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class ZoomService
{
    protected string $accessToken;
    protected Client $client;
    protected mixed $account_id;
    protected mixed $client_id;
    protected mixed $client_secret;
    protected mixed $credential_path;
    protected mixed $redirect_uri;
    private mixed $base_url_api;
    private mixed $base_url;
    private mixed $credential_data = null;

    public function __construct()
    {
        $this->client_id = config('zoom.client_id');
        $this->client_secret = config('zoom.client_secret');
        $this->account_id = config('zoom.account_id');
        $this->credential_path = config('zoom.credential_path');
        $this->redirect_uri = route('zoom.redirect-uri');
        $this->base_url = config('zoom.base_zoom_url', 'https://zoom.us/');
        $this->base_url_api = config('zoom.base_url', 'https://api.zoom.us/v2/');


        if (File::exists(base_path($this->credential_path))) {

            $this->credential_data = json_decode(File::get(base_path($this->credential_path)), true);

            $this->accessToken = $this->getAccessTokenFromFile();

            $this->client = new Client([
                'base_uri' => $this->base_url_api,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);
        }
    }

    public function generateOAuthUrl(): string
    {
        if (env('APP_ENV') == 'dev') {
            $this->redirect_uri = "https://esharty.local/zoom/redirect";
        }
        return "{$this->base_url}oauth/authorize?response_type=code&client_id={$this->client_id}&redirect_uri={$this->redirect_uri}";
    }

    /**
     * @param $code
     * @return array
     * @throws GuzzleException
     * @throws FileNotFoundException
     */
    public function getAccessTokenFromCode($code)
    {
        $client = new Client([
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->client_id . ':' . $this->client_secret),
                'Host' => 'zoom.us',
            ],
        ]);

        if (env('APP_ENV') == 'dev') {
            $this->redirect_uri = "https://esharty.local/zoom/redirect";
        }

        $response = $client->request('POST', "https://api.zoom.us/oauth/token", [
            'form_params' => [
                "grant_type" => "authorization_code",
                "code" => $code,
                "redirect_uri" => $this->redirect_uri
            ],
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);

        ZoomAccountsAccess::query()
            ->create($responseBody + ['expires_date' => Carbon::now()->addSeconds($responseBody['expires_in'])]);

        $token = json_encode($responseBody);

        File::put(base_path($this->credential_path), $token);

        if (!File::exists(base_path($this->credential_path))) {
            return ['status' => false, 'message' => 'Error while saving file'];
        }

        $savedToken = json_decode(File::get(base_path($this->credential_path)), true); //getting json from saved json file

        if (!empty(array_diff($savedToken, $responseBody))) { // checking reponse token and saved tokends are same
            return ['status' => false, 'message' => 'Error in saved token'];
        }

        return ['status' => true, 'message' => 'Token saved successfully'];
    }

    public function getAccessToken()
    {
        $client = new Client([
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->client_id . ':' . $this->client_secret),
                'Host' => 'zoom.us',
            ],
        ]);

        $response = $client->request('POST', "https://zoom.us/oauth/token", [
            'form_params' => [
                'grant_type' => 'account_credentials',
                'account_id' => $this->account_id,
            ],
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);
        return $responseBody['access_token'];
    }

    public function getAccessTokenFromFile()
    {
        return $this->credential_data['access_token'];
    }

    /**
     * @throws GuzzleException
     * @throws FileNotFoundException
     * @throws \Exception
     */
    public function refreshToken($refresh_token = null): array
    {
        if (!$refresh_token) {
            $accessTokens = ZoomAccountsAccess::query()
                ->latest()
                ->first();

            $refresh_token = $accessTokens->refresh_token;
        }

        $client = new Client([
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->client_id . ':' . $this->client_secret),
                'Host' => 'zoom.us',
            ],
        ]);

        $response = $client->request('POST', "https://api.zoom.us/oauth/token", [
            'form_params' => [
                "grant_type" => "refresh_token",
                "refresh_token" => $refresh_token ?? $this->credential_data['refresh_token']
            ],
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);

        ZoomAccountsAccess::query()
            ->where('refresh_token', $refresh_token ?? $this->credential_data['refresh_token'])
            ->update($responseBody + ['expires_date' => Carbon::now()->addSeconds($responseBody['expires_in'])]);

        $token = json_encode($responseBody);

        File::put(base_path($this->credential_path), $token);

        if (!File::exists(base_path($this->credential_path))) {
            return ['status' => false, 'message' => 'Error while saving file'];
        }

        $savedToken = json_decode(File::get(base_path($this->credential_path)), true); //getting json from saved json file

        if (!empty(array_diff($savedToken, $responseBody))) { // checking reponse token and saved tokends are same
            throw new \Exception("Token refreshed successfully But error in saved json token");
        }

        return ['status' => true, 'message' => 'Token saved successfully'];
    }

    // create meeting

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function createMeeting(array $data, $related = null, $trying = 0): array
    {
        $data = $this->mapBeforeCreateMeeting($data);

        try {
            $response = $this->client->request('POST', 'users/me/meetings', [
                'json' => $data,
            ]);

            $res = json_decode($response->getBody()->getContents(), false);

            $meeting = ZoomMeeting::query()
                ->create([
                    'uuid' => $res->uuid,
                    'meeting_id' => $res->id,
                    'host_id' => $res->host_id,
                    'host_email' => $res->host_email,
                    "topic" => $res->topic,
                    "type" => $res->type,
                    "status" => $res->status,
                    "start_time" => $res->start_time,
                    "duration" => $res->duration,
                    "timezone" => $res->timezone,
                    "agenda" => $res->agenda,
                    "meeting_created_at" => $res->created_at,
                    "password" => $res->password,
                    "h323_password" => $res->h323_password,
                    "pstn_password" => $res->pstn_password,
                    "encrypted_password" => $res->encrypted_password,
                    "settings" => $res->settings,
                    'pre_schedule' => $res->pre_schedule,
                    'start_url' => $res->start_url,
                    'join_url' => $res->join_url,
                    'is_active' => true,
                    'finished' => false,
                    'admin_created' => auth('admin')->user()->id,
                    'meeting_name' => $res->topic,
                    'label_color' => '',
                    'description' => $res->topic,
                    'start_date_time' => $res->start_time,
                    'end_date_time' => null,
                    'remind_type' => 'day',
                ]);

            if ($related) {
                $meeting->related_id = $related->id;
                $meeting->related_type = $related::class;
                $meeting->save();
            }

            return [
                'status' => true,
                'data' => $meeting,
            ];
        } catch (\Throwable $th) {

            if ($th->getCode() == 401 && $this->refreshToken() && $trying < 2) {
                return $this->createMeeting($data, $related, $trying++);
            }

            throw new \Exception($th->getMessage(), $th->getCode(), $th);
        }
    }

    public function addMeetingRegistrant($meeting_id = '', $json = [], $user_id = null)
    {
        try {
            $response = $this->client->request('POST', "meetings/{$meeting_id}/registrants", [
                'json' => $json
            ]);
            if ($response->getStatusCode() == 201) {
                $data = json_decode($response->getBody()->getContents(), true);

                $ZoomRegistrant = ZoomRegistrant::query()
                    ->create([
                        'meeting_id' => $meeting_id,
                        'registrant_id' => $data['registrant_id'],
                        'zoom_registrant_id' => $data['id'],
                        'topic' => $data['topic'],
                        'start_time' => $data['start_time'],
                        'join_url' => $data['join_url'],
                        'user_id' => $user_id,
                    ]);

                if ($ZoomRegistrant->meeting->related) {
                    $ZoomRegistrant->related_id = $ZoomRegistrant->meeting->related_id;
                    $ZoomRegistrant->related_type = $ZoomRegistrant->meeting->related_type;
                    $ZoomRegistrant->save();
                }

                return [
                    'status' => true,
                    'message' => 'Registration successfull',
                    'data' => $data,
                    'zoom_registrant' => $ZoomRegistrant
                ];
            }

            throw new \Exception("Not able to find error");
        } catch (\Exception $e) {
            if ($e->getCode() == 401 && $this->refreshToken()) {
                return $this->addMeetingRegistrant($meeting_id, $json);
            }
            if ($e->getCode() == 300) {
                return array('status' => false, 'message' => 'Meeting {meetingId} is not found or has expired.');
            }
            if ($e->getCode() == 400) {
                return array('status' => false, 'message' => 'Access error. Not have correct access. validation failed');
            }
            if ($e->getCode() == 404) {
                return array('status' => false, 'message' => 'Meeting not found or Meeting host does not exist: {userId}.');
            }
            if ($e->getCode() != 401) {
                return array('status' => false, 'message' => $e->getMessage());
            }
            return array('status' => false, 'message' => 'Not able to refresh token');
        } catch (GuzzleException $e) {
            dd($e->getCode(), $e->getTraceAsString());
        }
    }

    // update meeting
    public function updateMeeting(string $meetingId, array $data, $related = null, $trying = 0): array
    {
        try {
            $data = $this->mapBeforeCreateMeeting($data);

            $response = $this->client->request('PATCH', 'meetings/' . $meetingId, [
                'json' => $data,
            ]);

            if ($response->getStatusCode() == 204) {

                $res = json_decode($response->getBody()->getContents(), true);

                $res = $this->getMeeting($meetingId, true);

                $meeting = ZoomMeeting::query()
                    ->where('meeting_id', '=', $meetingId)
                    ->update([
                        'uuid' => $res->uuid,
                        'meeting_id' => $res->id,
                        'host_id' => $res->host_id,
                        'host_email' => $res->host_email,
                        "topic" => $res->topic,
                        "type" => $res->type,
                        "status" => $res->status,
                        "start_time" => $res->start_time,
                        "duration" => $res->duration,
                        "timezone" => $res->timezone,
                        "agenda" => $res->agenda,
                        "meeting_created_at" => $res->created_at,
                        "password" => $res->password,
                        "h323_password" => $res->h323_password,
                        "pstn_password" => $res->pstn_password,
                        "encrypted_password" => $res->encrypted_password,
                        "settings" => $res->settings,
                        'pre_schedule' => $res->pre_schedule,
                        'start_url' => $res->start_url,
                        'join_url' => $res->join_url,
                        'is_active' => true,
                        'finished' => false,
                        'admin_created' => auth('admin')->user()->id,
                        'meeting_name' => $res->topic,
                        'label_color' => '',
                        'description' => $res->topic,
                        'start_date_time' => $res->start_time,
                        'end_date_time' => null,
                        'remind_type' => 'day',
                    ]);

                if ($related) {
                    $meeting->related_id = $related->id;
                    $meeting->related_type = $related::class;
                    $meeting->save();
                }

                return [
                    'status' => true,
                    'data' => $meeting,
                ];
            }
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
            ];
        }
        return [
            'status' => false,
            'data' => null,
        ];
    }

    // get meeting
    public function getMeeting(string $meetingId, $returnObject = false)
    {
        try {
            $response = $this->client->request('GET', 'meetings/' . $meetingId);

            $responseBody = $response->getBody()->getContents();

            $data = json_decode($responseBody, false);

            if ($returnObject) {
                return json_decode($responseBody, false);
            }

            return [
                'status' => true,
                'data' => $data,
            ];
        } catch (\Throwable $th) {

            return [
                'status' => false,
                'message' => $th->getMessage(),
            ];
        }
    }

    // get all meetings
    public function getAllMeeting()
    {
        try {
            $response = $this->client->request('GET', 'users/me/meetings');
            $data = json_decode($response->getBody()->getContents(), true);
            return [
                'status' => true,
                'data' => $data,
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
            ];
        }
    }

    // get upcoming meetings
    public function getUpcomingMeeting()
    {
        try {
            $response = $this->client->request('GET', 'users/me/meetings?type=upcoming');

            $data = json_decode($response->getBody()->getContents(), true);
            return [
                'status' => true,
                'data' => $data,
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
            ];
        }
    }

    // get previous meetings
    public function getPreviousMeetings()
    {
        try {
            $meetings = $this->getAllMeeting();

            $previousMeetings = [];

            foreach ($meetings['meetings'] as $meeting) {
                $start_time = strtotime($meeting['start_time']);

                if ($start_time < time()) {
                    $previousMeetings[] = $meeting;
                }
            }

            return [
                'status' => true,
                'data' => $previousMeetings];

        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
            ];
        }
    }

    // get rescheduling meeting
    public function rescheduleMeeting(string $meetingId, array $data)
    {
        try {
            $response = $this->client->request('PATCH', 'meetings/' . $meetingId, [
                'json' => $data,
            ]);
            if ($response->getStatusCode() === 204) {
                return [
                    'status' => true,
                    'message' => 'Meeting Rescheduled Successfully',
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Something went wrong',
                ];
            }
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
            ];
        }
    }

    // end meeting
    public function endMeeting($meetingId)
    {
        try {
            $response = $this->client->request('PUT', 'meetings/' . $meetingId . '/status', [
                'json' => [
                    'action' => 'end',
                ],
            ]);
            if ($response->getStatusCode() === 204) {
                return [
                    'status' => true,
                    'message' => 'Meeting Ended Successfully',
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Something went wrong',
                ];
            }
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
            ];
        }
    }

    // delete meeting
    public function deleteMeeting(string $meetingId)
    {
        try {
            $response = $this->client->request('DELETE', 'meetings/' . $meetingId);
            if ($response->getStatusCode() === 204) {
                return [
                    'status' => true,
                    'message' => 'Meeting Deleted Successfully',
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Something went wrong',
                ];
            }
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
            ];
        }

    }

    // recover meeting
    public function recoverMeeting($meetingId)
    {
        try {
            $response = $this->client->request('PUT', 'meetings/' . $meetingId . '/status', [
                'json' => [
                    'action' => 'recover',
                ],
            ]);

            if ($response->getStatusCode() === 204) {
                return [
                    'status' => true,
                    'message' => 'Meeting Recovered Successfully',
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Something went wrong',
                ];
            }
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
            ];
        }
    }

    // get users list
    public function getUsers($data)
    {
        try {
            $response = $this->client->request('GET', 'users', [
                'query' => [
                    'page_size' => @$data['page_size'] ?? 300,
                    'status' => @$data['status'] ?? 'active',
                    'page_number' => @$data['page_number'] ?? 1,
                ],
            ]);
            $responseData = json_decode($response->getBody()->getContents(), true);
            $data = [];
            $data['current_page'] = $responseData['page_number'];
            $data['profile'] = $responseData['users'][0];
            $data['last_page'] = $responseData['page_count'];
            $data['per_page'] = $responseData['page_size'];
            $data['total'] = $responseData['total_records'];
            return [
                'status' => true,
                'data' => $data,
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
            ];
        }

    }

    public function addUser($data)
    {
        try {
            $response = $this->client->request('POST', 'users', [
                'json' => [
                    'action' => 'custCreate',
                    'user_info' => [
                        'email' => $data['email'],
                        'first_name' => $data['first_name'] ?? '',
                        'last_name' => $data['last_name'] ?? '',
                        'display_name' => $data['display_name'] ?? '',
                        'type' => 1,
                    ],
                ]
            ]);
            $responseData = json_decode($response->getBody()->getContents(), true);
            return [
                'status' => true,
                'data' => $responseData,
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
            ];
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function mapBeforeCreateMeeting(array $data): array
    {
        return array_merge([
            "agenda" => 'your agenda',
            "topic" => 'your topic',
            "type" => 2, // 1 => instant, 2 => scheduled, 3 => recurring with no fixed time, 8 => recurring with fixed time
            "duration" => 60, // in minutes
            "timezone" => 'Asia/Riyadh', // set your timezone
            "password" => \Str::random(10),
            "start_time" => '2023-11-7T12:02:00Z', // set your start time
            //            "template_id" => 'set your template id', // set your template id  Ex: "Dv4YdINdTk+Z5RToadh5ug==" from https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingtemplates
            "pre_schedule" => false,  // set true if you want to create a pre-scheduled meeting
            "schedule_for" => 'zoom@esharti.net', // set your schedule for
            "settings" => [
                'join_before_host' => true, // if you want to join before host set true otherwise set false
                'host_video' => true, // if you want to start video when host join set true otherwise set false
                'participant_video' => false, // if you want to start video when participants join set true otherwise set false
                'mute_upon_entry' => false, // if you want to mute participants when they join the meeting set true otherwise set false
                'waiting_room' => false, // if you want to use waiting room for participants set true otherwise set false
                'audio' => 'both', // values are 'both', 'telephony', 'voip'. default is both.
                'auto_recording' => 'cloud', // values are 'none', 'local', 'cloud'. default is none.
                'approval_type' => 2, // 0 => Automatically Approve, 1 => Manually Approve, 2 => No Registration Required

//                "additional_data_center_regions" => [
//                    "TY"
//                ],
                "allow_multiple_devices" => false,
//                "alternative_hosts" => "tariq.ayman94@gmail.com,",
                "alternative_hosts_email_notification" => true,
//                "approved_or_denied_countries_or_regions" => [
//                    "approved_list" => [
//                        "CX"
//                    ],
//                    "denied_list" => [
//                        "CA"
//                    ],
//                    "enable" => true,
//                    "method" => "approve"
//                ],
                "audio_conference_info" => "test",
//                "authentication_domains" => "esharti.net",
//                "authentication_exception" => [
//                    [
//                        "email" => "jchill@example.com",
//                        "name" => "Jill Chill"
//                    ]
//                ],
//                "authentication_option" => "signIn_D8cJuqWVQ623CI4Q8yQK0Q",

//                "breakout_room" => [
//                    "enable" => true,
//                    "rooms" => [
//                        [
//                            "name" => "room1",
//                            "participants" => [
//                                "jchill@example.com"
//                            ]
//                        ]
//                    ]
//                ],
                "calendar_type" => 2,
                "close_registration" => false,
//                "contact_email" => "jchill@example.com",
//                "contact_name" => "Jill Chill",
                "email_notification" => true,
                "encryption_type" => "enhanced_encryption",
                "focus_mode" => true,
//                "global_dial_in_countries" => [
//                    "US"
//                ],
//                "jbh_time" => 0,
//                "language_interpretation" => [
//                    "enable" => true,
//                    "interpreters" => [
//                        [
//                            "email" => "interpreter@example.com",
//                            "languages" => "US,FR"
//                        ]
//                    ]
//                ],
//                "sign_language_interpretation" => [
//                    "enable" => true,
//                    "interpreters" => [
//                        [
//                            "email" => "interpreter@example.com",
//                            "sign_language" => "American"
//                        ]
//                    ]
//                ],
                "meeting_authentication" => false,
//                "meeting_invitees" => [
////                    ["email" => "q5z@live.com"],
////                    ["email" => "tariq.ayman94@gmail.com"],
//                ],
                "private_meeting" => true,
                "registrants_confirmation_email" => true,
                "registrants_email_notification" => true,
                "registration_type" => 1,
                "show_share_button" => true,
                "use_pmi" => false,
                "watermark" => false,
                "host_save_video_order" => true,
                "alternative_host_update_polls" => true,
                "internal_meeting" => false,
                "continuous_meeting_chat" => [
                    "enable" => true,
                    "auto_add_invited_external_users" => true
                ],
                "participant_focused_meeting" => false,
                "push_change_to_calendar" => false,
//                "resources" => [
//                    [
//                        "resource_type" => "whiteboard",
//                        "resource_id" => "X4Hy02w3QUOdskKofgb9Jg",
//                        "permission_level" => "editor"
//                    ]
//                ]
            ],
            "default_password" => false,
//            "recurrence" => [
//                "end_date_time" => "2023-11-12T15:59:00Z",
//                "end_times" => 7,
//                "monthly_day" => 1,
//                "monthly_week" => 1,
//                "monthly_week_day" => 1,
//                "repeat_interval" => 1,
//                "type" => 1,
//                "weekly_days" => "1"
//            ],
//            "tracking_fields" => [
//                [
//                    "field" => "field1",
//                    "value" => "value1"
//                ]
//            ],
        ], $data);
    }
}
