<?php

namespace App\Models;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use SoftCascadeTrait;

    protected $table = 'users';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'email',
        'password',
        'status',
        'device_token',
        'national_id',
        'profile_picture',
        'mobile'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'deleted_at'
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = \Hash::make($password);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function lesson()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_show', 'lesson_id', 'user_id')->withTimestamps();
    }

    public function subscription()
    {
        return $this->belongsToMany(Course::class, 'course_subscription', 'user_id', 'course_id')->withTimestamps();
    }

    public function completedLesson()
    {
        return $this->belongsToMany(Lesson::class, 'users_complete_lessons', 'lesson_id', 'user_id')->withTimestamps();
    }

    public function bookmarksLesson()
    {
        return $this->belongsToMany(Lesson::class, 'users_bookmark_lessons', 'lesson_id', 'user_id')->withTimestamps();
    }

    public function bookmarksCourses()
    {
        return $this->belongsToMany(Course::class, 'users_bookmark_courses', 'course_id', 'user_id')->withTimestamps();
    }

    public function bookmarksArticle()
    {
        return $this->belongsToMany(Article::class, 'users_bookmark_articles', 'article_id', 'user_id')->withTimestamps();
    }

    public function bookmarksDictionary()
    {
        return $this->belongsToMany(Dictionary::class, 'users_bookmark_dictionaries', 'dictionary_id', 'user_id')->withTimestamps();
    }

    public function planPurchases()
    {
        return $this->belongsToMany(Plan::class, 'plan_purchases')->withTimestamps();
    }

    public function dictionaryPurchases()
    {
        return $this->belongsToMany(Dictionary::class, 'dictionary_purchases')->withTimestamps();
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'user_id');
    }

    public function getProfilePictureAttribute($profile_picture)
    {
        if (!empty($profile_picture)) {
            return asset('/upload/images/users/' . $profile_picture);
        }
        return $profile_picture;
    }

    /**
     * Specifies the user's FCM token
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return [$this->device_token] ?? [];
    }
}
