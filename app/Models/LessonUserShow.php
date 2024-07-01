<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonUserShow extends Model
{
    //
    public $timestamps = false;

    public $incrementing = false;
    public $primaryKey = 'lesson_id';
    protected $table = 'lesson_student_show';

    protected $fillable = ['lesson_id','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Course::class,'lesson_id');
    }
}
