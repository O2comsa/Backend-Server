<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    //
    use SoftDeletes;

    protected $table = 'articles';

    protected $fillable = ['title', 'description', 'image'];

    protected $appends = [
        'bookmarked'
    ];

    public function getImageAttribute($image)
    {
        if (!empty($image)) {
            return asset('/upload/images/articles/' . $image);
        }
        return $image;
    }

    public function getDescriptionAttribute()
    {
        if (request()->route()->getPrefix() == 'api' && (request()->routeIs('article.index') || request()->routeIs('latest-article'))) {
            return Str::limit($this->attributes['description'], 30, ' ...');
        }
        return $this->attributes['description'];
    }

    public function bookmarks()
    {
        return $this->belongsToMany(User::class, 'users_bookmark_articles')->withTimestamps();
    }

    public function getBookmarkedAttribute()
    {
        return $this->bookmarks()->where('user_id', request()->get('user_id'))->exists();
    }
}
