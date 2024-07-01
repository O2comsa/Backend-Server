<?php

namespace App\Observers;

use App\Models\ZoomMeeting;

class ZoomMeetingObserver
{
    public function saving(ZoomMeeting $event)
    {
        if (auth('admin')->check()) {
            $event->last_updated_by = auth()->user()->id;
        }
    }

    public function creating(ZoomMeeting $event)
    {
        if (auth('admin')->check()) {
            $event->admin_created = auth()->user()->id;
        }
    }
}
