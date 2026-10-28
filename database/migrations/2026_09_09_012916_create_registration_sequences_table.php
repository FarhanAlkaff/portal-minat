<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grade_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('last_sequence')->default(0);
            $table->timestamps();
            $table->unique(['academic_year_id', 'grade_id'], 'uniq_sequence_year_grade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_sequences');
    }
};
