<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Paytabs extends Model
{
    protected $table = 'paytabs';

    protected $fillable = [
        'payment_reference',
        'user_id',
        'related_id',
        'transaction_id',
        'create_response',
        'verify_payment_response',
        'paid',
        'related_type'
    ];

    protected $casts = [
        'create_response' => 'json',
        'verify_payment_response' => 'json',
        'updated_at' => 'datetime',
    ];

    // public function getUpdatedAtAttribute()
    // {
    //     return Carbon::parse($this->updated_at)->format("Y-m-d H:i:s");
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function related()
    {
        return $this->morphTo('related', 'related_type', 'related_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relationship with LiveEvent (if the paytab is related to a live event)
    public function liveEvent()
    {
        return $this->belongsTo(LiveEvent::class, 'live_event_id');
    }
}
