<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AdminPenyewaSanitizationTest extends TestCase
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
            'no_hp' => '6281234567891',
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

    public function test_it_sanitizes_phone_numbers_and_forces_62_prefix(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), [
                'nama' => 'Tenant Baru',
                'email' => 'tenantbaru@example.com',
                'no_hp' => '081234567895', // Starts with 0
                'nik' => '1234567890123456',
                'kamar_id' => $this->kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Tenant',
                'no_wali' => '+6281234567896', // Has + and 62
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
                'deposit' => '1.000.000', // Format Rupiah
                'harga_sewa' => '1.200.000', // Format Rupiah
            ]);

        $response->assertRedirect(route('admin.penyewa.index'));
        
        // Assert stored user phone number has 62 prefix
        $user = User::where('email', 'tenantbaru@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('6281234567895', $user->no_hp);

        // Assert stored tenant wali phone has 62 prefix and deposit/harga_sewa formatted as float
        $penyewa = $user->penyewa;
        $this->assertNotNull($penyewa);
        $this->assertEquals('6281234567896', $penyewa->no_wali);
        $this->assertEquals(1000000, floatval($penyewa->deposit));
        $this->assertEquals(1200000, floatval($penyewa->harga_sewa));
    }

    public function test_it_prevents_duplicate_room_allocations(): void
    {
        Queue::fake();

        // Create tenant manually first to take the room
        $tenantUser = User::create([
            'nama' => 'Tenant Lama',
            'email' => 'tenantlama@example.com',
            'no_hp' => '6281234567890',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
        
        Penyewa::create([
            'user_id' => $tenantUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1111111111111111',
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Wali Lama',
            'no_wali' => '6281234567899',
            'deposit' => 1000000,
            'harga_sewa' => 1000000,
            'status' => 'aktif',
        ]);
        
        // Update kamar status to terisi
        $this->kamar->update(['status' => 'terisi']);

        // Admin tries to assign another tenant to the same room
        $response = $this->from(route('admin.penyewa.create'))
            ->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), [
                'nama' => 'Tenant Baru',
                'email' => 'tenantbaru@example.com',
                'no_hp' => '081234567895',
                'nik' => '2222222222222222',
                'kamar_id' => $this->kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Baru',
                'no_wali' => '081234567896',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
            ]);

        $response->assertRedirect(route('admin.penyewa.create'));
        $response->assertSessionHasErrors(['kamar_id']);
    }
}
