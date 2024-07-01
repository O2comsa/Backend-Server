<?php

namespace App\Events\Zoom;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ZoomMeeting;

class MeetingHostEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meeting;

    public $notifyUser;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ZoomMeeting $meeting, $notifyUser)
    {

        $this->meeting = $meeting;
        $this->notifyUser = $notifyUser;
    }
}
