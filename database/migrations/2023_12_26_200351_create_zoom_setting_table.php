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
        Schema::create('zoom_setting', function (Blueprint $table) {
            $table->id();
            $table->string('api_key', 50)->nullable();
            $table->string('secret_key', 50)->nullable();
            $table->string('meeting_app')->default('in_app');
            $table->string('password')->nullable();

            $table->string('secret_token')->nullable();

            $table->string('account_id')->nullable();
            $table->string('meeting_client_id')->nullable();
            $table->string('meeting_client_secret')->nullable();

            $table->boolean('notify_update')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_setting');
    }
};
