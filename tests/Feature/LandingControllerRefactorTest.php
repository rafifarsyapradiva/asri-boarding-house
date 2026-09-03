<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Setting;
use App\Models\User;
use App\Models\Reservasi;
use App\Models\Penyewa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LandingControllerRefactorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear cache before each test
        Cache::flush();

        // Setup default configuration
        Config::set('reservasi.admin_wa', '62895330031313');
        Config::set('reservasi.dp_percentage', 0.30);
        Config::set('reservasi.min_durasi.harian', 1);
        Config::set('reservasi.max_durasi.harian', 7);
        Config::set('reservasi.min_durasi.bulanan', 1);
        Config::set('reservasi.max_durasi.bulanan', 12);
    }

    /**
     * Test price calculation through AJAX hitung-harga endpoint.
     */
    public function test_hitung_harga_calculates_correctly_with_delegated_model_logic(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);

        // 1. Bulanan calculation (no discounts)
        $response = $this->postJson(route('landing.hitungHarga', $kamar->id), [
            'tipe_sewa' => 'bulanan',
            'durasi' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total_harga' => 2000000,
                'dp_minimal' => 600000, // 30% of 2000000
            ]);
    }

    /**
     * Test price calculation handles validation bounds correctly.
     */
    public function test_hitung_harga_enforces_duration_bounds(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);

        // Rent daily for 10 days, max duration is 7
        $response = $this->postJson(route('landing.hitungHarga', $kamar->id), [
            'tipe_sewa' => 'harian',
            'durasi' => 10,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Durasi sewa untuk tipe harian harus antara 1 dan 7.'
            ]);
    }

    /**
     * Test that room availability excludes maintenance rooms.
     */
    public function test_cek_ketersediaan_excludes_maintenance_rooms_correctly(): void
    {
        // 1. Create a rentable room
        $kamarTersedia = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);

        // 2. Create a room in maintenance status
        $kamarMaintenance = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'maintenance',
        ]);

        // Request availability check for 1 month starting today
        $response = $this->postJson(route('landing.cekKetersediaan'), [
            'tanggal_masuk' => date('Y-m-d'),
            'durasi' => 1,
        ]);

        $response->assertStatus(200);

        // Room 101 should be available
        $this->assertTrue($response->json("availability.{$kamarTersedia->id}.is_available"));

        // Room 102 (maintenance) should NOT be in the availability list (or is not marked as available)
        // Since we refactored it to filter rentable rooms, room 102 should either be absent from the list,
        // or if somehow queried, not marked as available. Our query checks whereIn status ['tersedia', 'terisi'].
        // Therefore, it will not be present in the keys, ensuring it can't be booked.
        $this->assertArrayNotHasKey($kamarMaintenance->id, $response->json('availability'));
    }

    /**
     * Test WhatsApp metadata helper is centralized and formats correctly.
     */
    public function test_landing_page_renders_whatsapp_metadata_correctly(): void
    {
        // Set custom contact whatsapp
        Setting::updateOrCreate(['key' => 'contact_whatsapp'], ['value' => '08123456789']);

        $response = $this->get(route('landing.index'));

        $response->assertStatus(200);
        
        // Formatted WhatsApp should starts with 628123456789
        $response->assertSee('628123456789');
    }
}
