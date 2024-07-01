<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    //
    protected $table = 'otp';

    protected $fillable = ['mobile', 'otp', 'verified'];
}
