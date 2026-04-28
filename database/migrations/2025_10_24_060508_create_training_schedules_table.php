<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('nc_program'); 
            $table->foreignId('training_batch_id')->nullable()->constrained('training_batches')->nullOnDelete();
            $table->string('schedule_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('days'); 
            $table->integer('max_students')->default(25);
            $table->string('venue');
            $table->string('instructor');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('schedule_notifications_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_schedules');
    }
};
