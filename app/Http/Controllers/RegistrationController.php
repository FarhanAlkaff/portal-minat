<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Grade;
use App\Models\InformationSetting;
use App\Models\StudentCategory;
use App\Models\StudentRegistration;
use App\Services\AgeValidationService;
use App\Services\NameNormalizer;
use App\Services\PdfService;
use App\Services\RegistrationService;
use App\Http\Requests\StoreRegistrationRequest;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function __construct(
        private RegistrationService $registrationService,
        private AgeValidationService $ageService,
        private PdfService $pdfService,
    ) {}

    public function landing()
    {
        $grades = Grade::with('ageRule')->where('is_active', true)->orderBy('sort_order')->get();
        $years = AcademicYear::orderByDesc('start_year')->get();
        $activeYear = AcademicYear::active();
        $settings = InformationSetting::pluck('value', 'key');
        $sliders = \App\Models\LandingSlider::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        return view('landing', compact('grades', 'years', 'activeYear', 'settings', 'sliders'));
    }

    public function info(string $section)
    {
        $settings = InformationSetting::pluck('value', 'key');
        $sections = [
            'informasi' => ['title' => 'Informasi Pendaftaran', 'key' => 'info_pendaftaran'],
            'persyaratan' => ['title' => 'Persyaratan Pendaftaran', 'key' => 'info_persyaratan'],
            'alur-pendaftaran' => ['title' => 'Alur Pendaftaran', 'key' => 'info_alur'],
            'jadwal' => ['title' => 'Jadwal Pendaftaran', 'key' => 'info_jadwal'],
            'faq' => ['title' => 'Pertanyaan Umum (FAQ)', 'key' => 'info_faq'],
        ];
        abort_unless(isset($sections[$section]), 404);
        $current = $sections[$section];
        return view('info', ['title' => $current['title'], 'content' => $settings[$current['key']] ?? '']);
    }

    public function create(Request $request)
    {
        $grades = Grade::with('ageRule')->where('is_active', true)->orderBy('sort_order')->get();
        $years = AcademicYear::orderByDesc('start_year')->get();
        $campuses = Campus::where('is_active', true)->get();
        $categories = StudentCategory::where('is_active', true)->get();

        return view('registration.create', [
            'grades' => $grades,
            'years' => $years,
            'campuses' => $campuses,
            'categories' => $categories,
            'preselectedGrade' => $request->query('grade'),
            'preselectedYear' => $request->query('year'),
        ]);
    }

    public function validateAge(Request $request)
    {
        $data = $request->validate([
            'birth_date' => ['required', 'date'],
            'grade_id' => ['required', 'exists:grades,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);
        $year = AcademicYear::findOrFail($data['academic_year_id']);
        $grade = Grade::with('ageRule')->findOrFail($data['grade_id']);
        return response()->json($this->ageService->evaluate($data['birth_date'], $year, $grade));
    }

    public function checkDuplicate(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
            'grade_id' => ['required', 'exists:grades,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);
        $normalized = NameNormalizer::normalize($data['full_name']);
        $existing = StudentRegistration::where('normalized_name', $normalized)
            ->whereDate('birth_date', $data['birth_date'])
            ->where('grade_id', $data['grade_id'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->first();
        if ($existing) {
            return response()->json([
                'exists' => true,
                'registration_number' => $existing->registration_number,
                'status' => $existing->status,
                'view_url' => route('registration.result', $existing->registration_number),
            ]);
        }
        return response()->json(['exists' => false]);
    }

    public function store(StoreRegistrationRequest $request)
    {
        try {
            $result = $this->registrationService->register($request->validated());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['age' => $e->getMessage()])->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            // Unique index violation fallback (race condition)
            if ($e->getCode() === '23000') {
                $normalized = NameNormalizer::normalize($request->input('full_name'));
                $existing = StudentRegistration::where('normalized_name', $normalized)
                    ->whereDate('birth_date', $request->input('birth_date'))
                    ->where('grade_id', $request->input('grade_id'))
                    ->where('academic_year_id', $request->input('academic_year_id'))
                    ->first();
                if ($existing) {
                    return redirect()->route('registration.result', $existing->registration_number)
                        ->with('duplicate', true);
                }
            }
            throw $e;
        }

        $reg = $result['registration'];
        if ($result['status'] === 'duplicate') {
            return redirect()->route('registration.result', $reg->registration_number)->with('duplicate', true);
        }
        return redirect()->route('registration.result', $reg->registration_number)->with('success', true);
    }

    public function result(string $number)
    {
        $reg = StudentRegistration::with(['academicYear', 'grade', 'campus', 'category', 'histories.changer'])
            ->where('registration_number', $number)
            ->latest('id')
            ->firstOrFail();
        return view('registration.result', compact('reg'));
    }

    public function check(Request $request)
    {
        $found = null;
        $error = null;
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'registration_number' => ['required', 'string'],
                'birth_date' => ['required', 'date'],
            ]);
            $reg = StudentRegistration::where('registration_number', trim($data['registration_number']))
                ->whereDate('birth_date', $data['birth_date'])
                ->first();
            if ($reg) {
                return redirect()->route('registration.result', $reg->registration_number);
            }
            $error = 'Data pendaftaran tidak ditemukan. Pastikan nomor pendaftaran dan tanggal lahir sesuai.';
        }
        return view('registration.check', compact('found', 'error'));
    }

    public function verify(string $token)
    {
        $reg = StudentRegistration::with(['academicYear', 'grade'])
            ->where('verification_token', $token)
            ->firstOrFail();
        return view('registration.verify', compact('reg'));
    }

    public function pdf(string $number)
    {
        $reg = StudentRegistration::where('registration_number', $number)->latest('id')->firstOrFail();
        return $this->pdfService->proof($reg)
            ->download("Bukti-Pendaftaran-{$reg->registration_number}.pdf");
    }

    public function observationLetter(string $number)
    {
        $reg = StudentRegistration::where('registration_number', $number)->latest('id')->firstOrFail();
        abort_unless((bool) $reg->observation_date, 404, 'Jadwal observasi belum ditentukan.');
        return $this->pdfService->observationLetter($reg)
            ->download("Surat-Observasi-{$reg->registration_number}.pdf");
    }
}
