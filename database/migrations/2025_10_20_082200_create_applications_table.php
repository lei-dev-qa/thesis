<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->nullable()->index();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
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
            $table->boolean('correction_requested')->default(false);
            $table->text('correction_message')->nullable();
            $table->timestamp('correction_requested_at')->nullable();
            $table->boolean('was_corrected')->default(false);
            $table->timestamp('resubmitted_at')->nullable();
            $table->string('photo')->nullable();
            $table->string('surname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('middleinitial', 5)->nullable();
            $table->string('name_extension')->nullable();
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
            $table->string('highest_educational_attainment')->nullable();
            $table->string('educational_attainment_before_training')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('employment_before_training_status')->nullable();
            $table->string('employment_before_training_type')->nullable();
            $table->json('learner_classification')->nullable();
            $table->string('scholarship_type')->nullable();
            $table->boolean('privacy_consent')->default(false);
            $table->string('training_status')->default('enrolled')->nullable();
            $table->unsignedBigInteger('training_batch_id')->nullable()->index();
            $table->unsignedBigInteger('training_schedule_id')->nullable()->index();
            $table->date('training_completed_at')->nullable();
            $table->text('training_remarks')->nullable();
            $table->unsignedBigInteger('assessment_batch_id')->nullable()->index();
            $table->enum('assessment_status', ['pending', 'assigned', 'completed', 'failed'])
                  ->default('pending')
                  ->index();
            $table->timestamp('assessment_date')->nullable();
            $table->string('payment_proof')->nullable();
            $table->enum('payment_status', ['pending', 'submitted', 'verified', 'rejected'])
                  ->default('pending');
            $table->timestamp('payment_submitted_at')->nullable();
            $table->text('payment_remarks')->nullable();
            $table->string('official_receipt_photo')->nullable();
            $table->timestamp('official_receipt_uploaded_at')->nullable();
            $table->decimal('reassessment_fee', 10, 2)->nullable();
            $table->string('reassessment_payment_proof')->nullable();
            $table->timestamp('reassessment_payment_date')->nullable();
            $table->enum('reassessment_payment_status', ['pending', 'verified', 'rejected'])
                  ->nullable()
                  ->index();
            $table->string('reassessment_payment_reference')->nullable();
            $table->string('reassessment_official_receipt_photo')->nullable();
            $table->timestamp('reassessment_official_receipt_uploaded_at')->nullable();
            $table->string('second_reassessment_payment_proof')->nullable();
            $table->timestamp('second_reassessment_payment_date')->nullable();
            $table->enum('second_reassessment_payment_status', ['pending', 'verified', 'rejected'])
                  ->nullable()
                  ->index();
            $table->string('second_reassessment_payment_reference')->nullable();
            $table->string('second_reassessment_official_receipt_photo')->nullable();
            $table->timestamp('second_reassessment_official_receipt_uploaded_at')->nullable();
            $table->timestamps();
            $table->index(['is_reassessment', 'reassessment_attempt']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
