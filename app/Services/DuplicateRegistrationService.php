<?php

namespace App\Services;

use App\Models\StudentRegistration;

class DuplicateRegistrationService
{
    public function find(string $normalizedName, string $birthDate, int $gradeId, int $academicYearId): ?StudentRegistration
    {
        return StudentRegistration::query()
            ->where('normalized_name', $normalizedName)
            ->whereDate('birth_date', $birthDate)
            ->where('grade_id', $gradeId)
            ->where('academic_year_id', $academicYearId)
            ->first();
    }
}
