<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('training_batch_id')->constrained('training_batches')->cascadeOnDelete();
            $table->enum('result', ['completed', 'failed', 'ongoing'])->default('ongoing');
            $table->decimal('attendance_percentage', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['application_id', 'training_batch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_results');
    }
};
