<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthRedirectService;
use App\Models\User;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Reservasi;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthRedirectServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthRedirectService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AuthRedirectService();
    }

    public function test_redirects_to_profil_complete_if_phone_incomplete(): void
    {
        $user = User::create([
            'nama' => 'User Temp',
            'email' => 'temp@example.com',
            'no_hp' => 'temp_123456789',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
        ]);

        $destination = $this->service->resolvePostAuthDestination($user);

        $this->assertEquals('route', $destination['type']);
        $this->assertEquals('profil.complete', $destination['target']);
    }

    public function test_redirects_active_tenant_to_penyewa_dashboard(): void
    {
        $user = User::create([
            'nama' => 'User Tenant',
            'email' => 'tenant@example.com',
            'no_hp' => '08123456789',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
        ]);
        $kamar = Kamar::create(['nomor_kamar' => '101', 'lantai' => 1, 'tipe' => 'standar', 'luas_m2' => 12.0, 'harga_bulan' => 1000000, 'status' => 'terisi']);
        Penyewa::create(['user_id' => $user->id, 'kamar_id' => $kamar->id, 'harga_sewa' => 1000000, 'status' => 'aktif', 'nik' => '111', 'tanggal_masuk' => now()->toDateString(), 'tanggal_billing' => 1, 'tipe_sewa' => 'bulanan', 'durasi' => 1, 'nama_wali' => 'Wali Tenant', 'no_wali' => '081234567890']);

        $destination = $this->service->resolvePostAuthDestination($user);

        $this->assertEquals('route', $destination['type']);
        $this->assertEquals('penyewa.dashboard', $destination['target']);
    }

    public function test_redirects_user_with_pending_reservasi_to_reservasi_dashboard(): void
    {
        $user = User::create([
            'nama' => 'User Reservasi',
            'email' => 'res@example.com',
            'no_hp' => '08123456789',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
        ]);
        $kamar = Kamar::create(['nomor_kamar' => '102', 'lantai' => 1, 'tipe' => 'standar', 'luas_m2' => 12.0, 'harga_bulan' => 1000000, 'status' => 'tersedia']);
        Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'order_id' => 'RSV-TEST-1',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
            'total_harga' => 1000000,
            'nominal_dp' => 300000,
            'nominal_sisa' => 700000,
            'status' => 'pending',
        ]);

        $destination = $this->service->resolvePostAuthDestination($user);

        $this->assertEquals('route', $destination['type']);
        $this->assertEquals('penyewa.reservasi.dashboard', $destination['target']);
    }

    public function test_fallback_redirects_to_landing_index(): void
    {
        $user = User::create([
            'nama' => 'User Guest',
            'email' => 'guest@example.com',
            'no_hp' => '08123456789',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
        ]);

        $destination = $this->service->resolvePostAuthDestination($user);

        $this->assertEquals('route', $destination['type']);
        $this->assertEquals('landing.index', $destination['target']);
    }
}
