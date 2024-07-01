<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Events\Zoom\MeetingUpdateEvent;
use App\Notifications\Zoom\UpdateHost;

class MeetingUpdateListener
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
     * @param MeetingUpdateEvent $meeting
     * @return void
     */
    public function handle(MeetingUpdateEvent $meeting)
    {
        Notification::send($meeting->notifyUser, new UpdateHost($meeting->meeting));
    }
}
