<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Fasilitas;
use App\Models\User;
use App\Models\Reservasi;
use App\Models\Penyewa;
use Database\Seeders\FasilitasSeeder;
use Database\Seeders\KamarSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminKamarCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $penyewaUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed data master
        $this->seed(FasilitasSeeder::class);

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

    /**
     * Test guest cannot access room management routes.
     */
    public function test_guest_is_redirected_from_room_management(): void
    {
        $this->get(route('admin.kamar.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.kamar.create'))->assertRedirect(route('admin.login'));
    }

    public function test_tenant_cannot_access_room_management(): void
    {
        $this->actingAs($this->penyewaUser);
        $this->get(route('admin.kamar.index'))->assertStatus(403); // aborted because role is not admin
    }

    /**
     * Test admin can access index page.
     */
    public function test_admin_can_access_room_list(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => 'A101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 900000,
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.kamar.index'));
        $response->assertStatus(200);
        $response->assertSee('A101');
        $response->assertSee('Lantai 1');
    }

    /**
     * Test admin can view room status condition summary statistics and filter by status.
     */
    public function test_admin_can_view_room_status_statistics_and_filtering(): void
    {
        Kamar::create([
            'nomor_kamar' => 'STAT-101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);

        Kamar::create([
            'nomor_kamar' => 'STAT-102',
            'lantai' => 1,
            'tipe' => 'deluxe',
            'luas_m2' => 16,
            'harga_bulan' => 1500000,
            'status' => 'terisi',
        ]);

        Kamar::create([
            'nomor_kamar' => 'STAT-103',
            'lantai' => 2,
            'tipe' => 'vip',
            'luas_m2' => 20,
            'harga_bulan' => 2000000,
            'status' => 'maintenance',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.kamar.index'));
        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 3 &&
                   $stats['tersedia'] === 1 &&
                   $stats['terisi'] === 1 &&
                   $stats['maintenance'] === 1;
        });
        $response->assertSee('Siap Disewakan');
        $response->assertSee('Aktif Dihuni');
        $response->assertSee('Perbaikan/Renovasi');

        // Test filtering status tersedia
        $responseFiltered = $this->actingAs($this->admin)->get(route('admin.kamar.index', ['status' => 'tersedia']));
        $responseFiltered->assertStatus(200);
        $responseFiltered->assertSee('STAT-101');
        $responseFiltered->assertDontSee('STAT-103');
    }

    /**
     * Test admin can access create page.
     */
    public function test_admin_can_access_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.kamar.create'));
        $response->assertStatus(200);
        $response->assertSee('Form Tambah Kamar');
    }

    /**
     * Test admin can store a new room.
     */
    public function test_admin_can_store_room_with_facilities_and_image(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('room1.jpg', 100, 'image/jpeg');
        $fasilitasIds = Fasilitas::limit(2)->pluck('id')->toArray();

        $payload = [
            'nomor_kamar' => 'B201',
            'lantai' => 2,
            'tipe' => 'vip',
            'luas_m2' => 24.5,
            'harga_bulan' => 2500000,
            'deskripsi' => 'VIP room description.',
            'foto' => $file,
            'status' => 'tersedia',
            'fasilitas' => $fasilitasIds,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.kamar.store'), $payload);

        $response->assertRedirect(route('admin.kamar.index'));
        $response->assertSessionHas('success', 'Kamar berhasil ditambahkan.');

        $kamar = Kamar::where('nomor_kamar', 'B201')->first();
        $this->assertNotNull($kamar);
        $this->assertEquals(2, $kamar->lantai);
        $this->assertEquals('vip', $kamar->tipe);
        $this->assertEquals(24.5, $kamar->luas_m2);
        $this->assertEquals(2500000, $kamar->harga_bulan);
        $this->assertNotNull($kamar->foto);
        $this->assertCount(2, $kamar->fasilitas);

        Storage::disk('public')->assertExists($kamar->foto);
    }

    /**
     * Test store validation rules.
     */
    public function test_store_validation_errors(): void
    {
        $payload = [
            'nomor_kamar' => '', // required
            'lantai' => 0, // min 1
            'tipe' => 'invalid_tipe', // in:standar,deluxe,vip
            'luas_m2' => -5, // min 0
            'harga_bulan' => -100, // min 0
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.kamar.store'), $payload);

        $response->assertSessionHasErrors(['nomor_kamar', 'lantai', 'tipe', 'luas_m2', 'harga_bulan']);
    }

    /**
     * Test admin can access room details.
     */
    public function test_admin_can_view_room_details(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => 'C301',
            'lantai' => 3,
            'tipe' => 'deluxe',
            'luas_m2' => 18,
            'harga_bulan' => 1500000,
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.kamar.show', $kamar->id));
        $response->assertStatus(200);
        $response->assertSee('C301');
        $response->assertSee('Lantai 3');
        $response->assertSee('deluxe');
    }

    /**
     * Test admin can edit and update room.
     */
    public function test_admin_can_update_room_details_and_image(): void
    {
        Storage::fake('public');
        $oldFile = UploadedFile::fake()->create('old.jpg', 100, 'image/jpeg');
        $oldPath = $oldFile->store('kamar', 'public');

        $kamar = Kamar::create([
            'nomor_kamar' => 'D401',
            'lantai' => 4,
            'tipe' => 'vip',
            'luas_m2' => 30,
            'harga_bulan' => 3000000,
            'foto' => $oldPath,
            'status' => 'tersedia',
        ]);

        $newFile = UploadedFile::fake()->create('new.jpg', 100, 'image/jpeg');
        $payload = [
            'nomor_kamar' => 'D401-edit',
            'lantai' => 4,
            'tipe' => 'vip',
            'luas_m2' => 32,
            'harga_bulan' => 3100000,
            'foto' => $newFile,
            'status' => 'maintenance',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.kamar.update', $kamar->id), $payload);

        $response->assertRedirect(route('admin.kamar.index'));
        $response->assertSessionHas('success', 'Kamar berhasil diperbarui.');

        $kamar->refresh();
        $this->assertEquals('D401-edit', $kamar->nomor_kamar);
        $this->assertEquals(32, $kamar->luas_m2);
        $this->assertEquals(3100000, $kamar->harga_bulan);
        $this->assertEquals('maintenance', $kamar->status);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($kamar->foto);
    }

    /**
     * Test fast status update.
     */
    public function test_admin_can_update_room_status_only(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => 'E101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 800000,
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.kamar.updateStatus', $kamar->id), [
                'status' => 'maintenance',
            ]);

        $response->assertRedirect(route('admin.kamar.index'));
        $response->assertSessionHas('success', 'Status kamar berhasil diperbarui.');
        $this->assertEquals('maintenance', $kamar->fresh()->status);
    }

    public function test_admin_can_delete_empty_room(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('delete.jpg', 100, 'image/jpeg');
        $path = $file->store('kamar', 'public');

        $kamar = Kamar::create([
            'nomor_kamar' => 'F101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 800000,
            'foto' => $path,
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.kamar.destroy', $kamar->id));

        $response->assertRedirect(route('admin.kamar.index'));
        $response->assertSessionHas('success', 'Kamar berhasil dihapus.');

        $this->assertSoftDeleted('kamar', ['id' => $kamar->id]);
        Storage::disk('public')->assertMissing($path);
    }

    /**
     * Test admin cannot delete room with reservation or tenant records.
     */
    public function test_admin_cannot_delete_room_with_history(): void
    {
        $kamar1 = Kamar::create([
            'nomor_kamar' => 'G101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 800000,
            'status' => 'tersedia',
        ]);

        $kamar2 = Kamar::create([
            'nomor_kamar' => 'G102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 800000,
            'status' => 'tersedia',
        ]);

        // Case A: Room has reservation
        Reservasi::create([
            'user_id' => $this->penyewaUser->id,
            'kamar_id' => $kamar1->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 800000,
            'status' => 'pending',
            'order_id' => 'RSV-TEST-DEL-1',
        ]);

        // Case B: Room has tenant record
        Penyewa::create([
            'user_id' => $this->penyewaUser->id,
            'kamar_id' => $kamar2->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Wali',
            'no_wali' => '081234567890',
            'deposit' => 200000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
        ]);

        // Try deleting G101
        $response1 = $this->actingAs($this->admin)
            ->delete(route('admin.kamar.destroy', $kamar1->id));
        $response1->assertRedirect();
        $response1->assertSessionHas('error', 'Kamar tidak dapat dihapus karena memiliki riwayat reservasi atau data penyewa.');
        $this->assertDatabaseHas('kamar', ['id' => $kamar1->id]);

        // Try deleting G102
        $response2 = $this->actingAs($this->admin)
            ->delete(route('admin.kamar.destroy', $kamar2->id));
        $response2->assertRedirect();
        $response2->assertSessionHas('error', 'Kamar tidak dapat dihapus karena memiliki riwayat reservasi atau data penyewa.');
        $this->assertDatabaseHas('kamar', ['id' => $kamar2->id]);
    }
}
