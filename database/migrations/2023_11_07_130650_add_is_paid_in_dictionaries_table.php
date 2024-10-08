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
        Schema::table('dictionaries', function (Blueprint $table) {
            $table->string('status')->after('file_pdf')->default('active');
            $table->boolean('is_paid')->after('status')->default(false);
            $table->decimal('price')->after('is_paid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dictionaries', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('is_paid');
            $table->dropColumn('price');
        });
    }
};
