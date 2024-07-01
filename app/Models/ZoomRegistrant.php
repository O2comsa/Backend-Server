<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomRegistrant extends Model
{
    use HasFactory;

    protected $table = 'zoom_registrants';

    protected $fillable = [
        'meeting_id',
        'registrant_id',
        'zoom_registrant_id',
        'topic',
        'start_time',
        'join_url',
        'user_id'
    ];

    public function meeting()
    {
        return $this->belongsTo(ZoomMeeting::class,'meeting_id','meeting_id');
    }

    public function related()
    {
        return $this->morphOne('related', 'related');
    }
}
