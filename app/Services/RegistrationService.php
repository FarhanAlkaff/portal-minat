<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\RegistrationStatusHistory;
use App\Models\StudentRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegistrationService
{
    public function __construct(
        private AgeValidationService $ageService,
        private DuplicateRegistrationService $duplicateService,
        private RegistrationNumberService $numberService,
    ) {}

    /**
     * @return array{status:'duplicate'|'created', registration:StudentRegistration}
     * @throws \RuntimeException on age failure
     */
    public function register(array $data, ?int $adminId = null): array
    {
        $year = AcademicYear::findOrFail($data['academic_year_id']);
        $grade = Grade::with('ageRule')->findOrFail($data['grade_id']);

        // Age validation (backend)
        $ageResult = $this->ageService->evaluate($data['birth_date'], $year, $grade);
        if (!$ageResult['is_eligible']) {
            throw new \RuntimeException($ageResult['message']);
        }

        $normalized = NameNormalizer::normalize($data['full_name']);

        return DB::transaction(function () use ($data, $year, $grade, $ageResult, $normalized, $adminId) {
            // Duplicate check
            $existing = $this->duplicateService->find(
                $normalized,
                $data['birth_date'],
                $grade->id,
                $year->id
            );
            if ($existing) {
                return ['status' => 'duplicate', 'registration' => $existing];
            }

            $regNumber = $this->numberService->next($year->id, $grade->id, $data['campus_id']);

            $registration = StudentRegistration::create([
                'registration_number' => $regNumber,
                'academic_year_id' => $year->id,
                'grade_id' => $grade->id,
                'campus_id' => $data['campus_id'],
                'student_category_id' => $data['student_category_id'],

                'full_name' => $data['full_name'],
                'normalized_name' => $normalized,
                'nickname' => $data['nickname'],
                'gender' => $data['gender'],
                'nisn' => $data['nisn'] ?? null,
                'class' => $data['class'] ?? null,
                'birth_place' => $data['birth_place'],
                'birth_date' => $data['birth_date'],
                'family_status' => $data['family_status'],
                'child_order' => $data['child_order'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'previous_school' => $data['previous_school'] ?? null,

                'father_name' => $data['father_name'],
                'mother_name' => $data['mother_name'],
                'grandfather_name' => $data['grandfather_name'],
                'father_job' => $data['father_job'] ?? null,
                'mother_job' => $data['mother_job'] ?? null,
                'father_phone' => $data['father_phone'] ?? null,
                'mother_phone' => $data['mother_phone'] ?? null,

                'status' => 'Terdaftar',
                'administrative_status' => 'Menunggu Validasi',
                'observation_eligibility' => 'Belum Ditentukan',

                'age_reference_date' => $ageResult['reference_date'],
                'age_years' => $ageResult['age_years'],
                'age_months' => $ageResult['age_months'],
                'age_days' => $ageResult['age_days'],
                'age_validation_status' => 'Memenuhi',

                'verification_token' => Str::random(40),
                'registered_at' => now(),
            ]);

            RegistrationStatusHistory::create([
                'registration_id' => $registration->id,
                'old_status' => null,
                'new_status' => 'Terdaftar',
                'changed_by' => $adminId,
                'note' => $adminId ? 'Diinput oleh admin' : 'Pendaftaran online',
                'created_at' => now(),
            ]);

            return ['status' => 'created', 'registration' => $registration];
        });
    }
}
