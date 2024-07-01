<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Notification;
use App\Events\Zoom\MeetingReminderEvent;
use App\Notifications\Zoom\MeetingReminder;

class MeetingReminderListener
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
     * @param MeetingReminderEvent $event
     * @return void
     */
    public function handle(MeetingReminderEvent $event)
    {
        $users = $event->event->attendees;
        Notification::send($users, new MeetingReminder($event->event));
    }
}
