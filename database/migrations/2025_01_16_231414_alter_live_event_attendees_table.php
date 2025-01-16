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
        Schema::table('live_event_attendees', function (Blueprint $table) {
            $table->boolean('is_confirmed')->default(false); // حالة التأكيد
            $table->timestamp('reserved_at')->nullable(); // تاريخ الحجز
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_event_attendees', function (Blueprint $table) {
            //
        });
    }
};
