<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomAccountsAccess extends Model
{
    use HasFactory;

    protected $table = 'zoom_accounts_accesses';

    protected $fillable = [
        'access_token',
        'token_type',
        'refresh_token',
        'expires_in',
        'expires_date',
        'scope',

        'email',
        'password',
    ];

    protected $casts = [
        'expires_date' => 'datetime'
    ];

}
