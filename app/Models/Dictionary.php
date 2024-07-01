<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dictionary extends Model
{
    //
    use SoftDeletes;

    protected $table = 'dictionaries';

    protected $fillable = [
        'title',
        'description',
        'file_pdf',
        'image',
        'is_paid',
        'price',
        'status'
    ];

    protected $appends = [
        'bookmarked',
        'purchased'
    ];

    public function getFilePdfAttribute($pdf)
    {
        if ($this->price || $this->is_paid) {
            if (!$this->users()->where('user_id', request()->get('user_id'))->exists()) {
                return null;
            }
        }

        if (!empty($pdf)) {
            return asset('/upload/dictionaries/' . $pdf);
        }
        return $pdf;
    }

    public function getImageAttribute($image)
    {
        if (!empty($image)) {
            return asset('/upload/images/dictionaries/' . $image);
        }
        return $image;
    }

    public function bookmarks()
    {
        return $this->belongsToMany(User::class, 'users_bookmark_dictionaries')->withTimestamps();
    }

    public function getBookmarkedAttribute()
    {
        return $this->bookmarks()->where('user_id', request()->get('user_id'))->exists();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'dictionary_purchases')->withTimestamps();
    }

    public function getPurchasedAttribute()
    {
        return $this->users()->where('user_id', request()->get('user_id'))->exists();
    }
}
