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
        Schema::table('paytabs', function (Blueprint $table) {
            if (!Schema::hasColumn('paytabs', 'live_event_id')) {
                $table->unsignedBigInteger('live_event_id')->nullable()->after('course_id');
            }
        
            // Add foreign key if it does not exist
            if (!Schema::hasTable('paytabs_live_event_id_foreign')) {
                $table->foreign('live_event_id')->references('id')->on('live_events')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paytabs', function (Blueprint $table) {
            //
        });
    }
};
