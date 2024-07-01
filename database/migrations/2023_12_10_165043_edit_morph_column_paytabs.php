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
        $paytabs = \App\Models\Paytabs::get();

        foreach ($paytabs as $paytab) {
            $paytab->update([
                'related_type' => \App\Models\Course::class
            ]);
        }

        try {
            Schema::table('paytabs', function (Blueprint $table) {
                $table->dropSpatialIndex('paytabs_course_id_foreign');
            });
        } catch (Exception $exception) {
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
