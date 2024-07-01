<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\ZoomCategory;
use App\Models\ZoomMeeting;
use App\Models\ZoomMeetingNote;
use App\Events\Zoom\MeetingHostEvent;
use App\Events\Zoom\MeetingHostUpdateEvent;
use App\Events\Zoom\MeetingInviteEvent;
use App\Events\Zoom\MeetingReminderEvent;
use App\Events\Zoom\MeetingUpdateEvent;
use App\Listeners\HostUpdateListener;
use App\Listeners\InviteListener;
use App\Listeners\MeetingHostListener;
use App\Listeners\MeetingReminderListener;
use App\Listeners\MeetingUpdateListener;
use App\Observers\CategoryObserver;
use App\Observers\ZoomMeetingObserver;
use App\Observers\ZoomNoteObserver;

class ZoomEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MeetingReminderEvent::class => [MeetingReminderListener::class],
        MeetingInviteEvent::class => [InviteListener::class],
        MeetingHostEvent::class => [MeetingHostListener::class],
        MeetingUpdateEvent::class => [MeetingUpdateListener::class],
        MeetingHostUpdateEvent::class => [HostUpdateListener::class],
    ];

    protected $observers = [
        ZoomMeeting::class => [ZoomMeetingObserver::class],
        ZoomCategory::class => [CategoryObserver::class],
        ZoomMeetingNote::class => [ZoomNoteObserver::class],
    ];
}
