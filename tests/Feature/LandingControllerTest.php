<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Kamar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LandingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('fasilitas_aktif_landing');
        Cache::forget('kamar_aktif_landing');
    }

    public function test_landing_page_loads_successfully()
    {
        Fasilitas::create([
            'nama' => 'WiFi Super Cepat',
            'ikon' => 'wifi',
            'deskripsi' => 'Koneksi internet cepat 100Mbps',
            'is_active' => true,
        ]);

        $response = $this->get(route('landing.index'));

        $response->assertStatus(200);
        $response->assertViewIs('landing.index');
        $response->assertViewHasAll(['fasilitas', 'kamarList', 'cleanWa', 'reviews']);
    }

    public function test_fasilitas_page_loads_successfully()
    {
        $response = $this->get(route('landing.fasilitas'));

        $response->assertStatus(200);
        $response->assertViewIs('landing.fasilitas');
    }

    public function test_kamar_list_page_filters_by_type()
    {
        Kamar::create([
            'nomor_kamar' => 'VIP-01',
            'lantai' => 1,
            'tipe' => 'vip',
            'luas_m2' => 20.0,
            'harga_bulan' => 1500000,
            'status' => 'tersedia',
        ]);

        Kamar::create([
            'nomor_kamar' => 'STD-01',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 800000,
            'status' => 'tersedia',
        ]);

        $response = $this->get(route('landing.kamar', ['tipe_kamar' => 'vip']));

        $response->assertStatus(200);
        $response->assertViewIs('landing.kamar-list');
    }

    public function test_hitung_harga_returns_correct_pricing()
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);

        $response = $this->postJson(route('landing.hitungHarga', $kamar->id), [
            'tipe_sewa' => 'bulanan',
            'durasi'    => 2,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'     => true,
                'total_harga' => 2000000,
                'dp_minimal'  => 600000, // Default 30% DP
            ]);
    }

    public function test_hitung_harga_fails_on_invalid_duration()
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);

        $response = $this->postJson(route('landing.hitungHarga', $kamar->id), [
            'tipe_sewa' => 'bulanan',
            'durasi'    => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['durasi']);
    }

    public function test_cek_ketersediaan_handles_daily_rental_period_properly()
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '103',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);

        $response = $this->postJson(route('landing.cekKetersediaan'), [
            'tanggal_masuk' => now()->addDays(2)->format('Y-m-d'),
            'durasi'        => 3,
            'tipe_sewa'     => 'harian',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath("availability.{$kamar->id}.is_available", true);
    }

    public function test_track_whatsapp_click_registers_activity()
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '104',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);

        $response = $this->postJson(route('analytics.track-whatsapp'), [
            'source'   => 'landing_hero',
            'kamar_id' => $kamar->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('whatsapp_clicks', [
            'source'   => 'landing_hero',
            'kamar_id' => $kamar->id,
        ]);
    }
}
