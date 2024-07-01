<?php

use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\Models\Lesson::class, function (Faker $faker) {
    return [
        'title' => $faker->name,
        'video' => '',
        'lesson_time' => '11:11',
        'status' => \App\Helpers\CourseStatus::ACTIVE,
        'image' => $faker->image(public_path('/upload/images/lessons/'),1920,720,'',false),
        'course_id' => \App\Models\Course::where('id', '!=', null)->inRandomOrder()->first()->id
    ];
});
