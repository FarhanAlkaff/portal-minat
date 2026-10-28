<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Grade;
use App\Models\GradeAgeRule;
use App\Models\InformationSetting;
use App\Models\StudentCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'admin@alazharcairoplg.sch.id'],
            [
                'name' => 'Super Administrator',
                'role' => 'super_admin',
                'password' => Hash::make('Admin@12345'),
                'email_verified_at' => now(),
            ]
        );

        // Academic years
        AcademicYear::updateOrCreate(['name' => '2026/2027'], ['start_year' => 2026, 'end_year' => 2027, 'is_active' => true]);
        AcademicYear::updateOrCreate(['name' => '2027/2028'], ['start_year' => 2027, 'end_year' => 2028, 'is_active' => false]);
        AcademicYear::updateOrCreate(['name' => '2028/2029'], ['start_year' => 2028, 'end_year' => 2029, 'is_active' => false]);

        // Grades + age rules
        $grades = [
            ['code' => 'PG',    'name' => 'PG / Playgroup',   'sort_order' => 1, 'desc' => 'Kelompok Bermain (2 - 3 tahun 11 bulan)',
                'rule' => ['min_y' => 2, 'min_m' => 0, 'min_d' => 0, 'max_y' => 3, 'max_m' => 11, 'max_d' => 30]],
            ['code' => 'TK-A',  'name' => 'TK-A / Kelompok A', 'sort_order' => 2, 'desc' => 'Taman Kanak-Kanak A (minimal 4 tahun)',
                'rule' => ['min_y' => 4, 'min_m' => 0, 'min_d' => 0]],
            ['code' => 'TK-B',  'name' => 'TK-B / Kelompok B', 'sort_order' => 3, 'desc' => 'Taman Kanak-Kanak B (minimal 5 tahun)',
                'rule' => ['min_y' => 5, 'min_m' => 0, 'min_d' => 0]],
            ['code' => 'SD',    'name' => 'Sekolah Dasar',    'sort_order' => 4, 'desc' => 'Sekolah Dasar (minimal 6 tahun)',
                'rule' => ['min_y' => 6, 'min_m' => 0, 'min_d' => 0]],
            ['code' => 'SMP',   'name' => 'Sekolah Menengah Pertama', 'sort_order' => 5, 'desc' => 'SMP (maksimal 15 tahun)',
                'rule' => ['max_y' => 15, 'max_m' => 11, 'max_d' => 30]],
            ['code' => 'SMA',   'name' => 'Sekolah Menengah Atas', 'sort_order' => 6, 'desc' => 'SMA (aturan usia configurable oleh admin)',
                'rule' => ['min_y' => 15, 'min_m' => 0, 'min_d' => 0, 'max_y' => 18, 'max_m' => 11, 'max_d' => 30]],
        ];

        foreach ($grades as $g) {
            $grade = Grade::updateOrCreate(
                ['code' => $g['code']],
                ['name' => $g['name'], 'description' => $g['desc'], 'sort_order' => $g['sort_order'], 'is_active' => true]
            );
            $r = $g['rule'];
            GradeAgeRule::updateOrCreate(
                ['grade_id' => $grade->id],
                [
                    'minimum_age_years'  => $r['min_y'] ?? null,
                    'minimum_age_months' => $r['min_m'] ?? null,
                    'minimum_age_days'   => $r['min_d'] ?? null,
                    'maximum_age_years'  => $r['max_y'] ?? null,
                    'maximum_age_months' => $r['max_m'] ?? null,
                    'maximum_age_days'   => $r['max_d'] ?? null,
                    'reference_month' => 7,
                    'reference_day' => 1,
                    'is_active' => true,
                ]
            );
        }

        // Grade Admins
        $gradeAdmins = [
            [
                'email' => 'admin.tk@alazharcairoplg.sch.id',
                'name' => 'Admin PG - TK',
                'grades' => ['PG', 'TK-A', 'TK-B'],
            ],
            [
                'email' => 'admin.sd@alazharcairoplg.sch.id',
                'name' => 'Admin SD',
                'grades' => ['SD'],
            ],
            [
                'email' => 'admin.smp@alazharcairoplg.sch.id',
                'name' => 'Admin SMP',
                'grades' => ['SMP'],
            ],
            [
                'email' => 'admin.sma@alazharcairoplg.sch.id',
                'name' => 'Admin SMA',
                'grades' => ['SMA'],
            ],
        ];

        foreach ($gradeAdmins as $ga) {
            $user = User::updateOrCreate(
                ['email' => $ga['email']],
                [
                    'name' => $ga['name'],
                    'role' => 'grade_admin',
                    'password' => Hash::make('Admin@12345'),
                    'email_verified_at' => now(),
                ]
            );
            $gradeIds = Grade::whereIn('code', $ga['grades'])->pluck('id');
            $user->grades()->sync($gradeIds);
        }

        // Campus
        Campus::updateOrCreate(
            ['name' => 'Al-Azhar Cairo Palembang - Kampus Utama'],
            ['address' => 'Jl. Jend. Sudirman KM. 3, Palembang', 'is_active' => true]
        );

        // Categories
        StudentCategory::updateOrCreate(['name' => 'Siswa Baru'], ['is_active' => true]);
        StudentCategory::updateOrCreate(['name' => 'Pindahan'], ['is_active' => true]);

        // Information settings
        $defaults = [
            'landing_hero_title' => 'Pendaftaran Murid Baru',
            'landing_hero_subtitle' => 'Al-Azhar Cairo Palembang',
            'landing_hero_description' => 'Selamat datang di PORMIN (Portal Minat). Silakan melakukan pendaftaran calon murid baru secara online dengan mengisi data secara lengkap dan benar.',
            'info_pendaftaran' => "Pendaftaran murid baru Al-Azhar Cairo Palembang dilakukan secara online melalui portal PORMIN. Pastikan Anda mengisi seluruh data dengan lengkap dan benar sesuai dokumen resmi anak.",
            'info_persyaratan' => "• Fotokopi Akta Kelahiran\n• Fotokopi Kartu Keluarga\n• Fotokopi KTP Orang Tua\n• Pas foto anak terbaru\n• Ijazah / rapor sekolah asal (untuk pindahan)\n• Memenuhi persyaratan usia sesuai jenjang",
            'info_alur' => "1. Pilih tahun ajaran & grade\n2. Isi formulir pendaftaran online\n3. Sistem melakukan validasi usia otomatis\n4. Konfirmasi & submit\n5. Unduh bukti pendaftaran\n6. Menunggu validasi administrasi oleh sekolah\n7. Panggilan observasi\n8. Pengumuman hasil penerimaan",
            'info_jadwal' => "Gelombang 1: 1 Januari – 31 Maret\nGelombang 2: 1 April – 30 Juni\nObservasi: sesuai jadwal panggilan sekolah",
            'info_faq' => "Q: Apakah satu orang tua bisa mendaftarkan lebih dari satu anak?\nA: Ya, satu orang tua dapat mendaftarkan beberapa anak sekaligus.\n\nQ: Bagaimana jika saya lupa nomor pendaftaran?\nA: Silakan hubungi admin sekolah dengan menyertakan nama anak dan tanggal lahir.",
            'contact_phone' => '(0711) 000-0000',
            'contact_email' => 'info@alazharcairoplg.sch.id',
            'contact_address' => 'Jl. Jend. Sudirman KM. 3, Palembang',
        ];
        foreach ($defaults as $k => $v) {
            InformationSetting::updateOrCreate(['key' => $k], ['value' => $v]);
        }
    }
}
