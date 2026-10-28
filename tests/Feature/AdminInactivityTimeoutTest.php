<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInactivityTimeoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        AcademicYear::create([
            'name' => '2026/2027',
            'start_year' => 2026,
            'end_year' => 2027,
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin.test@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'super_admin',
        ]);
    }

    public function test_admin_login_sets_last_activity_timestamp(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin.test@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);
        $this->assertNotNull(session('admin_last_activity'));
    }

    public function test_active_admin_can_access_dashboard_and_updates_activity_timestamp(): void
    {
        $now = now()->timestamp;

        $response = $this->actingAs($this->admin)
            ->withSession(['admin_last_activity' => $now - 600]) // 10 menit yang lalu (masih aktif)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($this->admin);
        $this->assertGreaterThanOrEqual($now, session('admin_last_activity'));
    }

    public function test_inactive_admin_after_30_minutes_is_logged_out_and_redirected(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['admin_last_activity' => now()->timestamp - 1805]) // > 30 menit (1805 detik)
            ->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('warning');
        $this->assertGuest();
    }

    public function test_admin_keep_alive_endpoint_updates_last_activity(): void
    {
        $oldTimestamp = now()->timestamp - 300;

        $response = $this->actingAs($this->admin)
            ->withSession(['admin_last_activity' => $oldTimestamp])
            ->postJson(route('admin.keep-alive'));

        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);

        $this->assertGreaterThan($oldTimestamp, session('admin_last_activity'));
    }
}
