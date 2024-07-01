<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Events\Zoom\MeetingHostUpdateEvent;
use App\Notifications\Zoom\UpdateHost;

class HostUpdateListener
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
     * @param MeetingHostUpdateEvent $meeting
     * @return void
     */
    public function handle(MeetingHostUpdateEvent $meeting)
    {

        Notification::send($meeting->notifyUser, new UpdateHost($meeting->meeting));

    }
}
