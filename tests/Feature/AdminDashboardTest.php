<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Tagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    private Kamar $kamar;
    private Penyewa $penyewa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin.dashboard@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $this->user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa.dashboard@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $this->kamar = Kamar::create([
            'nomor_kamar' => '103',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'terisi'
        ]);

        $this->penyewa = Penyewa::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Dummy',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // Create tagihan
        Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-DASHBOARD',
            'periode_bulan' => (int)date('m'),
            'periode_tahun' => (int)date('Y'),
            'tanggal_tagihan' => date('Y-m-01'),
            'tanggal_jatuh_tempo' => date('Y-m-10'),
            'nominal_pokok' => 1000000,
            'nominal_denda' => 0,
            'nominal_total' => 1000000,
            'status' => 'lunas',
            'metode_pembayaran' => 'cash',
        ]);
    }

    public function test_admin_bisa_mengakses_halaman_dashboard(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
        $response->assertSee('Total Kamar');
        $response->assertSee('1 Unit');
        $response->assertSee('100% Hunian');
        $response->assertSee(sprintf('%02d/%d', date('m'), date('Y')));
    }

    public function test_non_admin_tidak_bisa_mengakses_halaman_dashboard(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }
}
