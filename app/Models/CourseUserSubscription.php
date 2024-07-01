<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseUserSubscription extends Model
{
    //
    public $timestamps = false;

    public $incrementing = false;
    public $primaryKey = 'course_id';
    protected $table = 'course_student_subscription';

    protected $fillable = ['course_id','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class,'course_id');
    }
}
