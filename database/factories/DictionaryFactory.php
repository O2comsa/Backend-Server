<?php

use App\User;
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

$factory->define(\App\Models\Dictionary::class, function (Faker $faker) {
    return [
        'title' => $faker->name,
        'description' => $faker->text,
        'file_pdf' => '',
        'image' => $faker->image(public_path('/upload/images/dictionaries/'),1920,720,'',false),
    ];
});
