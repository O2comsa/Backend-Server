<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LiveEvent extends Model
{
    use HasFactory;

    protected $table = 'live_events';

    protected $fillable = [
        'is_paid',
        'price',
        'event_at',
        'duration_event',
        'event_presenter',
        'name',
        'description',
        'agenda',
        'status',
        'image',
        'number_of_seats',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'price' => 'float',
        'agenda' => 'json'
    ];

    protected $appends = [
        'meeting_info',
        'purchased'
    ];

    public const ACTIVE_STATUS = 'active';

    public function usersAttendee()
    {
        return $this->belongsToMany(User::class, 'live_event_attendees');
    }

     public function scopeWithAvailableSeats(Builder $query): void
    {
        $query->withCount('usersAttendee')
              ->havingRaw('users_attendee_count < number_of_seats')
              ->select('live_events.*')
              ->groupBy('live_events.id', 'live_events.number_of_seats', 'live_events.is_paid', 'live_events.price', 'live_events.event_at', 'live_events.duration_event', 'live_events.event_presenter', 'live_events.name', 'live_events.description', 'live_events.agenda', 'live_events.status', 'live_events.image');
    }
    /**
     * Scope a query to only include active users.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', self::ACTIVE_STATUS);
    }

    public function getImageAttribute($image)
    {
        if (!empty($image)) {
            return asset('/upload/images/live-events/' . $image);
        }
        return $image;
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

    public function meeting()
    {
        return $this->morphOne(ZoomMeeting::class, 'meeting', 'related_type', 'related_id')->latest();
    }

    public function getPurchasedAttribute()
    {
        return $this->usersAttendee()->where('user_id', request()->get('user_id'))->exists();
    }
}
