<?php

namespace App\Events\Zoom;

use Illuminate\Queue\SerializesModels;
use App\Models\ZoomMeeting;

class MeetingReminderEvent
{
    use SerializesModels;

    public $event;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ZoomMeeting $event)
    {
        $this->event = $event;
    }
}
