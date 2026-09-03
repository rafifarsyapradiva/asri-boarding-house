<?php

namespace Tests\Unit;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Reservasi;
use App\Services\ReservasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservasiServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the hitungHarga calculation logic.
     */
    public function test_hitung_harga_kalkulasi_dengan_benar(): void
    {
        // TODO: Buat mock/data dummy Kamar, panggil `ReservasiService::hitungHarga()`, dan buat assertion `assertEquals()` untuk memastikan nominal total sewa, nilai DP 30%, dan sisa pelunasan terhitung secara presisi sesuai rumus matematika.

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $service = new ReservasiService();
        $rincian = $service->hitungHarga($kamar, 'bulanan', 2);

        $this->assertEquals(2000000, $rincian['total_harga']);
        $this->assertEquals(600000, $rincian['nominal_dp']);
        $this->assertEquals(1400000, $rincian['nominal_sisa']);
    }

    /**
     * Test cekDoubleBooking scheduling collision checker.
     */
    public function test_cek_double_booking_mendeteksi_tumpangan_jadwal(): void
    {
        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'deluxe',
            'luas_m2' => 15.0,
            'harga_bulan' => 1500000,
            'status' => 'tersedia'
        ]);

        $service = new ReservasiService();

        // 1. Awalnya tidak ada booking, harus ketersediaan bernilai false (tidak double booking)
        $this->assertFalse($service->cekDoubleBooking($kamar, '2026-06-01', '2026-06-05'));

        // 2. Buat reservasi aktif (pending)
        Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-06-10',
            'durasi' => 1,
            'total_harga' => 1500000,
            'status' => 'pending',
            'order_id' => 'RSV-AKTIF',
        ]);

        // 3. Rentang tanggal bersinggungan langsung (overlap) -> harus mengembalikan true
        $this->assertTrue($service->cekDoubleBooking($kamar, '2026-06-05', '2026-06-15'));

        // 4. Rentang tanggal tidak bersinggungan -> harus mengembalikan false
        $this->assertFalse($service->cekDoubleBooking($kamar, '2026-06-11', '2026-06-15'));

        // 5. Buat reservasi yang batal (status = batal)
        Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => '2026-06-20',
            'tanggal_selesai' => '2026-06-25',
            'durasi' => 1,
            'total_harga' => 1500000,
            'status' => 'batal',
            'order_id' => 'RSV-BATAL',
        ]);

        // 6. Memesan di rentang tanggal reservasi yang batal -> harus mengembalikan false (tidak terblokir)
        $this->assertFalse($service->cekDoubleBooking($kamar, '2026-06-22', '2026-06-24'));
    }

    /**
     * Test buatReservasi records values accurately.
     */
    public function test_buat_reservasi_berhasil_menyimpan_dan_hitung_dp(): void
    {
        // TODO: Siapkan array data input parameter reservasi baru yang valid, panggil fungsi `buatReservasi()`, kemudian lakukan `assertDatabaseHas()` pada tabel reservasi untuk memverifikasi penyimpanan data dan keunikan order_id.

        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '103',
            'lantai' => 1,
            'tipe' => 'vip',
            'luas_m2' => 20.0,
            'harga_bulan' => 2000000,
            'status' => 'tersedia'
        ]);

        $service = new ReservasiService();
        
        $dataInput = [
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-07-31',
            'durasi' => 1,
        ];

        $reservasi = $service->buatReservasi($dataInput);

        $this->assertDatabaseHas('reservasi', [
            'id' => $reservasi->id,
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'total_harga' => 2000000,
            'nominal_dp' => 600000,
        ]);
    }
}
