<?php
use App\Models\InformationSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Seed default settings for new features (idempotent)
        InformationSetting::updateOrCreate(['key' => 'logo_path'], ['value' => null]);
        InformationSetting::updateOrCreate(['key' => 'announcement_active'], ['value' => '1']);
        InformationSetting::updateOrCreate(['key' => 'announcement_title'], ['value' => 'Pengumuman Penting']);
        InformationSetting::updateOrCreate(['key' => 'announcement_items'], [
            'value' => "Gelombang 1 pendaftaran dibuka hingga 31 Maret 2026 — daftar sekarang!\nObservasi TK-A/TK-B setiap Sabtu · Ruang Observasi Kampus Utama\nDapatkan diskon pendaftaran khusus untuk anak kedua & seterusnya"
        ]);
        InformationSetting::updateOrCreate(['key' => 'wa_country_code'], ['value' => '62']);
        InformationSetting::updateOrCreate(['key' => 'wa_driver'], ['value' => 'link']); // link | fonnte
        InformationSetting::updateOrCreate(['key' => 'wa_fonnte_token'], ['value' => null]);
    }

    public function down(): void {}
};
