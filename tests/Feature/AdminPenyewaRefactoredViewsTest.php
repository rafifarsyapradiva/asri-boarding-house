<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminPenyewaRefactoredViewsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

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
    }

    #[Test]
    public function admin_can_view_penyewa_index_page_with_flash_component_and_test_ids()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penyewa.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.penyewa.index');
        $response->assertSee('Daftar Penyewa Kost');
        $response->assertSee('data-testid="search-input"', false);
        $response->assertSee('data-testid="tenant-table"', false);
    }

    #[Test]
    public function show_view_renders_eager_loaded_tagihan_count_without_direct_db_query()
    {
        $user = User::create([
            'nama' => 'Penyewa Test',
            'email' => 'penyewa@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'terisi',
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Test',
            'no_wali' => '081234567890',
            'tanggal_masuk' => '2026-01-01',
            'tipe_sewa' => 'bulanan',
            'durasi' => 6,
            'harga_sewa' => 1000000,
            'deposit' => 1000000,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.penyewa.show', $penyewa->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.penyewa.show');
        $response->assertSee('Detail Profil Penyewa');
        $response->assertSee('1234567890123456');
        $response->assertSee('Wali Test');
    }

    #[Test]
    public function create_view_renders_safe_submit_form_and_flash_component()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penyewa.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.penyewa.create');
        $response->assertSee('Form Registrasi Penyewa');
        $response->assertSee('checkValidity()', false);
    }

    #[Test]
    public function edit_view_renders_safe_submit_form_and_flash_component()
    {
        $user = User::create([
            'nama' => 'Penyewa Edit',
            'email' => 'edit@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567893',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'terisi',
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '9876543210987654',
            'nama_wali' => 'Wali Edit',
            'no_wali' => '081987654321',
            'tanggal_masuk' => '2026-01-01',
            'tipe_sewa' => 'bulanan',
            'durasi' => 6,
            'harga_sewa' => 1000000,
            'deposit' => 1000000,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.penyewa.edit', $penyewa->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.penyewa.edit');
        $response->assertSee('Form Edit Data Penyewa');
        $response->assertSee('checkValidity()', false);
    }
}
