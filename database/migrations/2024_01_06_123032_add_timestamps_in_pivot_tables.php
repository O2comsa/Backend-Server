<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users_bookmark_articles', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('course_subscription', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('users_bookmark_courses', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('users_bookmark_dictionaries', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('dictionary_purchases', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('lesson_show', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('users_complete_lessons', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('users_bookmark_lessons', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('plan_purchases', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
