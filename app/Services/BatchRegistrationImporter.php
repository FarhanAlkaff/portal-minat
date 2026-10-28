<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Grade;
use App\Models\StudentCategory;
use Illuminate\Http\UploadedFile;

class BatchRegistrationImporter
{
    public function __construct(private RegistrationService $service) {}

    public function csvHeaderTemplate(): array
    {
        return [
            'academic_year_name', 'grade_code', 'campus_name', 'category_name',
            'full_name', 'nickname', 'gender', 'nisn', 'class',
            'birth_place', 'birth_date', 'family_status', 'child_order', 'address', 'phone', 'previous_school',
            'father_name', 'mother_name', 'grandfather_name', 'father_job', 'mother_job', 'father_phone', 'mother_phone',
        ];
    }

    public function import(UploadedFile $file, ?int $adminId = null, ?array $allowedGradeIds = null): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) return ['success' => 0, 'failed' => 0, 'errors' => ['Tidak bisa membuka file']];

        $header = fgetcsv($handle);
        if (!$header) return ['success' => 0, 'failed' => 0, 'errors' => ['File kosong / header tidak valid']];
        $header = array_map(fn($h) => trim(strtolower($h)), $header);

        $yearMap = AcademicYear::pluck('id', 'name')->toArray();
        $gradeMap = Grade::pluck('id', 'code')->toArray();
        $campusMap = Campus::pluck('id', 'name')->toArray();
        $catMap = StudentCategory::pluck('id', 'name')->toArray();
        $firstCampusId = Campus::value('id');
        $firstCatId = StudentCategory::value('id');

        $success = 0; $failed = 0; $errors = []; $created = [];
        $row = 1;
        while (($cells = fgetcsv($handle)) !== false) {
            $row++;
            if (!array_filter($cells, fn($c) => trim((string)$c) !== '')) continue;
            $data = array_combine($header, array_pad($cells, count($header), null));
            try {
                $yearId = $yearMap[trim($data['academic_year_name'] ?? '')] ?? null;
                $gradeId = $gradeMap[trim($data['grade_code'] ?? '')] ?? null;
                if (!$yearId) throw new \RuntimeException("Tahun ajaran '" . ($data['academic_year_name'] ?? '') . "' tidak ditemukan");
                if (!$gradeId) throw new \RuntimeException("Kode grade '" . ($data['grade_code'] ?? '') . "' tidak ditemukan");
                if ($allowedGradeIds !== null && !in_array($gradeId, $allowedGradeIds)) {
                    throw new \RuntimeException("Anda tidak memiliki hak akses untuk import jenjang '" . ($data['grade_code'] ?? '') . "'");
                }
                $campusId = $campusMap[trim($data['campus_name'] ?? '')] ?? $firstCampusId;
                $catId = $catMap[trim($data['category_name'] ?? '')] ?? $firstCatId;

                $payload = [
                    'academic_year_id' => $yearId,
                    'grade_id' => $gradeId,
                    'campus_id' => $campusId,
                    'student_category_id' => $catId,
                    'full_name' => trim($data['full_name'] ?? ''),
                    'nickname' => trim($data['nickname'] ?? '') ?: trim($data['full_name'] ?? ''),
                    'gender' => strtoupper(substr(trim($data['gender'] ?? 'L'), 0, 1)),
                    'nisn' => $data['nisn'] ?? null,
                    'class' => $data['class'] ?? null,
                    'birth_place' => trim($data['birth_place'] ?? ''),
                    'birth_date' => trim($data['birth_date'] ?? ''),
                    'family_status' => trim($data['family_status'] ?? 'Anak Kandung'),
                    'child_order' => (int)($data['child_order'] ?? 1),
                    'address' => trim($data['address'] ?? '-'),
                    'phone' => trim($data['phone'] ?? '-'),
                    'previous_school' => $data['previous_school'] ?? null,
                    'father_name' => trim($data['father_name'] ?? ''),
                    'mother_name' => trim($data['mother_name'] ?? ''),
                    'grandfather_name' => trim($data['grandfather_name'] ?? ''),
                    'father_job' => $data['father_job'] ?? null,
                    'mother_job' => $data['mother_job'] ?? null,
                    'father_phone' => $data['father_phone'] ?? null,
                    'mother_phone' => $data['mother_phone'] ?? null,
                ];

                foreach (['full_name', 'birth_place', 'birth_date', 'father_name', 'mother_name', 'grandfather_name'] as $req) {
                    if (empty($payload[$req])) throw new \RuntimeException("Kolom wajib '{$req}' kosong");
                }
                if (!in_array($payload['gender'], ['L', 'P'])) $payload['gender'] = 'L';

                $result = $this->service->register($payload, $adminId);
                if ($result['status'] === 'duplicate') {
                    $errors[] = "Baris {$row}: DUPLIKAT — sudah terdaftar sebagai {$result['registration']->registration_number}";
                    $failed++;
                } else {
                    $success++;
                    $created[] = $result['registration']->registration_number;
                }
            } catch (\Throwable $e) {
                $errors[] = "Baris {$row}: " . $e->getMessage();
                $failed++;
            }
        }
        fclose($handle);
        return ['success' => $success, 'failed' => $failed, 'errors' => $errors, 'created' => $created];
    }
}
