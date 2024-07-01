<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ZoomMeeting extends Model
{
    protected $table = 'zoom_meetings';

    protected $fillable = [
        'uuid',
        'meeting_id',
        'host_id',
        'host_email',
        "topic",
        "type",
        "status",
        "start_time",
        "duration",
        "timezone",
        "agenda",
        "meeting_created_at",
        "password",
        "h323_password",
        "pstn_password",
        "encrypted_password",
        "settings",
        'pre_schedule',
        'start_url',
        'join_url',
        'admin_id',
        'is_active',
        'finished',
        'admin_created',
        'last_updated_by',
        'related',
        'status',
        'meeting_name',
        'label_color',
        'description',
        'start_date_time',
        'end_date_time',
        'repeat',
        'repeat_every',
        'repeat_cycles',
        'repeat_type',
        'send_reminder',
        'remind_time',
        'remind_type',
        'host_video',
        'participant_video',
        'meeting_app',
        'source_meeting_id',
        'occurrence_id',
        'occurrence_order',
        'category_id',
    ];

    protected $casts = [
        'uuid' => 'string',
        'meeting_id' => 'string',
        'host_id' => 'string',
        'host_email' => 'string',
        "topic" => 'string',
        "type" => 'integer',
        "status" => 'string',
        "start_time" => 'datetime',
        "duration" => 'string',
        "timezone" => 'string',
        "agenda" => 'string',
        "meeting_created_at" => 'datetime',
        "password" => 'string',
        "h323_password" => 'string',
        "pstn_password" => 'string',
        "encrypted_password" => 'string',
        "settings" => 'json',
        'pre_schedule' => 'boolean',
        'start_url' => 'string',
        'join_url' => 'string',
        'admin_id' => 'integer',
        'is_active' => 'boolean',
        'finished' => 'boolean',

        'admin_created' => 'integer',
        'last_updated_by' => 'integer',
        'meeting_name' => 'string',
        'label_color' => 'string',
        'description' => 'string',
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'repeat' => 'boolean',
        'repeat_every' => 'integer',
        'repeat_cycles' => 'integer',
        'repeat_type' => 'string',
        'send_reminder' => 'boolean',
        'remind_time' => 'integer',
        'remind_type' => 'integer',
        'host_video' => 'boolean',
        'participant_video' => 'boolean',
        'meeting_app' => 'string',
        'source_meeting_id' => 'integer',
        'occurrence_id' => 'integer',
        'occurrence_order' => 'integer',
        'category_id' => 'integer',
    ];

    protected $dates = ['start_date_time', 'end_date_time'];

    public function attendees()
    {
        return $this->hasMany(ZoomRegistrant::class, 'meeting_id', 'meeting_id');
    }

    public function host()
    {
        return $this->belongsTo(Admin::class, 'admin_created');
    }

    public function category()
    {
        return $this->belongsTo(ZoomCategory::class, 'category_id');
    }

    public function notes()
    {
        return $this->hasMany(ZoomMeetingNote::class, 'zoom_meeting_id')->orderBy('id', 'desc');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function related()
    {
        return $this->morphTo('related');
    }
}
