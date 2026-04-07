<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Consolidated applications table with all fields organized by category.
     * This replaces 13 separate migration files for better maintainability.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            // ==================== BASIC INFORMATION ====================
            $table->id();
            $table->string('reference_number')->nullable()->index();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // ==================== APPLICATION TYPE & STATUS ====================
            $table->string('title_of_assessment_applied_for');
            $table->enum('application_type', ['TWSP', 'Assessment Only'])
                  ->nullable()
                  ->comment('Type of application: TWSP (training) or Assessment Only');
            
            $table->boolean('is_reassessment')->default(false);
            $table->integer('reassessment_attempt')->default(0);
            
            $table->string('status')->default('pending')->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_remarks')->nullable();

            // ==================== CORRECTION/RESUBMISSION FIELDS ====================
            $table->boolean('correction_requested')->default(false);
            $table->text('correction_message')->nullable();
            $table->timestamp('correction_requested_at')->nullable();
            $table->boolean('was_corrected')->default(false);
            $table->timestamp('resubmitted_at')->nullable();

            // ==================== PERSONAL INFORMATION ====================
            $table->string('photo')->nullable();
            $table->string('surname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('middleinitial', 5)->nullable();
            $table->string('name_extension')->nullable(); // Jr., Sr., III, etc.
            
            $table->enum('sex', ['male', 'female', 'prefer_not_to_say'])->nullable();
            $table->string('civil_status')->nullable();
            $table->string('mobile', 32)->nullable();
            $table->string('email')->nullable();
            
            $table->date('birthdate')->nullable();
            $table->string('birthplace')->nullable();
            $table->string('birthplace_city')->nullable();
            $table->string('birthplace_province')->nullable();
            $table->string('birthplace_region')->nullable();
            $table->string('birthplace_region_code')->nullable();
            $table->string('birthplace_province_code')->nullable();
            $table->string('birthplace_city_code')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('nationality')->nullable();

            // ==================== ADDRESS INFORMATION (PSGC CODES) ====================
            $table->string('region_code');
            $table->string('region_name');
            $table->string('province_code');
            $table->string('province_name');
            $table->string('city_code');
            $table->string('city_name');
            $table->string('barangay_code');
            $table->string('barangay_name');
            $table->string('district')->nullable();
            $table->string('street_address')->nullable();
            $table->string('zip_code', 10)->nullable();

            // ==================== PARENT/GUARDIAN INFORMATION ====================
            $table->string('mothers_name')->nullable();
            $table->string('fathers_name')->nullable();
            
            $table->string('parent_guardian_name')->nullable();
            $table->string('parent_guardian_street')->nullable();
            $table->string('parent_guardian_district')->nullable();
            $table->string('parent_guardian_region_code')->nullable();
            $table->string('parent_guardian_region_name')->nullable();
            $table->string('parent_guardian_province_code')->nullable();
            $table->string('parent_guardian_province_name')->nullable();
            $table->string('parent_guardian_city_code')->nullable();
            $table->string('parent_guardian_city_name')->nullable();
            $table->string('parent_guardian_barangay_code')->nullable();
            $table->string('parent_guardian_barangay_name')->nullable();

            // ==================== EDUCATION & EMPLOYMENT ====================
            $table->string('highest_educational_attainment')->nullable();
            $table->string('educational_attainment_before_training')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('employment_before_training_status')->nullable();
            $table->string('employment_before_training_type')->nullable();

            // ==================== LEARNER CLASSIFICATION & SCHOLARSHIP ====================
            $table->json('learner_classification')->nullable();
            $table->string('scholarship_type')->nullable();
            $table->boolean('privacy_consent')->default(false);

            // ==================== TRAINING FIELDS ====================
            $table->string('training_status')->default('enrolled')->nullable();
            $table->unsignedBigInteger('training_batch_id')->nullable()->index();
            $table->unsignedBigInteger('training_schedule_id')->nullable()->index();

            $table->date('training_completed_at')->nullable();
            $table->text('training_remarks')->nullable();

            // ==================== ASSESSMENT FIELDS ====================
            $table->unsignedBigInteger('assessment_batch_id')->nullable()->index();
            $table->enum('assessment_status', ['pending', 'assigned', 'completed', 'failed'])
                  ->default('pending')
                  ->index();
            $table->timestamp('assessment_date')->nullable();

            // ==================== PAYMENT FIELDS - INITIAL (Assessment Only) ====================
            $table->string('payment_proof')->nullable();
            $table->enum('payment_status', ['pending', 'submitted', 'verified', 'rejected'])
                  ->default('pending');
            $table->timestamp('payment_submitted_at')->nullable();
            $table->text('payment_remarks')->nullable();
            $table->string('official_receipt_photo')->nullable();
            $table->timestamp('official_receipt_uploaded_at')->nullable();

            // ==================== PAYMENT FIELDS - 1ST REASSESSMENT ====================
            $table->decimal('reassessment_fee', 10, 2)->nullable();
            $table->string('reassessment_payment_proof')->nullable();
            $table->timestamp('reassessment_payment_date')->nullable();
            $table->enum('reassessment_payment_status', ['pending', 'verified', 'rejected'])
                  ->nullable()
                  ->index();
            $table->string('reassessment_payment_reference')->nullable();
            $table->string('reassessment_official_receipt_photo')->nullable();
            $table->timestamp('reassessment_official_receipt_uploaded_at')->nullable();

            // ==================== PAYMENT FIELDS - 2ND REASSESSMENT ====================
            $table->string('second_reassessment_payment_proof')->nullable();
            $table->timestamp('second_reassessment_payment_date')->nullable();
            $table->enum('second_reassessment_payment_status', ['pending', 'verified', 'rejected'])
                  ->nullable()
                  ->index();
            $table->string('second_reassessment_payment_reference')->nullable();
            $table->string('second_reassessment_official_receipt_photo')->nullable();
            $table->timestamp('second_reassessment_official_receipt_uploaded_at')->nullable();

            // ==================== TIMESTAMPS ====================
            $table->timestamps();

            // ==================== ADDITIONAL INDEXES ====================
            $table->index(['is_reassessment', 'reassessment_attempt']);
        });

        // Add foreign key constraint for assessment_batch_id after assessment_batches table is created
        // This will be handled in a separate migration: add_foreign_key_to_applications_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
