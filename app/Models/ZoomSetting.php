<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ZoomSetting extends Model
{
    protected $table = 'zoom_setting';

    protected $fillable = ['api_key', 'secret_key', 'meeting_app', 'secret_token', 'account_id', 'meeting_client_id', 'meeting_client_secret'];
}
