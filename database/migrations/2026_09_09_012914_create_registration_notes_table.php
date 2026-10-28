<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('student_registrations')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note');
            $table->string('note_type', 40)->default('Umum');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_notes');
    }
};
