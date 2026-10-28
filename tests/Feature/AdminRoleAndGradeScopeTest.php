<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\Grade;
use App\Models\StudentCategory;
use App\Models\StudentRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoleAndGradeScopeTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminTk;
    protected User $adminSd;
    protected Grade $gradePg;
    protected Grade $gradeTkA;
    protected Grade $gradeTkB;
    protected Grade $gradeSd;
    protected Grade $gradeSmp;
    protected Grade $gradeSma;
    protected AcademicYear $year;
    protected Campus $campus;
    protected StudentCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->year = AcademicYear::create(['name' => '2026/2027', 'start_year' => 2026, 'end_year' => 2027, 'is_active' => true]);
        $this->campus = Campus::create(['name' => 'Kampus Utama', 'address' => 'Palembang', 'is_active' => true]);
        $this->category = StudentCategory::create(['name' => 'Siswa Baru', 'is_active' => true]);

        $this->gradePg = Grade::create(['code' => 'PG', 'name' => 'Playgroup', 'sort_order' => 1, 'is_active' => true]);
        $this->gradeTkA = Grade::create(['code' => 'TK-A', 'name' => 'TK A', 'sort_order' => 2, 'is_active' => true]);
        $this->gradeTkB = Grade::create(['code' => 'TK-B', 'name' => 'TK B', 'sort_order' => 3, 'is_active' => true]);
        $this->gradeSd = Grade::create(['code' => 'SD', 'name' => 'Sekolah Dasar', 'sort_order' => 4, 'is_active' => true]);
        $this->gradeSmp = Grade::create(['code' => 'SMP', 'name' => 'SMP', 'sort_order' => 5, 'is_active' => true]);
        $this->gradeSma = Grade::create(['code' => 'SMA', 'name' => 'SMA', 'sort_order' => 6, 'is_active' => true]);

        // Super Admin
        $this->superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@alazharcairoplg.sch.id',
            'password' => bcrypt('Admin@12345'),
            'role' => 'super_admin',
        ]);

        // Admin TK (PG, TK-A, TK-B)
        $this->adminTk = User::create([
            'name' => 'Admin PG - TK',
            'email' => 'admin.tk@alazharcairoplg.sch.id',
            'password' => bcrypt('Admin@12345'),
            'role' => 'grade_admin',
        ]);
        $this->adminTk->grades()->sync([$this->gradePg->id, $this->gradeTkA->id, $this->gradeTkB->id]);

        // Admin SD
        $this->adminSd = User::create([
            'name' => 'Admin SD',
            'email' => 'admin.sd@alazharcairoplg.sch.id',
            'password' => bcrypt('Admin@12345'),
            'role' => 'grade_admin',
        ]);
        $this->adminSd->grades()->sync([$this->gradeSd->id]);
    }

    public function test_super_admin_can_access_user_management(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Akun Admin');
    }

    public function test_grade_admin_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->adminTk)->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    public function test_grade_admin_cannot_access_master_data(): void
    {
        $response = $this->actingAs($this->adminTk)->get(route('admin.grades'));
        $response->assertStatus(403);
    }

    public function test_grade_admin_dashboard_only_contains_assigned_grades(): void
    {
        $response = $this->actingAs($this->adminTk)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $perGrade = $response->viewData('perGrade');
        $this->assertCount(3, $perGrade);
        $codes = collect($perGrade)->pluck('code')->toArray();
        $this->assertEquals(['PG', 'TK-A', 'TK-B'], $codes);
    }

    private function createRegistration(Grade $grade, string $number, string $name): StudentRegistration
    {
        return StudentRegistration::create([
            'registration_number' => $number,
            'academic_year_id' => $this->year->id,
            'grade_id' => $grade->id,
            'campus_id' => $this->campus->id,
            'student_category_id' => $this->category->id,
            'full_name' => $name,
            'normalized_name' => strtoupper($name),
            'nickname' => $name,
            'gender' => 'L',
            'birth_place' => 'Palembang',
            'birth_date' => '2020-01-01',
            'family_status' => 'Anak Kandung',
            'child_order' => 1,
            'address' => 'Jl. Sudirman',
            'phone' => '08123456789',
            'father_name' => 'Ayah',
            'mother_name' => 'Ibu',
            'grandfather_name' => 'Kakek',
            'status' => 'Terdaftar',
            'administrative_status' => 'Menunggu Validasi',
            'observation_eligibility' => 'Belum Ditentukan',
            'age_reference_date' => '2026-07-01',
            'age_years' => 6,
            'age_months' => 6,
            'age_days' => 0,
            'age_validation_status' => 'Memenuhi',
            'verification_token' => \Illuminate\Support\Str::random(32),
        ]);
    }

    public function test_grade_admin_only_sees_registrations_of_their_assigned_grade(): void
    {
        $regTk = $this->createRegistration($this->gradeTkA, 'REG-TK-001', 'Anak TK');
        $regSd = $this->createRegistration($this->gradeSd, 'REG-SD-001', 'Anak SD');

        // Admin TK requests registration list
        $response = $this->actingAs($this->adminTk)->get(route('admin.registrations.index'));
        $response->assertStatus(200);
        $response->assertSee('REG-TK-001');
        $response->assertDontSee('REG-SD-001');

        // Admin TK can view their own grade registration
        $responseDetail = $this->actingAs($this->adminTk)->get(route('admin.registrations.show', $regTk));
        $responseDetail->assertStatus(200);

        // Admin TK CANNOT view SD registration (403)
        $responseForbidden = $this->actingAs($this->adminTk)->get(route('admin.registrations.show', $regSd));
        $responseForbidden->assertStatus(403);
    }

    public function test_super_admin_can_create_new_admin_with_grade(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.users.store'), [
            'name' => 'Admin Baru SMP',
            'email' => 'admin.baru@alazharcairoplg.sch.id',
            'password' => 'secret123',
            'role' => 'grade_admin',
            'grade_ids' => [$this->gradeSmp->id],
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'admin.baru@alazharcairoplg.sch.id',
            'role' => 'grade_admin',
        ]);

        $newUser = User::where('email', 'admin.baru@alazharcairoplg.sch.id')->first();
        $this->assertTrue($newUser->grades->contains($this->gradeSmp));
    }

    public function test_super_admin_can_update_and_delete_user(): void
    {
        // Update
        $response = $this->actingAs($this->superAdmin)->put(route('admin.users.update', $this->adminSd), [
            'name' => 'Admin SD Updated',
            'email' => 'admin.sd@alazharcairoplg.sch.id',
            'role' => 'grade_admin',
            'grade_ids' => [$this->gradeSd->id, $this->gradePg->id],
        ]);
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['name' => 'Admin SD Updated']);

        // Delete
        $responseDelete = $this->actingAs($this->superAdmin)->delete(route('admin.users.destroy', $this->adminSd));
        $responseDelete->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $this->adminSd->id]);
    }

    public function test_all_admins_can_view_and_update_password(): void
    {
        // 1. Grade Admin (Admin TK) can view password change form
        $response = $this->actingAs($this->adminTk)->get(route('admin.password.edit'));
        $response->assertStatus(200);
        $response->assertSee('Perbarui Kata Sandi');

        // 2. Grade Admin updates their password
        $updateResponse = $this->actingAs($this->adminTk)->put(route('admin.password.update'), [
            'current_password' => 'Admin@12345',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $updateResponse->assertSessionHas('success');

        // Verify hash changed and can authenticate with new password
        $this->adminTk->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $this->adminTk->password));

        // 3. Super Admin can also view and update password
        $responseSuper = $this->actingAs($this->superAdmin)->get(route('admin.password.edit'));
        $responseSuper->assertStatus(200);

        $updateResponseSuper = $this->actingAs($this->superAdmin)->put(route('admin.password.update'), [
            'current_password' => 'Admin@12345',
            'password' => 'supersecret999',
            'password_confirmation' => 'supersecret999',
        ]);
        $updateResponseSuper->assertSessionHas('success');

        $this->superAdmin->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('supersecret999', $this->superAdmin->password));
    }

    public function test_password_update_validates_current_and_confirmation(): void
    {
        // Wrong current password
        $responseWrongCurrent = $this->actingAs($this->adminTk)->put(route('admin.password.update'), [
            'current_password' => 'wrongcurrentpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $responseWrongCurrent->assertSessionHasErrors('current_password');

        // Mismatched confirmation
        $responseMismatch = $this->actingAs($this->adminTk)->put(route('admin.password.update'), [
            'current_password' => 'Admin@12345',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);
        $responseMismatch->assertSessionHasErrors('password');
    }

    public function test_single_grade_admin_does_not_see_grade_filter(): void
    {
        // Admin SD (only has 1 grade: SD) -> Grade filter is hidden
        $responseSd = $this->actingAs($this->adminSd)->get(route('admin.registrations.index'));
        $responseSd->assertStatus(200);
        $responseSd->assertDontSee('Semua Grade');

        // Super Admin (has all 6 grades) -> Grade filter is visible
        $responseSuper = $this->actingAs($this->superAdmin)->get(route('admin.registrations.index'));
        $responseSuper->assertStatus(200);
        $responseSuper->assertSee('Semua Grade');
    }

    public function test_super_admin_can_edit_and_delete_campus_with_safeguard(): void
    {
        // 1. Create a campus without registrations
        $newCampus = Campus::create(['name' => 'Kampus Baru Tes', 'address' => 'Jl. Merdeka', 'is_active' => true]);

        // Edit campus
        $responseEdit = $this->actingAs($this->superAdmin)->post(route('admin.campuses.save'), [
            'id' => $newCampus->id,
            'name' => 'Kampus Baru Tes Renamed',
            'address' => 'Jl. Merdeka No. 10',
            'is_active' => 1,
        ]);
        $responseEdit->assertSessionHas('success');
        $this->assertDatabaseHas('campuses', ['name' => 'Kampus Baru Tes Renamed']);

        // Delete campus successfully because 0 registrations
        $responseDelete = $this->actingAs($this->superAdmin)->delete(route('admin.campuses.destroy', $newCampus));
        $responseDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('campuses', ['id' => $newCampus->id]);

        // 2. Safeguard: cannot delete campus with registrations
        $usedCampus = Campus::create(['name' => 'Kampus Terpakai', 'address' => 'Jl. Utama', 'is_active' => true]);
        $this->createRegistration($this->gradeSd, 'REG-CAMPUS-01', 'Anak Kampus');
        // Attach to this campus
        StudentRegistration::where('registration_number', 'REG-CAMPUS-01')->update(['campus_id' => $usedCampus->id]);

        $responseDeleteBlocked = $this->actingAs($this->superAdmin)->delete(route('admin.campuses.destroy', $usedCampus));
        $responseDeleteBlocked->assertSessionHas('error');
        $this->assertDatabaseHas('campuses', ['id' => $usedCampus->id]);
    }

    public function test_custom_login_route_accessible_and_old_admin_login_hidden(): void
    {
        $res = $this->get('/alazcaplg/login');
        $res->assertStatus(200);

        $resOld = $this->get('/admin/login');
        $resOld->assertRedirect(route('landing'));
    }

    public function test_super_admin_can_manage_landing_sliders_and_grade_admin_cannot(): void
    {
        // 1. Grade Admin is forbidden from saving sliders
        $resForbidden = $this->actingAs($this->adminSd)->post(route('admin.sliders.save'), [
            'caption' => 'Test Slider',
        ]);
        $resForbidden->assertStatus(403);

        // 2. Super Admin can save new slider
        $file = \Illuminate\Http\UploadedFile::fake()->image('new-banner.jpg', 800, 500);
        $resAdd = $this->actingAs($this->superAdmin)->post(route('admin.sliders.save'), [
            'image' => $file,
            'caption' => 'Gedung Sekolah Keren',
            'sort_order' => 10,
            'is_active' => 1,
        ]);
        $resAdd->assertSessionHas('success');
        $this->assertDatabaseHas('landing_sliders', [
            'caption' => 'Gedung Sekolah Keren',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $slider = \App\Models\LandingSlider::where('caption', 'Gedung Sekolah Keren')->first();

        // 3. Super Admin can update slider
        $resUpdate = $this->actingAs($this->superAdmin)->post(route('admin.sliders.save'), [
            'id' => $slider->id,
            'caption' => 'Gedung Sekolah Keren Updated',
            'sort_order' => 12,
            'is_active' => 1,
        ]);
        $resUpdate->assertSessionHas('success');
        $this->assertDatabaseHas('landing_sliders', [
            'id' => $slider->id,
            'caption' => 'Gedung Sekolah Keren Updated',
        ]);

        // 4. Landing page renders the caption
        $resLanding = $this->get(route('landing'));
        $resLanding->assertStatus(200);
        $resLanding->assertSee('Gedung Sekolah Keren Updated');

        // 5. Super Admin can delete slider
        $resDelete = $this->actingAs($this->superAdmin)->delete(route('admin.sliders.destroy', $slider));
        $resDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('landing_sliders', ['id' => $slider->id]);
    }

    public function test_footer_renders_multiline_address_cleanly(): void
    {
        \App\Models\InformationSetting::set('contact_address', "Jl. Sudirman KM 3 <br>\nJl. Sukarela KM 7");
        $res = $this->get(route('landing'));
        $res->assertStatus(200);
        $res->assertSee('Jl. Sudirman KM 3');
        $res->assertSee('Jl. Sukarela KM 7');
        $res->assertDontSee('&lt;br&gt;');
    }
}


