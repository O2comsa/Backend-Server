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
        'reserved_seats'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'price' => 'float',
        'agenda' => 'json'
    ];

    protected $appends = [
        'meeting_info',
        'purchased',
        'is_seat_available',
    ];

    public const ACTIVE_STATUS = 'active';

    public function usersAttendee()
    {
        return $this->belongsToMany(User::class, 'live_event_attendees');
    }

    public function scopeActiveWithAvailableSeats(Builder $query): void
    {
        $query->select('live_events.*')
              ->leftJoin('live_event_attendees', 'live_event_attendees.live_event_id', '=', 'live_events.id')
              ->where('live_events.status', self::ACTIVE_STATUS)
              ->groupBy('live_events.id', 'live_events.is_paid', 'live_events.price', 'live_events.event_at', 'live_events.duration_event', 'live_events.event_presenter', 'live_events.name', 'live_events.description', 'live_events.agenda', 'live_events.status', 'live_events.image', 'live_events.number_of_seats', 'live_events.deleted_at','live_events.created_at','live_events.updated_at')
              ->havingRaw('COUNT(live_event_attendees.user_id) < live_events.number_of_seats');
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
    public function getIsSeatAvailableAttribute()
    {
        return $this->reserved_seats < $this->number_of_seats;
    }

    public function meeting()
    {
        return $this->morphOne(ZoomMeeting::class, 'meeting', 'related_type', 'related_id')->latest();
    }

    public function getPurchasedAttribute()
    {
        return $this->usersAttendee()->where('user_id', request()->get('user_id'))->where('is_confirmed', 1)->exists();
    }

    public function paytabs()
    {
        return $this->hasMany(Paytabs::class, 'live_event_id');
    }
}
