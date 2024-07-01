<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveSupportRequest extends Model
{
    use HasFactory;

    protected $table = 'live_support_requests';

    protected $fillable = [
        'user_id',
        'admin_id',
        'plan_id',
        'status',
        'duration'
    ];

    protected $appends = [
        'meeting_info'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function meeting()
    {
        return $this->morphOne(ZoomMeeting::class, 'meeting', 'related_type', 'related_id');
    }

    public function getMeetingInfoAttribute()
    {
        if ($this->price || $this->is_paid) {
            if (!$this->usersAttendee()->where('user_id', request()->get('user_id'))->exists()) {
                return null;
            }
        }

        return $this->meeting;
    }
}
