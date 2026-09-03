<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Kamar;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KamarTest extends TestCase
{
    use RefreshDatabase;

    public function test_foto_url_returns_storage_path_when_file_exists()
    {
        Storage::fake('public');
        Storage::disk('public')->put('rooms/test-room.jpg', 'fake content');

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'deluxe',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'foto' => 'rooms/test-room.jpg',
            'status' => 'tersedia'
        ]);

        $this->assertEquals(asset('storage/rooms/test-room.jpg'), $kamar->foto_url);
    }

    public function test_foto_url_returns_unsplash_fallback_when_foto_is_empty()
    {
        $kamarVip = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'vip',
            'luas_m2' => 15.0,
            'harga_bulan' => 2000000,
            'foto' => null,
            'status' => 'tersedia'
        ]);

        $kamarDeluxe = Kamar::create([
            'nomor_kamar' => '103',
            'lantai' => 1,
            'tipe' => 'deluxe',
            'luas_m2' => 12.0,
            'harga_bulan' => 1200000,
            'foto' => null,
            'status' => 'tersedia'
        ]);

        $kamarStandar = Kamar::create([
            'nomor_kamar' => '104',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 10.0,
            'harga_bulan' => 800000,
            'foto' => null,
            'status' => 'tersedia'
        ]);

        $this->assertStringContainsString('unsplash.com', $kamarVip->foto_url);
        $this->assertStringContainsString('photo-1618773928121-c32242e63f39', $kamarVip->foto_url);

        $this->assertStringContainsString('unsplash.com', $kamarDeluxe->foto_url);
        $this->assertStringContainsString('photo-1590490360182-c33d57733427', $kamarDeluxe->foto_url);

        $this->assertStringContainsString('unsplash.com', $kamarStandar->foto_url);
        $this->assertStringContainsString('photo-1598928506311-c55ded91a20c', $kamarStandar->foto_url);
    }

    public function test_kalkulasi_harga_dasar_calculates_correctly()
    {
        $kamar = new Kamar(['harga_bulan' => 3000000]);

        $this->assertEquals(200000, $kamar->kalkulasiHargaDasar('harian', 2));
        $this->assertEquals(750000, $kamar->kalkulasiHargaDasar('mingguan', 1));
        $this->assertEquals(9000000, $kamar->kalkulasiHargaDasar('bulanan', 3));
    }

    public function test_kalkulasi_harga_sewa_applies_discount_override()
    {
        $kamar = new Kamar(['harga_bulan' => 1000000]);

        // 2 bulan = 2.000.000, diskon 10% = 1.800.000
        $this->assertEquals(1800000, $kamar->kalkulasiHargaSewa('bulanan', 2, 10.0));
    }

    public function test_kalkulasi_minimal_dp_calculates_correct_percentage()
    {
        config(['reservasi.dp_percentage' => 0.30]);
        $kamar = new Kamar();

        $this->assertEquals(300000, $kamar->kalkulasiMinimalDp(1000000));
        $this->assertEquals(0, $kamar->kalkulasiMinimalDp(0));
    }
}
