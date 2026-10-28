<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Grade;
use App\Models\GradeAgeRule;
use App\Models\InformationSetting;
use App\Models\LandingSlider;
use App\Models\StudentCategory;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function years()
    {
        return view('admin.master.years', [
            'items' => AcademicYear::withCount('registrations')->orderByDesc('start_year')->get()
        ]);
    }

    public function saveYear(Request $req)
    {
        $data = $req->validate([
            'id' => ['nullable', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:20'],
            'start_year' => ['required', 'integer', 'min:2020', 'max:2099'],
            'end_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        if (!empty($data['is_active'])) AcademicYear::query()->update(['is_active' => false]);
        AcademicYear::updateOrCreate(['id' => $data['id'] ?? 0], [
            'name' => $data['name'],
            'start_year' => $data['start_year'],
            'end_year' => $data['end_year'],
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);
        return back()->with('success', 'Tahun ajaran berhasil disimpan.');
    }

    public function destroyYear(AcademicYear $year)
    {
        if ($year->registrations()->exists()) {
            return back()->with('error', "Tahun ajaran '{$year->name}' tidak dapat dihapus karena masih digunakan oleh {$year->registrations()->count()} data pendaftaran. Anda dapat menonaktifkannya.");
        }
        $year->delete();
        return back()->with('success', "Tahun ajaran '{$year->name}' berhasil dihapus.");
    }

    public function grades()
    {
        return view('admin.master.grades', [
            'items' => Grade::with('ageRule')->withCount('registrations')->orderBy('sort_order')->get()
        ]);
    }

    public function saveGrade(Request $req)
    {
        $data = $req->validate([
            'id' => ['nullable', 'exists:grades,id'],
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        Grade::updateOrCreate(['id' => $data['id'] ?? 0], [
            'code' => strtoupper(trim($data['code'])),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);
        return back()->with('success', 'Grade berhasil disimpan.');
    }

    public function destroyGrade(Grade $grade)
    {
        if ($grade->registrations()->exists()) {
            return back()->with('error', "Grade '{$grade->name}' ({$grade->code}) tidak dapat dihapus karena memiliki {$grade->registrations()->count()} data pendaftaran. Silakan nonaktifkan grade ini.");
        }
        $grade->users()->detach();
        if ($grade->ageRule) {
            $grade->ageRule->delete();
        }
        $grade->delete();
        return back()->with('success', "Grade '{$grade->name}' berhasil dihapus.");
    }

    public function ageRule(Grade $grade)
    {
        $rule = $grade->ageRule ?? new GradeAgeRule(['reference_month' => 7, 'reference_day' => 1]);
        return view('admin.master.age_rule', compact('grade', 'rule'));
    }

    public function saveAgeRule(Request $req, Grade $grade)
    {
        $data = $req->validate([
            'minimum_age_years' => ['nullable', 'integer', 'min:0', 'max:30'],
            'minimum_age_months' => ['nullable', 'integer', 'min:0', 'max:12'],
            'minimum_age_days' => ['nullable', 'integer', 'min:0', 'max:31'],
            'maximum_age_years' => ['nullable', 'integer', 'min:0', 'max:30'],
            'maximum_age_months' => ['nullable', 'integer', 'min:0', 'max:12'],
            'maximum_age_days' => ['nullable', 'integer', 'min:0', 'max:31'],
            'reference_month' => ['required', 'integer', 'min:1', 'max:12'],
            'reference_day' => ['required', 'integer', 'min:1', 'max:31'],
        ]);
        GradeAgeRule::updateOrCreate(['grade_id' => $grade->id], $data + ['is_active' => true]);
        return redirect()->route('admin.grades')->with('success', 'Aturan usia tersimpan.');
    }

    public function campuses()
    {
        return view('admin.master.campuses', [
            'items' => Campus::withCount('registrations')->orderBy('name')->get()
        ]);
    }

    public function saveCampus(Request $req)
    {
        $data = $req->validate([
            'id' => ['nullable', 'exists:campuses,id'],
            'name' => ['required', 'string', 'max:191'],
            'address' => ['required', 'string', 'max:191'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        Campus::updateOrCreate(['id' => $data['id'] ?? 0], [
            'name' => $data['name'],
            'address' => $data['address'],
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);
        return back()->with('success', 'Data kampus berhasil disimpan.');
    }

    public function destroyCampus(Campus $campus)
    {
        if ($campus->registrations()->exists()) {
            return back()->with('error', "Kampus '{$campus->name}' tidak dapat dihapus karena masih digunakan oleh {$campus->registrations()->count()} data pendaftaran. Anda dapat menonaktifkannya.");
        }
        $campus->delete();
        return back()->with('success', "Kampus '{$campus->name}' berhasil dihapus.");
    }

    public function categories()
    {
        return view('admin.master.categories', [
            'items' => StudentCategory::withCount('registrations')->orderBy('name')->get()
        ]);
    }

    public function saveCategory(Request $req)
    {
        $data = $req->validate([
            'id' => ['nullable', 'exists:student_categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        StudentCategory::updateOrCreate(['id' => $data['id'] ?? 0], [
            'name' => $data['name'],
            'is_active' => (bool)($data['is_active'] ?? false),
        ]);
        return back()->with('success', 'Kategori siswa berhasil disimpan.');
    }

    public function destroyCategory(StudentCategory $category)
    {
        if ($category->registrations()->exists()) {
            return back()->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih digunakan oleh {$category->registrations()->count()} data pendaftaran. Anda dapat menonaktifkannya.");
        }
        $category->delete();
        return back()->with('success', "Kategori '{$category->name}' berhasil dihapus.");
    }

    public function information()
    {
        $settings = InformationSetting::pluck('value', 'key');
        $sliders = LandingSlider::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.master.information', compact('settings', 'sliders'));
    }
    public function saveInformation(Request $req)
    {
        $keys = ['landing_hero_title', 'landing_hero_subtitle', 'landing_hero_description', 'info_pendaftaran', 'info_persyaratan', 'info_alur', 'info_jadwal', 'info_faq', 'contact_phone', 'contact_email', 'contact_address', 'announcement_title', 'announcement_items', 'announcement_active', 'wa_country_code', 'wa_driver', 'wa_fonnte_token'];
        foreach ($keys as $k) {
            if ($req->has($k)) InformationSetting::set($k, (string) $req->input($k));
        }
        // Logo upload
        if ($req->hasFile('logo')) {
            $req->validate(['logo' => ['image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048']]);
            $file = $req->file('logo');
            $name = 'logo-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $name);
            InformationSetting::set('logo_path', 'uploads/' . $name);
        }
        if ($req->boolean('logo_remove')) {
            InformationSetting::set('logo_path', null);
        }
        return back()->with('success', 'Informasi landing tersimpan.');
    }

    public function saveSlider(Request $req)
    {
        $id = $req->input('id');
        $rules = [
            'caption' => ['nullable', 'string', 'max:150'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable'],
        ];

        if ($id) {
            $slider = LandingSlider::findOrFail($id);
            $rules['image'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'];
        } else {
            $slider = new LandingSlider();
            $rules['image'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'];
        }

        $req->validate($rules, [
            'image.required' => 'Foto slide wajib dipilih.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, webp.',
            'image.max' => 'Ukuran gambar maksimal 3 MB.',
            'caption.max' => 'Keterangan gambar maksimal 150 karakter.',
        ]);

        if ($req->hasFile('image')) {
            $file = $req->file('image');
            $filename = 'slider-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $targetDir = public_path('uploads/sliders');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $file->move($targetDir, $filename);

            // Remove old uploaded file if in uploads/sliders
            if ($slider->image_path && str_starts_with($slider->image_path, 'uploads/sliders/') && file_exists(public_path($slider->image_path))) {
                @unlink(public_path($slider->image_path));
            }

            $slider->image_path = 'uploads/sliders/' . $filename;
        }

        $slider->caption = $req->filled('caption') ? trim($req->input('caption')) : null;
        $slider->sort_order = (int) ($req->input('sort_order') ?? 0);
        $slider->is_active = $req->boolean('is_active');
        $slider->save();

        \App\Services\AuditLogger::log(
            $id ? "Mengubah foto slider landing page ID #{$slider->id}" : "Menambahkan foto slider baru ke landing page ID #{$slider->id}"
        );

        return back()->with('success', $id ? 'Foto slider berhasil diperbarui.' : 'Foto slider berhasil ditambahkan.');
    }

    public function destroySlider(LandingSlider $slider)
    {
        if ($slider->image_path && str_starts_with($slider->image_path, 'uploads/sliders/') && file_exists(public_path($slider->image_path))) {
            @unlink(public_path($slider->image_path));
        }
        $slider->delete();

        \App\Services\AuditLogger::log("Menghapus foto slider landing page ID #{$slider->id}");

        return back()->with('success', 'Foto slider berhasil dihapus.');
    }
}
