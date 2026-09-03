<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Reservasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoftDeletesTest extends TestCase
{
    use RefreshDatabase;

    public function test_kamar_can_be_soft_deleted_and_restored(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => 'A101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 15,
            'harga_bulan' => 1500000,
            'status' => 'tersedia',
        ]);

        $kamar->delete();

        $this->assertSoftDeleted('kamar', ['id' => $kamar->id]);

        $kamar->restore();

        $this->assertDatabaseHas('kamar', [
            'id' => $kamar->id,
            'deleted_at' => null,
        ]);
    }

    public function test_penyewa_and_user_can_be_soft_deleted_with_suffix_release(): void
    {
        $admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => 'A102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 15,
            'harga_bulan' => 1500000,
            'status' => 'tersedia',
        ]);

        $user = User::create([
            'nama' => 'Rudi',
            'email' => 'rudi@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '081223344556',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'status' => 'aktif',
            'nama_wali' => 'Wali Rudi',
            'no_wali' => '081223344557',
        ]);

        // Simulasikan penghapusan lewat PenyewaController destroy
        $response = $this->actingAs($admin)
            ->delete(route('admin.penyewa.destroy', $penyewa));

        $response->assertRedirect(route('admin.penyewa.index'));
        $response->assertSessionHas('success');

        // Pastikan keduanya ter-soft delete
        $this->assertSoftDeleted('penyewa', ['id' => $penyewa->id]);
        $this->assertSoftDeleted('users', ['id' => $user->id]);

        // Refresh model untuk mendapatkan data yang ter-update
        $deletedPenyewa = Penyewa::withTrashed()->find($penyewa->id);
        $deletedUser = User::withTrashed()->find($user->id);

        // Pastikan NIK, email, dan no_hp ditambahkan suffix _deleted_
        $this->assertStringContainsString('_deleted_', $deletedPenyewa->nik);
        $this->assertStringContainsString('_deleted_', $deletedUser->email);
        $this->assertStringContainsString('_deleted_', $deletedUser->no_hp);

        // Verifikasi bahwa data asli sekarang bebas untuk didaftarkan ulang
        $newUser = User::create([
            'nama' => 'Rudi Baru',
            'email' => 'rudi@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '081223344556',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $newPenyewa = Penyewa::create([
            'user_id' => $newUser->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'status' => 'aktif',
            'nama_wali' => 'Wali Rudi',
            'no_wali' => '081223344557',
        ]);

        $this->assertDatabaseHas('penyewa', ['id' => $newPenyewa->id]);
        $this->assertDatabaseHas('users', ['id' => $newUser->id]);
    }

    public function test_reservasi_can_be_soft_deleted(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => 'A103',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 15,
            'harga_bulan' => 1500000,
            'status' => 'tersedia',
        ]);

        $user = User::create([
            'nama' => 'Budi',
            'email' => 'budi@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '081223344558',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $reservasi = Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'durasi' => 1,
            'total_harga' => 1500000,
            'status' => 'pending',
            'is_dp' => false,
        ]);

        $reservasi->delete();

        $this->assertSoftDeleted('reservasi', ['id' => $reservasi->id]);
    }
}
