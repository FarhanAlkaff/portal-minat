<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_sliders', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed 6 default initial slides so existing photos are immediately manageable
        $now = now();
        $defaults = [
            ['image_path' => 'images/pgnew1.jpeg', 'caption' => 'Playgroup Al-Azhar Cairo', 'sort_order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['image_path' => 'images/pgnew2.jpeg', 'caption' => 'Aktivitas Belajar & Bermain', 'sort_order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['image_path' => 'images/sdnew1.jpg', 'caption' => 'SD Islam Al-Azhar Cairo', 'sort_order' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['image_path' => 'images/sdnew2.jpg', 'caption' => 'Ruang Belajar & Fasilitas Modern', 'sort_order' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['image_path' => 'images/smpnew1.jpg', 'caption' => 'SMP Islam Al-Azhar Cairo', 'sort_order' => 5, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['image_path' => 'images/smpnew2.jpg', 'caption' => 'Pengembangan Minat & Bakat Siswa', 'sort_order' => 6, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('landing_sliders')->insert($defaults);
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_sliders');
    }
};
