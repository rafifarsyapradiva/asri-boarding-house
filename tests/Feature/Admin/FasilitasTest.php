<?php

namespace Tests\Feature\Admin;


use PHPUnit\Framework\Attributes\Test;
use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FasilitasTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    #[Test]
    public function admin_can_view_facilities_index_page()
    {
        $facility = Fasilitas::create([
            'nama' => 'WiFi High Speed',
            'ikon' => 'wifi',
            'deskripsi' => 'Koneksi internet cepat 100 Mbps',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.fasilitas.index'));

        $response->assertStatus(200);
        $response->assertSee('WiFi High Speed');
        $response->assertSee('📶'); // Emoji from Model Accessor
    }

    #[Test]
    public function admin_can_create_facility_via_ajax()
    {
        $payload = [
            'nama' => 'Air Conditioner',
            'ikon' => 'snowflake',
            'deskripsi' => 'Pendingin ruangan 1 PK hemat listrik',
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.fasilitas.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Fasilitas berhasil ditambahkan!',
        ]);

        $this->assertDatabaseHas('fasilitas', [
            'nama' => 'Air Conditioner',
            'ikon' => 'snowflake',
        ]);
    }

    #[Test]
    public function store_facility_fails_validation_with_missing_or_duplicate_data()
    {
        Fasilitas::create([
            'nama' => 'Water Heater',
            'ikon' => 'bath',
            'deskripsi' => 'Pemanas air otomatis',
            'is_active' => true,
        ]);

        // Duplicate name
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.fasilitas.store'), [
                'nama' => 'Water Heater',
                'ikon' => 'shower',
                'deskripsi' => 'Deskripsi baru',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nama']);
    }

    #[Test]
    public function admin_can_update_facility_via_ajax()
    {
        $facility = Fasilitas::create([
            'nama' => 'Lemari Lama',
            'ikon' => 'door-closed',
            'deskripsi' => 'Deskripsi lama',
            'is_active' => false,
        ]);

        $payload = [
            'nama' => 'Lemari Pakaian Kayu Jati',
            'ikon' => 'door-closed',
            'deskripsi' => 'Deskripsi diperbarui',
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson(route('admin.fasilitas.update', $facility->id), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Fasilitas berhasil diperbarui!',
        ]);

        $this->assertDatabaseHas('fasilitas', [
            'id' => $facility->id,
            'nama' => 'Lemari Pakaian Kayu Jati',
        ]);
    }

    #[Test]
    public function admin_can_delete_unattached_facility()
    {
        $facility = Fasilitas::create([
            'nama' => 'Meja Lipat',
            'ikon' => 'desktop',
            'deskripsi' => 'Meja belajar lipat',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('admin.fasilitas.destroy', $facility->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Fasilitas berhasil dihapus!',
        ]);

        $this->assertDatabaseMissing('fasilitas', [
            'id' => $facility->id,
        ]);
    }

    #[Test]
    public function cannot_delete_facility_attached_to_a_room()
    {
        $facility = Fasilitas::create([
            'nama' => 'Kasur Busa',
            'ikon' => 'bed',
            'deskripsi' => 'Kasur empuk',
            'is_active' => true,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);
        $kamar->fasilitas()->attach($facility->id);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('admin.fasilitas.destroy', $facility->id));

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Fasilitas tidak dapat dihapus karena sedang terpasang pada satu atau lebih kamar.',
        ]);

        $this->assertDatabaseHas('fasilitas', [
            'id' => $facility->id,
        ]);
    }
}
