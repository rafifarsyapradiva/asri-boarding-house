<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Reservasi;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Events\ReservasiDikonfirmasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AdminReservasiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    private Kamar $kamar;
    private Reservasi $reservasiDp;

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

        $this->user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
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

        $this->reservasiDp = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => true,
            'nominal_dp' => 300000,
            'nominal_sisa' => 700000,
            'status' => 'dp',
            'order_id' => 'RSV-ADMIN-TEST'
        ]);
    }

    /**
     * Test admin successfully confirms reservation and triggers transition.
     */
    public function test_admin_berhasil_mengonfirmasi_reservasi_dan_memicu_transisi_data(): void
    {
        Event::fake([ReservasiDikonfirmasi::class]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.reservasi.konfirmasi', $this->reservasiDp->id), [
                'nik' => '1234567890123456',
                'nama_wali' => 'Wali Dummy',
                'no_wali' => '081234567890',
                'catatan_admin' => 'Verifikasi KTP dan berkas berhasil.'
            ]);

        $response->assertRedirect(route('admin.reservasi.index'));
        $response->assertSessionHas('success');

        // Assert Reservasi updated in database
        $this->reservasiDp->refresh();
        $this->assertEquals('dikonfirmasi', $this->reservasiDp->status);
        $this->assertEquals('Verifikasi KTP dan berkas berhasil.', $this->reservasiDp->catatan_admin);
        $this->assertEquals($this->admin->id, $this->reservasiDp->dikonfirmasi_oleh);
        $this->assertNotNull($this->reservasiDp->penyewa_id);

        // Assert Penyewa record created
        $this->assertDatabaseHas('penyewa', [
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Dummy',
            'no_wali' => '081234567890',
            'status' => 'aktif',
        ]);

        // Assert Kamar status updated to terisi
        $this->kamar->refresh();
        $this->assertEquals('terisi', $this->kamar->status);

        // Assert sisa DP tagihan created
        $this->assertDatabaseHas('tagihan', [
            'penyewa_id' => $this->reservasiDp->penyewa_id,
            'nominal_total' => 700000,
            'status' => 'pending'
        ]);

        // Assert event was dispatched
        Event::assertDispatched(ReservasiDikonfirmasi::class);
    }

    /**
     * Test non-admin cannot confirm reservation.
     */
    public function test_non_admin_tidak_bisa_mengonfirmasi_reservasi(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.reservasi.konfirmasi', $this->reservasiDp->id), [
                'nik' => '1234567890123456',
                'nama_wali' => 'Wali Dummy',
                'no_wali' => '081234567890',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test validation rules for confirmation.
     */
    public function test_validasi_input_konfirmasi_reservasi(): void
    {
        // NIK not 16 digits, invalid phone format
        $response = $this->actingAs($this->admin)
            ->post(route('admin.reservasi.konfirmasi', $this->reservasiDp->id), [
                'nik' => '12345',
                'nama_wali' => '',
                'no_wali' => 'invalid-phone',
            ]);

        $response->assertSessionHasErrors(['nik', 'nama_wali', 'no_wali']);
    }
}
