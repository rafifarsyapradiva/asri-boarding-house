<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Fasilitas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFasilitasCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $penyewaUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::create([
            'nama' => 'Admin Test',
            'email' => 'admin.test@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '62895330031122',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        // Create Tenant User
        $this->penyewaUser = User::create([
            'nama' => 'Penyewa Test',
            'email' => 'penyewa.test@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '628123456789',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
    }

    public function test_guest_is_redirected_from_facilities_management(): void
    {
        $this->get(route('admin.fasilitas.index'))->assertRedirect(route('admin.login'));
    }

    public function test_tenant_cannot_access_facilities_management(): void
    {
        $this->actingAs($this->penyewaUser);
        $this->get(route('admin.fasilitas.index'))->assertStatus(403);
    }

    public function test_admin_can_access_facilities_list(): void
    {
        $fasilitas = Fasilitas::create([
            'nama' => 'Internet Fiber',
            'ikon' => 'wifi',
            'deskripsi' => 'Super fast 100 Mbps internet',
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.fasilitas.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Internet Fiber');
        $response->assertSee('Super fast 100 Mbps internet');
    }

    public function test_admin_can_store_facility_via_ajax(): void
    {
        $payload = [
            'nama' => 'TV Kabel Premium',
            'ikon' => 'desktop',
            'deskripsi' => 'TV Kabel dengan 100+ channels',
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.fasilitas.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Fasilitas berhasil ditambahkan!'
        ]);

        $this->assertDatabaseHas('fasilitas', [
            'nama' => 'TV Kabel Premium',
            'ikon' => 'desktop',
            'deskripsi' => 'TV Kabel dengan 100+ channels',
            'is_active' => true
        ]);
    }

    public function test_admin_can_update_facility_via_ajax(): void
    {
        $fasilitas = Fasilitas::create([
            'nama' => 'TV Kabel Biasa',
            'ikon' => 'desktop',
            'deskripsi' => 'TV Kabel lokal',
            'is_active' => true
        ]);

        $payload = [
            'nama' => 'TV Kabel Premium',
            'ikon' => 'desktop',
            'deskripsi' => 'TV Kabel dengan 100+ channels premium',
            'is_active' => 0,
        ];

        // Resource route pluralizes parameter to 'fasilita' or 'fasilitas'
        // Let's resolve route dynamically via route helper
        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.fasilitas.update', $fasilitas->id), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Fasilitas berhasil diperbarui!'
        ]);

        $this->assertDatabaseHas('fasilitas', [
            'id' => $fasilitas->id,
            'nama' => 'TV Kabel Premium',
            'deskripsi' => 'TV Kabel dengan 100+ channels premium',
            'is_active' => false
        ]);
    }

    public function test_admin_can_delete_unused_facility_via_ajax(): void
    {
        $fasilitas = Fasilitas::create([
            'nama' => 'Fasilitas Sampah',
            'ikon' => 'bolt',
            'deskripsi' => 'Tidak dipakai',
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('admin.fasilitas.destroy', $fasilitas->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Fasilitas berhasil dihapus!'
        ]);

        $this->assertDatabaseMissing('fasilitas', [
            'id' => $fasilitas->id
        ]);
    }

    public function test_admin_cannot_delete_used_facility(): void
    {
        $fasilitas = Fasilitas::create([
            'nama' => 'AC Hemat Energi',
            'ikon' => 'snowflake',
            'deskripsi' => 'AC hemat listrik',
            'is_active' => true
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101Test',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'tersedia',
        ]);

        $kamar->fasilitas()->attach($fasilitas->id);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('admin.fasilitas.destroy', $fasilitas->id));

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Fasilitas tidak dapat dihapus karena sedang terpasang pada satu atau lebih kamar.'
        ]);

        $this->assertDatabaseHas('fasilitas', [
            'id' => $fasilitas->id
        ]);
    }
}
