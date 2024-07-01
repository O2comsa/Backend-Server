<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes;

    protected $table = 'banner';
    protected $fillable = ['image','store_id'];
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    public function getImageAttribute($image)
    {
        return request()->getSchemeAndHttpHost() . '/upload/images/banner/' . $image;
    }
}
