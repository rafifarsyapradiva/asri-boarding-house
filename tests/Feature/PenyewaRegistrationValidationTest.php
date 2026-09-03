<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenyewaRegistrationValidationTest extends TestCase
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
            'no_hp' => '081234567891',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $this->kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);
    }

    public function test_admin_can_register_new_tenant(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), [
                'nama' => 'Tenant Baru',
                'email' => 'tenantbaru@example.com',
                'no_hp' => '081234567895',
                'nik' => '1234567890123456',
                'kamar_id' => $this->kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Tenant',
                'no_wali' => '081234567896',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
            ]);

        $response->assertRedirect(route('admin.penyewa.index'));
        $response->assertSessionHas('success', 'Penyewa berhasil didaftarkan.');
    }

    public function test_admin_register_tenant_validation_failure(): void
    {
        // Suppress redirect to test session errors or assert redirect back
        $response = $this->from(route('admin.penyewa.create'))
            ->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), [
                'nama' => 'Tenant Baru',
                'email' => 'tenantbaru@example.com',
                'no_hp' => '081234567895',
                'nik' => '123', // invalid NIK (not 16 digits)
                'kamar_id' => $this->kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Tenant',
                'no_wali' => 'invalid-no-wali', // invalid format
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
            ]);

        $response->assertRedirect(route('admin.penyewa.create'));
        $response->assertSessionHasErrors(['nik', 'no_wali']);
    }
}
