<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Tagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PenyewaDashboardAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $activeTenantUser;
    private User $inactiveTenantUser;
    private User $adminUser;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Room
        $this->kamar = Kamar::create([
            'nomor_kamar' => '201',
            'lantai' => 2,
            'tipe' => 'vip',
            'luas_m2' => 24.0,
            'harga_bulan' => 1400000,
            'status' => 'terisi'
        ]);

        // Active Tenant User
        $this->activeTenantUser = User::create([
            'nama' => 'Rafif Active Tenant',
            'email' => 'active.tenant@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        Penyewa::create([
            'user_id' => $this->activeTenantUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
            'no_wali' => '082219575575',
            'nama_wali' => 'Wali Active',
            'tipe_sewa' => 'bulanan',
            'durasi' => 12,
            'deposit' => 1400000,
        ]);

        // Inactive Tenant User
        $this->inactiveTenantUser = User::create([
            'nama' => 'Rafif Inactive Tenant',
            'email' => 'inactive.tenant@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        Penyewa::create([
            'user_id' => $this->inactiveTenantUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123457',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'nonaktif',
            'no_wali' => '082219575576',
            'nama_wali' => 'Wali Inactive',
            'tipe_sewa' => 'bulanan',
            'durasi' => 12,
            'deposit' => 1400000,
        ]);

        // Admin User
        $this->adminUser = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567893',
            'role' => 'admin',
            'is_active' => 1,
        ]);
    }

    /**
     * Test active tenant can access dashboard and see relevant details.
     */
    public function test_active_tenant_can_access_dashboard_and_see_details(): void
    {
        $response = $this->actingAs($this->activeTenantUser)
            ->get(route('penyewa.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Penyewa');
        $response->assertSee('Kamar 201');
        $response->assertSee('Lantai 2');
        $response->assertSee('vip');
        $response->assertSee('Rp 1.400.000');
    }

    /**
     * Test inactive tenant is redirected away from dashboard.
     */
    public function test_inactive_tenant_is_redirected_from_dashboard(): void
    {
        $response = $this->actingAs($this->inactiveTenantUser)
            ->get(route('penyewa.dashboard'));

        $response->assertRedirect(route('landing.index'));
        $response->assertSessionHas('error', 'Akses ditolak. Halaman ini khusus untuk penyewa aktif.');
    }

    /**
     * Test guest is redirected to login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('penyewa.dashboard'));
        $response->assertRedirect(route('penyewa.login'));
    }

    /**
     * Test admin cannot access tenant dashboard.
     */
    public function test_admin_cannot_access_tenant_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('penyewa.dashboard'));

        $response->assertStatus(403);
    }

    /**
     * Test dashboard renders correct chart variables without double counting gagal.
     */
    public function test_dashboard_renders_correct_chart_variables_without_double_counting(): void
    {
        $penyewa = $this->activeTenantUser->penyewa;

        // Bill 1: Lunas
        Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-AUDIT-LUNAS',
            'periode_bulan' => 1,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-01-01',
            'tanggal_jatuh_tempo' => '2026-01-10',
            'nominal_pokok' => 1400000,
            'nominal_total' => 1400000,
            'status' => 'lunas'
        ]);

        // Bill 2: Pending
        Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-AUDIT-PENDING',
            'periode_bulan' => 2,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-02-01',
            'tanggal_jatuh_tempo' => '2026-02-10',
            'nominal_pokok' => 1400000,
            'nominal_total' => 1400000,
            'status' => 'pending'
        ]);

        // Bill 3: Gagal (unpaid, should be under Belum Lunas, but NOT under Lainnya)
        Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-AUDIT-GAGAL',
            'periode_bulan' => 3,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-03-01',
            'tanggal_jatuh_tempo' => '2026-03-10',
            'nominal_pokok' => 1400000,
            'nominal_total' => 1400000,
            'status' => 'gagal'
        ]);

        // Bill 4: Kadaluarsa (should be under Lainnya, but NOT under Belum Lunas)
        Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-AUDIT-EXPIRED',
            'periode_bulan' => 4,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-04-01',
            'tanggal_jatuh_tempo' => '2026-04-10',
            'nominal_pokok' => 1400000,
            'nominal_total' => 1400000,
            'status' => 'kadaluarsa'
        ]);

        $response = $this->actingAs($this->activeTenantUser)
            ->get(route('penyewa.dashboard'));

        $response->assertStatus(200);

        // Verify variables passed to the view (disjoint groups)
        $response->assertViewHas('lunasCount', 1);
        $response->assertViewHas('tagihanBelumLunas', 1); // pending only (gagal excluded)
        
        $response->assertViewHas('tersedia', 1); // Lunas
        $response->assertViewHas('terisi', 1); // Belum Lunas (pending only)
        $response->assertViewHas('maintenance', 2); // Lainnya (kadaluarsa + gagal)
    }

    /**
     * Test tenant without reservations can access pending dashboard and see empty state.
     */
    public function test_tenant_without_reservasi_can_access_pending_dashboard_and_see_empty_state(): void
    {
        // Create user without reservations
        $newTenantUser = User::create([
            'nama' => 'Penyewa Baru Tanpa Booking',
            'email' => 'baru.tenant@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567894',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $response = $this->actingAs($newTenantUser)
            ->get(route('penyewa.reservasi.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pemesanan Kamar Belum Ditemukan');
        $response->assertSee('Cari');
        $response->assertSee('1. Registrasi Akun');
        $response->assertSee('2. Pilih');
        $response->assertSee('Booking Kamar');
        $response->assertSee('Kembali ke Beranda');
    }
}
