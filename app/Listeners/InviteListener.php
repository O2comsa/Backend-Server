<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Events\Zoom\MeetingInviteEvent;
use App\Notifications\Zoom\Invite;

class InviteListener
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
     * @param MeetingInviteEvent $meeting
     * @return void
     */
    public function handle(MeetingInviteEvent $meeting)
    {
        Notification::send($meeting->notifyUser, new Invite($meeting->meeting));

    }
}
