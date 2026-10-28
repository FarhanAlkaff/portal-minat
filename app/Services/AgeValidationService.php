<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Grade;
use Carbon\Carbon;

class AgeValidationService
{
    /**
     * Calculate age & eligibility for given birth_date, academic_year, grade.
     *
     * @return array{reference_date:string, age_years:int, age_months:int, age_days:int, is_eligible:bool, message:string, min_age:?string, max_age:?string}
     */
    public function evaluate(string $birthDate, AcademicYear $year, Grade $grade): array
    {
        $rule = $grade->ageRule;
        $refMonth = $rule?->reference_month ?? 7;
        $refDay = $rule?->reference_day ?? 1;

        $reference = Carbon::create($year->start_year, $refMonth, $refDay)->startOfDay();
        $birth = Carbon::parse($birthDate)->startOfDay();

        if ($birth->greaterThan($reference)) {
            return [
                'reference_date' => $reference->toDateString(),
                'age_years' => 0, 'age_months' => 0, 'age_days' => 0,
                'is_eligible' => false,
                'message' => "Tanggal lahir belum tercapai pada tanggal acuan {$reference->translatedFormat('d F Y')}.",
                'min_age' => $this->fmtRule($rule, 'min'),
                'max_age' => $this->fmtRule($rule, 'max'),
            ];
        }

        $diff = $birth->diff($reference);
        $ageY = $diff->y;
        $ageM = $diff->m;
        $ageD = $diff->d;

        $isEligible = true;
        $message = "Memenuhi persyaratan usia {$grade->name}";

        if ($rule) {
            // Convert to days-since for a stable comparison
            $ageAsDays = $this->approxDays($ageY, $ageM, $ageD);

            $minDays = $this->approxDays(
                $rule->minimum_age_years ?? 0,
                $rule->minimum_age_months ?? 0,
                $rule->minimum_age_days ?? 0,
            );
            $hasMin = ($rule->minimum_age_years !== null || $rule->minimum_age_months !== null || $rule->minimum_age_days !== null);

            $hasMax = ($rule->maximum_age_years !== null || $rule->maximum_age_months !== null || $rule->maximum_age_days !== null);
            $maxDays = $this->approxDays(
                $rule->maximum_age_years ?? 0,
                $rule->maximum_age_months ?? 0,
                $rule->maximum_age_days ?? 0,
            );

            if ($hasMin && $ageAsDays < $minDays) {
                $isEligible = false;
                $message = "Belum memenuhi persyaratan usia {$grade->name}. Minimal " . $this->fmtRule($rule, 'min')
                    . " pada tanggal {$reference->translatedFormat('d F Y')}.";
            } elseif ($hasMax && $ageAsDays > $maxDays) {
                $isEligible = false;
                $message = "Melebihi batas usia {$grade->name}. Maksimal " . $this->fmtRule($rule, 'max')
                    . " pada tanggal {$reference->translatedFormat('d F Y')}.";
            }
        }

        return [
            'reference_date' => $reference->toDateString(),
            'age_years' => $ageY,
            'age_months' => $ageM,
            'age_days' => $ageD,
            'is_eligible' => $isEligible,
            'message' => $message,
            'min_age' => $this->fmtRule($rule, 'min'),
            'max_age' => $this->fmtRule($rule, 'max'),
        ];
    }

    private function approxDays(int $y, int $m, int $d): int
    {
        // Approximate but consistent for comparisons across rules
        return $y * 365 + $m * 30 + $d;
    }

    private function fmtRule($rule, string $type): ?string
    {
        if (!$rule) return null;
        if ($type === 'min') {
            $y = $rule->minimum_age_years;
            $m = $rule->minimum_age_months;
            $d = $rule->minimum_age_days;
        } else {
            $y = $rule->maximum_age_years;
            $m = $rule->maximum_age_months;
            $d = $rule->maximum_age_days;
        }
        if ($y === null && $m === null && $d === null) return null;
        $parts = [];
        if ($y) $parts[] = "{$y} tahun";
        if ($m) $parts[] = "{$m} bulan";
        if ($d) $parts[] = "{$d} hari";
        return $parts ? implode(' ', $parts) : '0 tahun';
    }
}
