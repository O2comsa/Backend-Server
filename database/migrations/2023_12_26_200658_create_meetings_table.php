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
        Schema::dropIfExists('zoom_meetings');

        Schema::create('zoom_meetings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('admin_created')->nullable()->references('id')->on('admins')->onDelete('cascade')->onUpdate('cascade');

            $table->foreignId('last_updated_by')->nullable()->references('id')->on('admins')->onDelete('SET NULL')->onUpdate('cascade');

            $table->nullableMorphs('related');

            $table->string('uuid');
            $table->string('meeting_id');
            $table->string('host_id');
            $table->string('host_email');

            $table->text("topic");
            $table->string("type");
            $table->enum('status', ['waiting', 'live', 'canceled', 'finished'])->default('waiting');
            $table->string("start_time");
            $table->string("duration");
            $table->string("timezone");
            $table->text("agenda");
            $table->string("meeting_created_at");

            $table->string("password")->nullable();
            $table->string("h323_password");
            $table->string("pstn_password");
            $table->string("encrypted_password");
            $table->json("settings");
            $table->boolean('pre_schedule');

            $table->text('start_url')->nullable();
            $table->text('join_url')->nullable();

            $table->string('meeting_name', 100);
            $table->string('label_color', 20);
            $table->mediumText('description')->nullable();
            $table->dateTime('start_date_time')->nullable();
            $table->dateTime('end_date_time')->nullable();
            $table->boolean('repeat')->default(0);
            $table->integer('repeat_every')->nullable();
            $table->integer('repeat_cycles')->nullable();
            $table->enum('repeat_type', ['day', 'week', 'month', 'year']);
            $table->boolean('send_reminder')->default(0);
            $table->integer('remind_time')->nullable();
            $table->enum('remind_type', ['day', 'hour', 'minute']);
            $table->boolean('host_video')->default(0);
            $table->boolean('participant_video')->default(0);
            $table->string('meeting_app')->default('in_app');

            $table->boolean('is_active')->default(false);
            $table->boolean('finished')->default(false);

            $table->foreignId('source_meeting_id')->nullable()->references('id')->on('zoom_meetings')->onDelete('cascade')->onUpdate('cascade');

            $table->bigInteger('occurrence_id')->nullable();
            $table->integer('occurrence_order')->nullable();

            $table->foreignId('category_id')->nullable()->references('id')->on('zoom_categories')->onDelete('cascade')->onUpdate('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_meetings');
    }
};
