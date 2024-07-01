<?php

use App\Helpers\ApiHelper;
use App\Helpers\AppStatus;
use Illuminate\Database\Seeder;

class DummyTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        if (env('APP_ENV') == 'dev') {

            File::makeDirectory(public_path("/upload/images/lessons/"),777,true,true);
            File::makeDirectory(public_path("/upload/images/courses/"),777,true,true);
            File::makeDirectory(public_path("/upload/images/articles/"),777,true,true);

            factory(App\Models\Article::class, 30)->create();
            factory(App\Models\Dictionary::class, 30)->create();
            factory(App\Models\Course::class, 100)->create();
            factory(App\Models\Lesson::class, 200)->create();
        }
    }
}
