<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    //
    use SoftDeletes, SoftCascadeTrait;

    protected $table = 'courses';

    protected $fillable = ['title', 'description', 'image', 'status', 'price', 'free'];

    protected $casts = [
        'free' => 'boolean'
    ];

    protected $softCascade = ['lessons', 'subscription'];

    protected $appends = ['subscribed', 'lessons_count', 'eligible', 'completed', 'bookmarked'];

    public function getDescriptionAttribute()
    {
        if (request()->route()->getPrefix() == 'api' && (request()->routeIs('courses.index'))) {
            return Str::limit($this->attributes['description'], 30, ' ...');
        }
        return $this->attributes['description'];
    }

    public function user()
    {
        return $this->belongsToMany(User::class);
    }

    public function getImageAttribute($image)
    {
        if (!empty($image)) {
            return asset('/upload/images/courses/' . $image);
        }
        return $image;
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'course_id')->latest();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'related_courses', 'course_id', 'related_course_id');
    }

    public function subscription()
    {
        return $this->belongsToMany(User::class, 'course_subscription', 'course_id', 'user_id')->withTimestamps();
    }

    public function bookmarks()
    {
        return $this->belongsToMany(User::class, 'users_bookmark_courses', 'course_id', 'user_id')->withTimestamps();
    }

    public function getSubscribedAttribute()
    {
        return $this->subscription()->where('user_id', request()->get('user_id'))->exists();
    }

    public function getBookmarkedAttribute()
    {
        return $this->bookmarks()->where('user_id', request()->get('user_id'))->exists();
    }

    public function getLessonsCountAttribute()
    {
        return $this->lessons()->count();
    }

    public function getEligibleAttribute()
    {
        if ($this->courses()->count() == 0) {
            return true;
        }

        $flag = false;
        foreach ($this->courses()->get() as $lesson) {
            if ($lesson->completed) {
                $flag = true;
            }
        }
        return $flag;
    }

    public function getCompletedAttribute()
    {
        if ($this->lessons()->count() == 0) {
            return false;
        }

        $flag = true;
        foreach ($this->lessons()->get() as $lesson) {
            if (!$lesson->completed) {
                $flag = false;
            }
        }
        return $flag;
    }
}
