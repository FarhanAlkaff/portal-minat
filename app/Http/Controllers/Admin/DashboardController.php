<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Grade;
use App\Models\InformationSetting;
use App\Models\StudentCategory;
use App\Models\StudentRegistration;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $activeYear = AcademicYear::active() ?? AcademicYear::orderByDesc('start_year')->first();
        $academicYears = AcademicYear::orderByDesc('start_year')->get();

        // Tentukan tahun untuk Distribusi Pendaftaran:
        // Default adalah tahun yang akan datang (start_year > tahun saat ini) jika ada, atau tahun terbaru/aktif
        $upcomingYear = AcademicYear::where('start_year', '>', now()->year)->orderBy('start_year')->first();
        $defaultYear = $upcomingYear ?? $activeYear ?? $academicYears->first();

        $selectedYearId = $request->query('dist_year_id', $defaultYear?->id);
        $selectedYear = AcademicYear::find($selectedYearId) ?? $defaultYear;

        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $allowedGradeIds = $user->accessibleGradeIds();

        $baseCountQuery = StudentRegistration::query();
        if (!$isSuperAdmin) {
            $baseCountQuery->whereIn('grade_id', $allowedGradeIds);
        }

        $counts = [
            'total' => (clone $baseCountQuery)->count(),
            'menunggu' => (clone $baseCountQuery)->where('administrative_status', 'Menunggu Validasi')->count(),
            'lolos_admin' => (clone $baseCountQuery)->where('administrative_status', 'Lolos Administrasi')->count(),
            'tidak_lolos' => (clone $baseCountQuery)->where('administrative_status', 'Tidak Lolos Administrasi')->count(),
            'layak' => (clone $baseCountQuery)->where('observation_eligibility', 'Layak Observasi')->count(),
            'dipanggil' => (clone $baseCountQuery)->where('status', 'Dipanggil Observasi')->count(),
            'observasi_selesai' => (clone $baseCountQuery)->where('status', 'Observasi Selesai')->count(),
            'lulus_obs' => (clone $baseCountQuery)->where('status', 'Lulus Observasi')->count(),
            'tidak_lulus_obs' => (clone $baseCountQuery)->where('status', 'Tidak Lulus Observasi')->count(),
            'diterima' => (clone $baseCountQuery)->where('status', 'Diterima')->count(),
            'ditolak' => (clone $baseCountQuery)->where('status', 'Ditolak')->count(),
        ];

        $totalRegistrations = max($counts['total'], 1);

        $perGradeQuery = Grade::query()->orderBy('sort_order');
        if (!$isSuperAdmin) {
            $perGradeQuery->whereIn('id', $allowedGradeIds);
        }

        $perGrade = $perGradeQuery->get()
            ->map(function ($g) use ($totalRegistrations) {
                $base = StudentRegistration::where('grade_id', $g->id);
                $total = (clone $base)->count();
                $percentage = round(($total / $totalRegistrations) * 100);
                $color = Grade::colorFor($g->code);

                return (object) [
                    'id' => $g->id,
                    'code' => $g->code,
                    'name' => $g->name,
                    'is_active' => (bool)$g->is_active,
                    'total' => $total,
                    'percentage' => $percentage,
                    'lolos_admin' => (clone $base)->where('administrative_status', 'Lolos Administrasi')->count(),
                    'layak' => (clone $base)->where('observation_eligibility', 'Layak Observasi')->count(),
                    'diterima' => (clone $base)->where('status', 'Diterima')->count(),
                    'color' => $color,
                ];
            });

        // Hanya jenjang yang AKTIF dan dapat diakses oleh user ini
        $activeGrades = $user->accessibleGrades(onlyActive: true);

        // Data Distribusi Kampus & Grade untuk Tahun yang Dipilih
        $campuses = Campus::where('is_active', true)->get();
        $totalRegisteredInYear = 0;
        $totalTargetInYear = 0;

        $campusDistribution = $campuses->map(function ($campus) use ($selectedYear, $activeGrades, &$totalRegisteredInYear, &$totalTargetInYear) {
            $campusTotalRegistered = 0;
            $campusTotalTarget = 0;

            $gradesData = $activeGrades->map(function ($grade) use ($selectedYear, $campus, &$campusTotalRegistered, &$campusTotalTarget) {
                $count = StudentRegistration::where('academic_year_id', $selectedYear->id)
                    ->where('campus_id', $campus->id)
                    ->where('grade_id', $grade->id)
                    ->count();

                $target = (int) InformationSetting::get("target_{$selectedYear->id}_{$campus->id}_{$grade->id}", 0);

                $campusTotalRegistered += $count;
                $campusTotalTarget += $target;

                $percent = $target > 0 ? min(round(($count / $target) * 100, 1), 100) : 0;

                return (object) [
                    'grade' => $grade,
                    'count' => $count,
                    'target' => $target,
                    'percent' => $percent,
                    'color' => Grade::colorFor($grade->code),
                ];
            });

            $totalRegisteredInYear += $campusTotalRegistered;
            $totalTargetInYear += $campusTotalTarget;

            $campusPercent = $campusTotalTarget > 0 
                ? min(round(($campusTotalRegistered / $campusTotalTarget) * 100, 1), 100) 
                : 0;

            return (object) [
                'campus' => $campus,
                'total_registered' => $campusTotalRegistered,
                'total_target' => $campusTotalTarget,
                'percent' => $campusPercent,
                'grades' => $gradesData,
            ];
        });

        $totalTargetPercent = $totalTargetInYear > 0 
            ? round(($totalRegisteredInYear / $totalTargetInYear) * 100, 1) 
            : 0;

        // Pendaftar terbaru sesuai izin akses grade
        $recentQuery = StudentRegistration::with(['grade', 'campus', 'category'])->latest('id');
        if (!$isSuperAdmin) {
            $recentQuery->whereIn('grade_id', $allowedGradeIds);
        }
        $recentRegistrations = $recentQuery->take(6)->get();

        // Jadwal observasi terdekat sesuai izin akses grade
        $obsQuery = StudentRegistration::with(['grade', 'campus'])
            ->whereNotNull('observation_date')
            ->where('status', 'Dipanggil Observasi')
            ->orderBy('observation_date')
            ->orderBy('observation_time');
        if (!$isSuperAdmin) {
            $obsQuery->whereIn('grade_id', $allowedGradeIds);
        }
        $upcomingObservations = $obsQuery->take(4)->get();

        // Kategori Siswa untuk tahun terpilih sesuai izin akses grade
        $categories = StudentCategory::all()->map(function ($cat) use ($selectedYear, $isSuperAdmin, $allowedGradeIds) {
            $catQuery = StudentRegistration::where('academic_year_id', $selectedYear->id)
                ->where('student_category_id', $cat->id);
            if (!$isSuperAdmin) {
                $catQuery->whereIn('grade_id', $allowedGradeIds);
            }
            $count = $catQuery->count();
            return (object) [
                'name' => $cat->name,
                'count' => $count,
            ];
        });

        return view('admin.dashboard', compact(
            'counts',
            'perGrade',
            'recentRegistrations',
            'upcomingObservations',
            'campusDistribution',
            'categories',
            'activeYear',
            'academicYears',
            'selectedYear',
            'activeGrades',
            'campuses',
            'totalRegisteredInYear',
            'totalTargetInYear',
            'totalTargetPercent'
        ));
    }

    public function saveTargets(Request $request)
    {
        $yearId = $request->input('academic_year_id');
        $targets = $request->input('targets', []);

        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $allowedGradeIds = $user->accessibleGradeIds();

        foreach ($targets as $campusId => $gradeTargets) {
            foreach ($gradeTargets as $gradeId => $targetVal) {
                if (!$isSuperAdmin && !in_array((int)$gradeId, $allowedGradeIds)) {
                    continue;
                }
                $targetVal = max(0, (int)$targetVal);
                InformationSetting::set("target_{$yearId}_{$campusId}_{$gradeId}", (string)$targetVal);
            }
        }

        return redirect()->route('admin.dashboard', ['dist_year_id' => $yearId])
            ->with('success', 'Target kuota murid untuk tahun ajaran ' . AcademicYear::find($yearId)?->name . ' berhasil disimpan!');
    }
}

