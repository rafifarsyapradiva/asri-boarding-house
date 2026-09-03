<?php

namespace Tests\Feature;


use PHPUnit\Framework\Attributes\Test;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Reservasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckerOptimizationsTest extends TestCase
{
    use RefreshDatabase;

    private Kamar $kamar;
    private User $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test room
        $this->kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'vip',
            'luas_m2' => 20,
            'harga_bulan' => 1500000.00,
            'deskripsi' => 'Kamar VIP Nyaman',
            'status' => 'tersedia',
        ]);

        // Create a test tenant user
        $this->tenant = User::create([
            'nama' => 'Penyewa Test',
            'email' => 'penyewa@test.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
    }

    /**
     * Test validation failure with invalid dates and formats.
     */
    public function test_invalid_date_format_returns_422_json(): void
    {
        // 1. Wrong format date string (DD-MM-YYYY)
        $response = $this->postJson(route('landing.cekKetersediaan'), [
            'kamar_id' => 'XYZ',
            'tanggal_masuk' => '31-02-2026',
            'durasi' => -5,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure(['success', 'message', 'errors']);

        // Check if message mentions formatting
        $this->assertStringContainsString('Format tanggal masuk harus YYYY-MM-DD', $response->json('message'));

        // 2. Fictional date formatted correctly but invalid (2026-02-31)
        $response2 = $this->postJson(route('landing.cekKetersediaan'), [
            'tanggal_masuk' => '2026-02-31',
            'durasi' => 1,
        ]);

        $response2->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test overlap boundary for active tenants.
     */
    public function test_tenant_overlap_exact_boundary(): void
    {
        // Create an active tenant checking out on 2026-08-31
        Penyewa::create([
            'user_id' => $this->tenant->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => '2026-08-01',
            'tanggal_keluar' => '2026-08-31',
            'nama_wali' => 'Wali Test',
            'no_wali' => '08123456789',
            'status' => 'aktif',
            'harga_sewa' => 1500000,
        ]);

        // Query check-in on the checkout day 2026-08-31
        $response = $this->postJson(route('landing.cekKetersediaan'), [
            'tanggal_masuk' => '2026-08-31',
            'durasi' => 1,
        ]);

        $response->assertStatus(200);
        $availability = $response->json('availability');
        
        // Should be marked as NOT available due to transition day boundary overlap
        $this->assertFalse($availability[$this->kamar->id]['is_available']);
        $this->assertEquals('terisi', $availability[$this->kamar->id]['status']);
    }

    /**
     * Test overlap boundary for reservations.
     */
    public function test_reservation_overlap_exact_boundary(): void
    {
        // Create a confirmed/active reservation ending on 2026-08-31
        Reservasi::create([
            'user_id' => $this->tenant->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-08-31',
            'durasi' => 1,
            'total_harga' => 1500000,
            'status' => 'lunas',
            'order_id' => 'RSV-BOUNDARY-001',
        ]);

        // Query check-in on 2026-08-31
        $response = $this->postJson(route('landing.cekKetersediaan'), [
            'tanggal_masuk' => '2026-08-31',
            'durasi' => 1,
        ]);

        $response->assertStatus(200);
        $availability = $response->json('availability');

        // Should be marked as NOT available due to overlap
        $this->assertFalse($availability[$this->kamar->id]['is_available']);
        $this->assertEquals('terisi', $availability[$this->kamar->id]['status']);
    }
}
