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
        Schema::table('paytabs', function (Blueprint $table) {
            $table->renameColumn('course_id', 'related_id');
            $table->string('related_type')->after('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paytabs', function (Blueprint $table) {
            $table->renameColumn('related_id', 'course_id');
            $table->dropColumn('related_type');
        });
    }
};
