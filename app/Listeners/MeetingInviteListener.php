<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Notifications\Zoom\MeetingInvite;

class MeetingInviteListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param $meeting
     * @return void
     */
    public function handle($meeting)
    {
        Notification::send($meeting->notifyUser, new MeetingInvite($meeting->meeting));
    }
}
