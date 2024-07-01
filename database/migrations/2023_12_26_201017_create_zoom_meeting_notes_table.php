<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zoom_meeting_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zoom_meeting_id');
            $table->foreign('zoom_meeting_id')->references('id')->on('zoom_meetings')->onDelete('cascade')->onUpdate('cascade');

            $table->text('note');

            $table->foreignId('added_by')->nullable()->index('zoom_meeting_added_by_foreign')->references('id')->on('admins')->onUpdate('CASCADE')->onDelete('SET NULL');

            $table->foreignId('last_updated_by')->nullable()->index('zoom_meeting_last_updated_by_foreign')->references('id')->on('admins')->onUpdate('CASCADE')->onDelete('SET NULL');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_meeting_notes');
    }
};
