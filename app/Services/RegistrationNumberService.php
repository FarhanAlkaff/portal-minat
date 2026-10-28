<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Grade;
use App\Models\RegistrationSequence;
use Illuminate\Support\Facades\DB;

class RegistrationNumberService
{
    /**
     * Return next registration number for a grade + academic year + campus using a row-locked sequence.
     * MUST be called inside a DB transaction.
     */
    public function next(int $academicYearId, int $gradeId, int $campusId): string
    {
        $year = AcademicYear::findOrFail($academicYearId);
        $grade = Grade::findOrFail($gradeId);
        $campus = Campus::findOrFail($campusId);

        // Ambil 2 digit akhir tahun ajaran (contoh: 2026 -> 26)
        $yearCode = $year->start_year 
            ? substr((string) $year->start_year, -2) 
            : (preg_match('/\d{2,4}/', $year->name, $m) ? substr($m[0], -2) : date('y'));

        // Bersihkan kode grade (contoh: TK-A -> TKA, SD -> SD)
        $cleanGradeCode = strtoupper(str_replace(['-', ' '], '', $grade->code));

        // Tentukan kode kampus berdasarkan nama (default: 3 atau 7, atau angka pertama)
        $campusName = strtoupper($campus->name);
        if (str_contains($campusName, '3')) {
            $campusCode = '3';
        } elseif (str_contains($campusName, '7')) {
            $campusCode = '7';
        } else {
            preg_match('/\d+/', $campusName, $m);
            $campusCode = $m[0] ?? (string) $campus->id;
        }

        // PENCARIAN SEQUENCE DIPISAH BERDASARKAN ACADEMIC_YEAR_ID + GRADE_ID + CAMPUS_ID
        // Ini membuat nomor urut (0001, 0002, dst) terisolasi dan mandiri per kampus & tahun
        $seq = RegistrationSequence::query()
            ->where('academic_year_id', $academicYearId)
            ->where('grade_id', $gradeId)
            ->where('campus_id', $campusId)
            ->lockForUpdate()
            ->first();

        if (!$seq) {
            $seq = RegistrationSequence::create([
                'academic_year_id' => $academicYearId,
                'grade_id' => $gradeId,
                'campus_id' => $campusId,
                'last_sequence' => 0,
            ]);
            // Re-fetch with lock
            $seq = RegistrationSequence::where('id', $seq->id)->lockForUpdate()->first();
        }

        $seq->last_sequence += 1;
        $seq->save();

        $formattedNumber = str_pad((string) $seq->last_sequence, 4, '0', STR_PAD_LEFT);

        // Hasil akhir: KodeGradeTahun - KodeKampus - NomorUrut (Contoh: SD26-3-0001 atau TKA26-3-0001)
        return "{$cleanGradeCode}{$yearCode}-{$campusCode}-{$formattedNumber}";
    }
}