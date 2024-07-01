<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Events\Zoom\MeetingHostEvent;
use App\Notifications\Zoom\InviteHost;

class MeetingHostListener
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
     * @param MeetingHostEvent $meeting
     * @return void
     */
    public function handle(MeetingHostEvent $meeting)
    {

        Notification::send($meeting->notifyUser, new InviteHost($meeting->meeting));

    }
}
