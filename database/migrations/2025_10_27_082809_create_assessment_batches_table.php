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
        Schema::create('assessment_batches', function (Blueprint $table) {
          $table->id();
            $table->string('nc_program');
            $table->string('batch_name')->unique();
            $table->date('assessment_date');
            $table->date('intensive_review_day1')->nullable();
            $table->time('intensive_review_day1_start')->nullable();
            $table->time('intensive_review_day1_end')->nullable();
            $table->date('intensive_review_day2')->nullable();
            $table->time('intensive_review_day2_start')->nullable();
            $table->time('intensive_review_day2_end')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue');
            $table->string('assessor_name')->nullable();
            $table->integer('max_applicants')->default(10);
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamp('schedule_notifications_sent_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index(['nc_program', 'status']);
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_batches');
    }
};
