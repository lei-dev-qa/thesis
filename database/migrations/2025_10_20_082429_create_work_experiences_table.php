<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('position');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->decimal('monthly_salary', 12, 2)->nullable();
            $table->string('appointment_status')->nullable(); 
            $table->unsignedInteger('years_experience')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('work_experiences');
    }
};
