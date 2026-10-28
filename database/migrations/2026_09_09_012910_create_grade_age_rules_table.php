<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_age_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('minimum_age_years')->nullable();
            $table->unsignedSmallInteger('minimum_age_months')->nullable();
            $table->unsignedSmallInteger('minimum_age_days')->nullable();
            $table->unsignedSmallInteger('maximum_age_years')->nullable();
            $table->unsignedSmallInteger('maximum_age_months')->nullable();
            $table->unsignedSmallInteger('maximum_age_days')->nullable();
            $table->unsignedTinyInteger('reference_month')->default(7);
            $table->unsignedTinyInteger('reference_day')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_age_rules');
    }
};
