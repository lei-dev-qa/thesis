<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_batches', function (Blueprint $table) {
            $table->id();
            $table->string('nc_program'); 
            $table->integer('batch_number');
            $table->integer('max_students')->default(25);
            $table->enum('status', ['enrolling', 'full', 'scheduled', 'ongoing', 'completed', 'cancelled'])->default('enrolling');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['nc_program', 'batch_number']);
            $table->index(['nc_program', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_batches');
    }
};
