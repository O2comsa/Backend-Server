<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ZoomMeetingNote extends Model
{
    protected $table = 'zoom_meeting_notes';

    protected $guarded = ['id'];

    protected $with = ['user'];

    protected $dates = ['start_date_time', 'end_date_time'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function meeting()
    {
        return $this->belongsTo(ZoomMeeting::class, 'zoom_meeting_id');
    }
}
