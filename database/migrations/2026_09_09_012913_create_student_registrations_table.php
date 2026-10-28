<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 30);
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('grade_id')->constrained()->restrictOnDelete();
            $table->foreignId('campus_id')->constrained()->restrictOnDelete();
            $table->foreignId('student_category_id')->constrained()->restrictOnDelete();

            // Data anak
            $table->string('full_name');
            $table->string('normalized_name', 191);
            $table->string('nickname');
            $table->enum('gender', ['L', 'P']);
            $table->string('nisn', 20)->nullable();
            $table->string('class', 30)->nullable();
            $table->string('birth_place');
            $table->date('birth_date');
            $table->string('family_status', 30);
            $table->unsignedSmallInteger('child_order');
            $table->text('address');
            $table->string('phone', 25);
            $table->string('previous_school')->nullable();

            // Data orang tua
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('grandfather_name');
            $table->string('father_job')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('father_phone', 25)->nullable();
            $table->string('mother_phone', 25)->nullable();

            // Workflow
            $table->string('status', 40)->default('Terdaftar');
            $table->string('administrative_status', 40)->default('Menunggu Validasi');
            $table->string('observation_eligibility', 40)->default('Belum Ditentukan');

            // Usia
            $table->date('age_reference_date');
            $table->unsignedSmallInteger('age_years');
            $table->unsignedSmallInteger('age_months');
            $table->unsignedSmallInteger('age_days');
            $table->string('age_validation_status', 30)->default('Memenuhi');

            // Observasi
            $table->date('observation_date')->nullable();
            $table->time('observation_time')->nullable();
            $table->string('observation_location')->nullable();
            $table->string('observation_room')->nullable();
            $table->text('observation_notes')->nullable();
            $table->string('observation_result', 30)->nullable(); // Lulus / Tidak Lulus

            // Verification
            $table->string('verification_token', 64)->unique();
            $table->timestamp('registered_at')->useCurrent();

            $table->timestamps();

            // Unique constraint (Rule 4)
            $table->unique(
                ['normalized_name', 'birth_date', 'grade_id', 'academic_year_id'],
                'uniq_registration_child'
            );
            $table->unique(['registration_number', 'academic_year_id'], 'uniq_regnum_per_year');
            $table->index(['grade_id', 'academic_year_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_registrations');
    }
};
