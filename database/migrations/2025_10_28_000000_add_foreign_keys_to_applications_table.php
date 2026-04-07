<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add foreign key constraints to applications table.
     * This must run AFTER training_batches, training_schedules, and assessment_batches tables are created.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->foreign('training_batch_id')
                  ->references('id')
                  ->on('training_batches')
                  ->nullOnDelete();
                  
            $table->foreign('training_schedule_id')
                  ->references('id')
                  ->on('training_schedules')
                  ->nullOnDelete();
                  
            $table->foreign('assessment_batch_id')
                  ->references('id')
                  ->on('assessment_batches')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['training_batch_id']);
            $table->dropForeign(['training_schedule_id']);
            $table->dropForeign(['assessment_batch_id']);
        });
    }
};
