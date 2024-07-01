<?php

namespace App\Models;

use App\Helpers\NotificationsType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

class Notifications extends DatabaseNotification
{
    protected $appends = [
        'type_api',
        'created_at_text'
    ];

    public function getTypeApiAttribute()
    {
        if (request()->route()->getPrefix() == 'api') {
            return NotificationsType::getString($this->type);
        }
        return $this->type;
    }

    public function getCreatedAtTextAttribute()
    {
        if (request()->route()->getPrefix() == 'api') {

            return Carbon::parse($this->created_at)->format('h:i A d-m-Y');
        }
        return $this->created_at;
    }
}
