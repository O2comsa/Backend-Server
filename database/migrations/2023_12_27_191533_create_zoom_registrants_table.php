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
        Schema::create('zoom_registrants', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_id');
            $table->string('registrant_id');
            $table->string('zoom_registrant_id');
            $table->string('topic');
            $table->string('start_time');
            $table->text('join_url');
            $table->unsignedBigInteger('user_id')->nullable();

            $table->nullableMorphs('related');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_registrants');
    }
};
