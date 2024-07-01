<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContactUs extends Model
{
    use SoftDeletes;

    protected $table = 'contact_us';

    protected $fillable = [
        'name',
        'user_id',
        'message',
        'email',
        'files'
    ];

    protected $casts = [
        'files' => 'json'
    ];

    protected $hidden = ['deleted_at'];

    protected $appends = [
        'files_assets'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFilesAssetsAttribute()
    {
        $files = [];

        foreach ($this->files ?? [] as $file) {
            $file = str_replace('/home/esharti/esharti_staging/public', '', $file);

            $files[] = asset($file);
        }

        return $files;
    }
}
