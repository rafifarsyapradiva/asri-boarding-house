<?php

namespace Tests\Feature\Auth;

use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

use Tests\TestCase;

class PenyewaInitialLoginTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567891',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $this->kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);
    }

    public function test_offline_tenant_can_login_with_08_phone_format_password(): void
    {
        // 1. Admin registers offline tenant with no_hp = 085155356177
        $response = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), [
                'nama' => 'Rafif Test',
                'email' => 'rafif.test@example.com',
                'no_hp' => '085155356177',
                'nik' => '1234567890123456',
                'kamar_id' => $this->kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Rafif',
                'no_wali' => '085155356178',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
            ]);

        $response->assertRedirect(route('admin.penyewa.index'));

        // Logout admin
        Auth::logout();

        // 2. Tenant logs in using email and raw "08..." password format
        $loginResponse = $this->post('/penyewa/login', [
            'login' => 'rafif.test@example.com',
            'password' => '085155356177',
        ]);

        $loginResponse->assertRedirect(route('penyewa.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'rafif.test@example.com')->first());
    }

    public function test_offline_tenant_can_login_with_62_phone_format_password(): void
    {
        // 1. Admin registers offline tenant
        $this->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), [
                'nama' => 'Rafif Test 2',
                'email' => 'rafif2.test@example.com',
                'no_hp' => '085155356177',
                'nik' => '1234567890123457',
                'kamar_id' => $this->kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Rafif',
                'no_wali' => '085155356178',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
            ]);

        Auth::logout();

        // 2. Tenant logs in using "62..." password format
        $loginResponse = $this->post('/penyewa/login', [
            'login' => 'rafif2.test@example.com',
            'password' => '6285155356177',
        ]);

        $loginResponse->assertRedirect(route('penyewa.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'rafif2.test@example.com')->first());
    }

    public function test_initial_password_fallback_disabled_after_password_change(): void
    {
        $tenantUser = User::create([
            'nama' => 'Tenant Changed',
            'email' => 'changed@example.com',
            'no_hp' => '6285155356177',
            'password' => bcrypt('NewSecurePassword123!'),
            'role' => 'penyewa',
            'is_active' => 1,
            'require_password_change' => false, // Password already changed
        ]);

        Penyewa::create([
            'user_id' => $tenantUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123458',
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Wali',
            'no_wali' => '628123456789',
            'deposit' => 1000000,
            'harga_sewa' => 1000000,
            'status' => 'aktif',
        ]);

        // Attempt login with old default 08... format -> Should fail
        $failedResponse = $this->post('/penyewa/login', [
            'login' => 'changed@example.com',
            'password' => '085155356177',
        ]);

        $failedResponse->assertSessionHasErrors('login');
        $this->assertGuest();

        // Attempt login with new password -> Should succeed
        $successResponse = $this->post('/penyewa/login', [
            'login' => 'changed@example.com',
            'password' => 'NewSecurePassword123!',
        ]);

        $successResponse->assertRedirect(route('penyewa.dashboard'));
        $this->assertAuthenticatedAs($tenantUser);
    }
}
