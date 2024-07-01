<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    //
    use SoftDeletes;

    protected $table = 'lessons';

    protected $fillable = ['title', 'video', 'lesson_time', 'status', 'course_id', 'image'];

    protected $appends = [
        'viewed',
        'completed',
        'bookmarked'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function getVideoAttribute($image)
    {
        if (!empty($image)) {
            return asset('/upload/lessons/' . $image);
        }
        return $image;
    }

    public function getImageAttribute($image)
    {
        if (!empty($image)) {
            return asset('/upload/images/lessons/' . $image);
        }
        return $image;
    }

    public function userShow()
    {
        return $this->belongsToMany(User::class, 'lesson_show')->withTimestamps();
    }

    public function userCompleted()
    {
        return $this->belongsToMany(User::class, 'users_complete_lessons')->withTimestamps();
    }

    public function bookmarks()
    {
        return $this->belongsToMany(User::class, 'users_bookmark_lessons')->withTimestamps();
    }

    public function getBookmarkedAttribute()
    {
        return $this->bookmarks()->where('user_id', request()->get('user_id'))->exists();
    }

    public function getViewedAttribute()
    {
        return $this->userShow()->where('lesson_id', $this->attributes['id'])->where('user_id', request()->get('user_id'))->exists();
    }

    public function getCompletedAttribute()
    {
        return $this->userCompleted()
            ->where('lesson_id', $this->attributes['id'])
            ->where('user_id', request()->get('user_id'))
            ->exists();
    }
}
