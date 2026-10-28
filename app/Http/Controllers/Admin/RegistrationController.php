<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Grade;
use App\Models\RegistrationNote;
use App\Models\RegistrationStatusHistory;
use App\Models\StudentCategory;
use App\Models\StudentRegistration;
use App\Services\AuditLogger;
use App\Services\PdfService;
use App\Services\RegistrationService;
use App\Http\Requests\StoreRegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function __construct(private RegistrationService $registrationService, private PdfService $pdfService) {}

    private function checkGradeAccess(StudentRegistration $registration): void
    {
        $user = auth()->user();
        if ($user && !$user->isSuperAdmin() && !in_array($registration->grade_id, $user->accessibleGradeIds())) {
            abort(403, 'Anda tidak memiliki hak akses untuk data pendaftaran jenjang ini.');
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $allowedGradeIds = $user->accessibleGradeIds();

        $q = StudentRegistration::query()->with(['grade', 'academicYear', 'campus']);

        if (!$user->isSuperAdmin()) {
            $q->whereIn('grade_id', $allowedGradeIds);
        }

        if ($v = $request->query('academic_year_id')) $q->where('academic_year_id', $v);
        if ($v = $request->query('grade_id')) {
            if ($user->isSuperAdmin() || in_array((int)$v, $allowedGradeIds)) {
                $q->where('grade_id', $v);
            }
        }
        if ($v = $request->query('campus_id')) $q->where('campus_id', $v);
        if ($v = $request->query('status')) $q->where('status', $v);
        if ($v = $request->query('administrative_status')) $q->where('administrative_status', $v);
        if ($v = $request->query('observation_eligibility')) $q->where('observation_eligibility', $v);
        if ($v = $request->query('search')) {
            $q->where(function ($qq) use ($v) {
                $qq->where('registration_number', 'like', "%{$v}%")
                    ->orWhere('full_name', 'like', "%{$v}%")
                    ->orWhere('nisn', 'like', "%{$v}%")
                    ->orWhere('father_name', 'like', "%{$v}%")
                    ->orWhere('mother_name', 'like', "%{$v}%")
                    ->orWhere('phone', 'like', "%{$v}%");
            });
        }

        $registrations = $q->latest('id')->paginate(15)->withQueryString();
        $filters = [
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'grades' => $user->accessibleGrades(onlyActive: false),
            'campuses' => Campus::orderBy('name')->get(),
            'statuses' => ['Terdaftar', 'Menunggu Validasi', 'Lolos Administrasi', 'Tidak Lolos Administrasi', 'Layak Observasi', 'Dipanggil Observasi', 'Observasi Selesai', 'Lulus Observasi', 'Tidak Lulus Observasi', 'Diterima', 'Ditolak'],
        ];

        return view('admin.registrations.index', compact('registrations', 'filters'));
    }

    public function create()
    {
        $user = auth()->user();
        return view('admin.registrations.create', [
            'grades' => $user->accessibleGrades(onlyActive: true)->load('ageRule'),
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'campuses' => Campus::where('is_active', true)->get(),
            'categories' => StudentCategory::where('is_active', true)->get(),
        ]);
    }

    public function store(StoreRegistrationRequest $request)
    {
        $user = auth()->user();
        $gradeId = (int)$request->input('grade_id');
        if (!$user->isSuperAdmin() && !in_array($gradeId, $user->accessibleGradeIds())) {
            return back()->withErrors(['grade_id' => 'Anda tidak memiliki hak akses untuk mendaftarkan jenjang ini.'])->withInput();
        }

        try {
            $result = $this->registrationService->register($request->validated(), auth()->id());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['age' => $e->getMessage()])->withInput();
        }
        $reg = $result['registration'];
        AuditLogger::log("Admin membuat pendaftaran {$reg->registration_number}", $reg);

        if ($result['status'] === 'duplicate') {
            return redirect()->route('admin.registrations.show', $reg->id)->with('info', 'Data sudah ada — menampilkan data existing.');
        }
        return redirect()->route('admin.registrations.show', $reg->id)->with('success', 'Pendaftaran berhasil ditambahkan.');
    }

    public function show(StudentRegistration $registration)
    {
        $this->checkGradeAccess($registration);
        $registration->load(['academicYear', 'grade.ageRule', 'campus', 'category', 'histories.changer', 'notes.admin']);
        return view('admin.registrations.show', ['reg' => $registration]);
    }

    public function validateAdmin(Request $request, StudentRegistration $registration)
    {
        $this->checkGradeAccess($registration);
        $data = $request->validate([
            'administrative_status' => ['required', 'in:Menunggu Validasi,Lolos Administrasi,Tidak Lolos Administrasi'],
            'observation_eligibility' => ['required', 'in:Belum Ditentukan,Layak Observasi,Tidak Layak Observasi'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldStatus = $registration->status;
        $registration->administrative_status = $data['administrative_status'];
        $registration->observation_eligibility = $data['observation_eligibility'];

        // Derive main status
        if ($data['administrative_status'] === 'Tidak Lolos Administrasi') {
            $registration->status = 'Tidak Lolos Administrasi';
        } elseif ($data['observation_eligibility'] === 'Tidak Layak Observasi') {
            $registration->status = 'Tidak Layak Observasi';
        } elseif ($data['observation_eligibility'] === 'Layak Observasi') {
            $registration->status = 'Layak Observasi';
        } elseif ($data['administrative_status'] === 'Lolos Administrasi') {
            $registration->status = 'Lolos Administrasi';
        } else {
            $registration->status = 'Menunggu Validasi';
        }
        $registration->save();

        if ($oldStatus !== $registration->status) {
            RegistrationStatusHistory::create([
                'registration_id' => $registration->id,
                'old_status' => $oldStatus,
                'new_status' => $registration->status,
                'changed_by' => auth()->id(),
                'note' => $data['note'] ?? null,
                'created_at' => now(),
            ]);
        }

        if (!empty($data['note'])) {
            RegistrationNote::create([
                'registration_id' => $registration->id,
                'admin_id' => auth()->id(),
                'note' => $data['note'],
                'note_type' => 'Validasi Administrasi',
            ]);
        }

        AuditLogger::log("Update validasi {$registration->registration_number} → {$registration->status}", $registration);
        return back()->with('success', 'Validasi berhasil disimpan.');
    }

    public function observation(Request $request, StudentRegistration $registration)
    {
        $this->checkGradeAccess($registration);
        $data = $request->validate([
            'observation_date' => ['required', 'date'],
            'observation_time' => ['required'],
            'observation_location' => ['required', 'string', 'max:191'],
            'observation_room' => ['nullable', 'string', 'max:100'],
            'observation_notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $oldStatus = $registration->status;
        $registration->fill($data);
        $registration->status = 'Dipanggil Observasi';
        $registration->save();

        RegistrationStatusHistory::create([
            'registration_id' => $registration->id,
            'old_status' => $oldStatus,
            'new_status' => 'Dipanggil Observasi',
            'changed_by' => auth()->id(),
            'note' => 'Jadwal observasi ditetapkan',
            'created_at' => now(),
        ]);
        AuditLogger::log("Jadwal observasi {$registration->registration_number}", $registration, $data);

        // Auto WhatsApp notification
        $wa = app(\App\Services\WhatsappNotificationService::class)
            ->notifyObservationScheduled($registration, auth()->id());

        return back()->with('success', 'Jadwal observasi tersimpan.')->with('wa', $wa);
    }

    public function observationResult(Request $request, StudentRegistration $registration)
    {
        $this->checkGradeAccess($registration);
        $data = $request->validate([
            'observation_result' => ['required', 'in:Lulus,Tidak Lulus'],
            'decision' => ['required', 'in:Belum,Diterima,Ditolak'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $oldStatus = $registration->status;
        $registration->observation_result = $data['observation_result'];

        if ($data['observation_result'] === 'Lulus') {
            $registration->status = $data['decision'] === 'Diterima' ? 'Diterima'
                : ($data['decision'] === 'Ditolak' ? 'Ditolak' : 'Lulus Observasi');
        } else {
            $registration->status = $data['decision'] === 'Ditolak' ? 'Ditolak' : 'Tidak Lulus Observasi';
        }
        $registration->save();

        RegistrationStatusHistory::create([
            'registration_id' => $registration->id,
            'old_status' => $oldStatus,
            'new_status' => $registration->status,
            'changed_by' => auth()->id(),
            'note' => $data['note'] ?? null,
            'created_at' => now(),
        ]);
        if (!empty($data['note'])) {
            RegistrationNote::create([
                'registration_id' => $registration->id,
                'admin_id' => auth()->id(),
                'note' => $data['note'],
                'note_type' => 'Observasi',
            ]);
        }
        AuditLogger::log("Hasil observasi {$registration->registration_number} → {$registration->status}", $registration);
        return back()->with('success', 'Hasil observasi disimpan.');
    }

    public function destroy(StudentRegistration $registration)
    {
        $this->checkGradeAccess($registration);
        AuditLogger::log("Hapus pendaftaran {$registration->registration_number}", $registration);
        $registration->delete();
        return redirect()->route('admin.registrations.index')->with('success', 'Data dihapus.');
    }

    public function importForm()
    {
        return view('admin.registrations.import');
    }

    public function importStore(Request $request, \App\Services\BatchRegistrationImporter $importer)
    {
        $request->validate(['csv' => ['required', 'file', 'mimes:csv,txt', 'max:5120']]);
        $user = auth()->user();
        $allowed = $user->isSuperAdmin() ? null : $user->accessibleGradeIds();
        $result = $importer->import($request->file('csv'), auth()->id(), $allowed);
        AuditLogger::log("Import batch pendaftaran: {$result['success']} sukses, {$result['failed']} gagal", null, ['result' => $result]);
        return back()->with('import_result', $result);
    }

    public function importTemplate(\App\Services\BatchRegistrationImporter $importer)
    {
        $headers = $importer->csvHeaderTemplate();
        $sample = [
            '2026/2027', 'TK-A', 'Al-Azhar Cairo Palembang - Kampus Utama', 'Siswa Baru',
            'Nur Aisyah', 'Aisyah', 'P', '', '', 'Palembang', '2022-05-10', 'Anak Kandung', '1',
            'Jl. Merdeka No. 12', '081234567890', '', 'Budi Santoso', 'Siti Aminah', 'Karto Wiryo',
            'Wiraswasta', 'IRT', '081200000001', '081200000002',
        ];
        return response()->streamDownload(function () use ($headers, $sample) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            fputcsv($out, $sample);
            fclose($out);
        }, 'template_pormin_batch.csv', ['Content-Type' => 'text/csv']);
    }

    public function resendWhatsapp(StudentRegistration $registration, \App\Services\WhatsappNotificationService $wa)
    {
        $this->checkGradeAccess($registration);
        abort_unless((bool) $registration->observation_date, 400, 'Jadwal observasi belum ditentukan.');
        $result = $wa->notifyObservationScheduled($registration, auth()->id());
        return back()->with('wa', $result)->with('success', 'Notifikasi WA diproses.');
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $allowedGradeIds = $user->accessibleGradeIds();

        $q = StudentRegistration::query()->with(['grade', 'academicYear', 'campus']);

        if (!$user->isSuperAdmin()) {
            $q->whereIn('grade_id', $allowedGradeIds);
        }
        
        // Tangkap nilai filter
        $academicYearId = $request->query('academic_year_id');
        $gradeId = $request->query('grade_id');
        $status = $request->query('status');
        $campusId = $request->query('campus_id');

        if ($v = $academicYearId) $q->where('academic_year_id', $v);
        if ($v = $gradeId) {
            if ($user->isSuperAdmin() || in_array((int)$v, $allowedGradeIds)) {
                $q->where('grade_id', $v);
            }
        }
        if ($v = $status) $q->where('status', $v);
        if ($v = $campusId) $q->where('campus_id', $v);

        $rows = $q->orderBy('registration_number')->get();

        // 1. Ambil nama teks dari relasi untuk menyusun nama file
        $tahunName  = $academicYearId ? AcademicYear::find($academicYearId)?->name : 'semua-tahun';
        $gradeName  = $gradeId ? Grade::find($gradeId)?->code : 'semua-grade';
        $campusName = $campusId ? Campus::find($campusId)?->name : 'semua-kampus';

        // 2. Bersihkan karakter string agar aman jadi nama file (Contoh: "2027/2028" jadi "2027-2028")
        $tahunName  = Str::slug($tahunName ?? 'semua-tahun');
        $gradeName  = Str::slug($gradeName ?? 'semua-grade');
        $campusName = Str::slug($campusName ?? 'semua-kampus');

        // 3. Susun format nama file: pormin_tahunajaran_grade_kampus_tanggal.csv
        $filename = "pormin_{$tahunName}_{$gradeName}_{$campusName}_" . now()->format('YmdHis') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No', 'No. Pendaftaran', 'Nama Anak', 'Grade', 'Tahun', 'Kampus', 'Tgl Lahir', 'Nama Ayah', 'Nama Ibu', 'HP', 'Status']);
            foreach ($rows as $i => $r) {
                fputcsv($out, [
                    $i + 1,
                    $r->registration_number, $r->full_name,
                    $r->grade->code ?? '', $r->academicYear->name ?? '',
                    $r->campus->name ?? '', $r->birth_date?->format('d-m-Y'),
                    $r->father_name, $r->mother_name, $r->phone, $r->status,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
