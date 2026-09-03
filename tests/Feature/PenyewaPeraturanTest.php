<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Penyewa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenyewaPeraturanTest extends TestCase
{
    use RefreshDatabase;

    private User $activeTenantUser;
    private User $inactiveTenantUser;
    private User $adminUser;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'terisi'
        ]);

        $this->activeTenantUser = User::create([
            'nama' => 'Penyewa Aktif',
            'email' => 'aktif@example.com',
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
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        $this->inactiveTenantUser = User::create([
            'nama' => 'Penyewa Tidak Aktif',
            'email' => 'tidakaktif@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        // Inactive tenant does not have a status 'aktif' Penyewa record.
        Penyewa::create([
            'user_id' => $this->inactiveTenantUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123457',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'nonaktif',
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Tidak Aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        $this->adminUser = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin.rules@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567893',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        // Seed initial rules
        $this->seed(\Database\Seeders\PeraturanSeeder::class);
    }

    public function test_active_tenant_can_access_peraturan_page(): void
    {
        $response = $this->actingAs($this->activeTenantUser)
            ->get(route('penyewa.peraturan'));

        $response->assertStatus(200);
        $response->assertSee('Peraturan');
        $response->assertSee('Tata Tertib');
        $response->assertSee('Kost Putri Asri Boarding House');
        $response->assertSee('1. Kebersihan');
        $response->assertSee('Kerapian');
        $response->assertSee('Manajemen Asri Boarding House');
    }

    public function test_inactive_tenant_can_access_peraturan_page(): void
    {
        $response = $this->actingAs($this->inactiveTenantUser)
            ->get(route('penyewa.peraturan'));

        $response->assertStatus(200);
        $response->assertSee('Peraturan');
        $response->assertSee('Tata Tertib');
        $response->assertSee('Kost Putri Asri Boarding House');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('penyewa.peraturan'));

        $response->assertRedirect(route('penyewa.login'));
    }

    public function test_admin_cannot_access_peraturan_page(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('penyewa.peraturan'));

        $response->assertStatus(403);
    }

    public function test_admin_can_view_rules_list(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.peraturan.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Peraturan');
        $response->assertSee('Tambah Peraturan');
    }

    public function test_admin_can_create_new_rule(): void
    {
        $ruleData = [
            'judul' => 'Aturan Ketenangan',
            'deskripsi' => 'Dilarang membuat kegaduhan setelah pukul 22:00 WIB.',
            'ikon' => 'moon',
            'urutan' => 10,
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.peraturan.store'), $ruleData);

        $response->assertRedirect(route('admin.peraturan.index'));
        $response->assertSessionHas('success', 'Peraturan baru berhasil ditambahkan!');

        $this->assertDatabaseHas('peraturan', [
            'judul' => 'Aturan Ketenangan',
            'ikon' => 'moon',
            'urutan' => 10,
        ]);
    }

    public function test_admin_can_update_existing_rule(): void
    {
        // First create a rule
        $rule = \App\Models\Peraturan::create([
            'judul' => 'Aturan Lama',
            'deskripsi' => 'Deskripsi lama.',
            'ikon' => 'bolt',
            'urutan' => 5,
        ]);

        $updatedData = [
            'judul' => 'Aturan Diperbarui',
            'deskripsi' => 'Deskripsi diperbarui.',
            'ikon' => 'sparkles',
            'urutan' => 6,
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.peraturan.update', $rule->id), $updatedData);

        $response->assertRedirect(route('admin.peraturan.index'));
        $response->assertSessionHas('success', 'Peraturan berhasil diperbarui!');

        $this->assertDatabaseHas('peraturan', [
            'id' => $rule->id,
            'judul' => 'Aturan Diperbarui',
            'ikon' => 'sparkles',
            'urutan' => 6,
        ]);
    }

    public function test_admin_can_delete_existing_rule(): void
    {
        $rule = \App\Models\Peraturan::create([
            'judul' => 'Aturan Sementara',
            'deskripsi' => 'Akan dihapus.',
            'ikon' => 'no-symbol',
            'urutan' => 8,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.peraturan.destroy', $rule->id));

        $response->assertRedirect(route('admin.peraturan.index'));
        $response->assertSessionHas('success', 'Peraturan berhasil dihapus!');

        $this->assertDatabaseMissing('peraturan', [
            'id' => $rule->id,
        ]);
    }

    public function test_tenant_cannot_manage_rules(): void
    {
        // Try index
        $this->actingAs($this->activeTenantUser)
            ->get(route('admin.peraturan.index'))
            ->assertStatus(403);

        // Try store
        $this->actingAs($this->activeTenantUser)
            ->post(route('admin.peraturan.store'), [])
            ->assertStatus(403);

        // Try delete
        $rule = \App\Models\Peraturan::create([
            'judul' => 'Aturan Sementara',
            'deskripsi' => 'Akan dihapus.',
            'ikon' => 'no-symbol',
            'urutan' => 8,
        ]);

        $this->actingAs($this->activeTenantUser)
            ->delete(route('admin.peraturan.destroy', $rule->id))
            ->assertStatus(403);
    }
}
