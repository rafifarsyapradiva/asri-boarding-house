<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Fasilitas;
use App\Models\User;
use Database\Seeders\FasilitasSeeder;
use Database\Seeders\KamarSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KamarDataRevenueAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Bersihkan properti seeder dan jalankan seeder
        unset($this->seeder);
        $this->seed(FasilitasSeeder::class);
        $this->seed(KamarSeeder::class);
    }

    /**
     * Metode test_total_kuota_kamar_dan_distribusi_lantai_wajib_32()
     */
    public function test_total_kuota_kamar_dan_distribusi_lantai_wajib_32(): void
    {
        // Assert hitungan total baris di tabel kamar wajib tepat 32
        $this->assertDatabaseCount('kamar', 32);

        // Assert kuota lantai 1 berjumlah 16 kamar
        $this->assertEquals(16, Kamar::where('lantai', 1)->count());

        // Assert kuota lantai 2 berjumlah 16 kamar
        $this->assertEquals(16, Kamar::where('lantai', 2)->count());

        // Assert jumlah sub-total masing-masing tipe kamar: VIP = 6, DELUXE = 3, STANDAR = 23
        $this->assertEquals(6, Kamar::where('tipe', 'vip')->count());
        $this->assertEquals(3, Kamar::where('tipe', 'deluxe')->count());
        $this->assertEquals(23, Kamar::where('tipe', 'standar')->count());

        // Assert relasi fasilitas untuk tipe VIP (6 fasilitas)
        $vipRoom = Kamar::where('tipe', 'vip')->first();
        $this->assertNotNull($vipRoom);
        $vipFasilitas = $vipRoom->fasilitas->pluck('nama')->toArray();
        $this->assertContains('Air Conditioner (AC)', $vipFasilitas);
        $this->assertContains('Kamar Mandi Dalam', $vipFasilitas);
        $this->assertContains('Free Listrik', $vipFasilitas);

        // Assert relasi fasilitas untuk tipe DELUXE (5 fasilitas, tanpa AC)
        $deluxeRoom = Kamar::where('tipe', 'deluxe')->first();
        $this->assertNotNull($deluxeRoom);
        $deluxeFasilitas = $deluxeRoom->fasilitas->pluck('nama')->toArray();
        $this->assertNotContains('Air Conditioner (AC)', $deluxeFasilitas);
        $this->assertContains('Kamar Mandi Dalam', $deluxeFasilitas);

        // Assert relasi fasilitas untuk tipe STANDAR (5 fasilitas, tanpa AC & Kamar Mandi Dalam)
        $standarRoom = Kamar::where('tipe', 'standar')->first();
        $this->assertNotNull($standarRoom);
        $standarFasilitas = $standarRoom->fasilitas->pluck('nama')->toArray();
        $this->assertNotContains('Air Conditioner (AC)', $standarFasilitas);
        $this->assertNotContains('Kamar Mandi Dalam', $standarFasilitas);
        $this->assertContains('Kamar Mandi Luar (Bersama)', $standarFasilitas);
    }

    /**
     * Metode test_audit_potensi_pendapatan_maksimum_bulanan()
     */
    public function test_audit_potensi_pendapatan_maksimum_bulanan(): void
    {
        // Lakukan kueri penjumlahan Math Sum langsung ke database
        $totalPotensi = Kamar::sum('harga_bulan');

        // Assert secara mutlak bahwa nilai $totalPotensi harus bernilai tepat 28500000
        $this->assertEquals(28500000, (float)$totalPotensi);
    }

    /**
     * Metode test_penegakan_maximum_durasi_sewa_offline()
     */
    public function test_penegakan_maximum_durasi_sewa_offline(): void
    {
        // Inisialisasi Admin
        $admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin.audit@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '62895330031313',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        // Uji untuk setiap tipe kamar: vip, deluxe, standar
        $tipeKamars = ['vip', 'deluxe', 'standar'];
        
        foreach ($tipeKamars as $tipe) {
            $kamar = Kamar::where('tipe', $tipe)->first();
            $this->assertNotNull($kamar);

            // Kirim request manual offline dengan tipe_sewa bulanan durasi melebihi 12 bulan (misal 13 bulan)
            $payload = [
                'nama' => "Penyewa Offline {$tipe}",
                'email' => "offline.{$tipe}@example.com",
                'no_hp' => '0812345678' . rand(10, 99),
                'nik' => '12345678901234' . rand(10, 99),
                'kamar_id' => $kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Offline',
                'no_wali' => '08122334455',
                'tipe_sewa' => 'bulanan',
                'durasi' => 13, // Melanggar batas maksimum 12 bulan
            ];

            $response = $this->actingAs($admin)
                ->postJson(route('admin.penyewa.store'), $payload);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['durasi']);
            $response->assertJsonFragment([
                'durasi' => ['Durasi sewa bulanan maksimal 12 bulan.']
            ]);
        }
    }
}
