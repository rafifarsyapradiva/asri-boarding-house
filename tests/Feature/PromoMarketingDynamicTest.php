<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Setting;
use App\Services\ReservasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoMarketingDynamicTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin.promo@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567811',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $this->user = User::create([
            'nama' => 'Penyewa Promo',
            'email' => 'penyewa.promo@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567812',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $this->kamar = Kamar::create([
            'nomor_kamar' => 'PRM-01',
            'lantai' => 1,
            'tipe' => 'deluxe',
            'luas_m2' => 16.0,
            'harga_bulan' => 2000000,
            'status' => 'tersedia',
        ]);
    }

    /**
     * Test Setting model helper for getting discounts.
     */
    public function test_setting_helper_resolves_discounts_correctly(): void
    {
        // Default configuration:
        // Package 1: bulanan, 1 month, 0% discount
        // Package 2: bulanan, 3 months, 5% discount
        // Package 3: bulanan, 12 months, 8.33% discount
        
        $this->assertEquals(0, Setting::getDiscountForDuration('bulanan', 1));
        $this->assertEquals(5, Setting::getDiscountForDuration('bulanan', 3));
        $this->assertEquals(8.33, Setting::getDiscountForDuration('bulanan', 12));
        
        // Random duration should have 0% discount
        $this->assertEquals(0, Setting::getDiscountForDuration('bulanan', 5));
    }

    /**
     * Test AJAX pricing calculator handles promo discounts.
     */
    public function test_ajax_calculator_returns_discounted_prices(): void
    {
        // 1. Without discount (1 Month)
        $response1 = $this->postJson(route('landing.hitungHarga', $this->kamar->id), [
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);
        $response1->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total_harga' => 2000000, // 2,000,000 * 1 = 2,000,000
            ]);

        // 2. With 5% discount (3 Months)
        // Original: 2,000,000 * 3 = 6,000,000; Promo: 6,000,000 * 0.95 = 5,700,000
        $response2 = $this->postJson(route('landing.hitungHarga', $this->kamar->id), [
            'tipe_sewa' => 'bulanan',
            'durasi' => 3,
        ]);
        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total_harga' => 5700000,
            ]);

        // 3. With 8.33% discount (12 Months)
        // Original: 2,000,000 * 12 = 24,000,000; Promo: 24,000,000 * (1 - 0.0833) = 22,000,800
        $response3 = $this->postJson(route('landing.hitungHarga', $this->kamar->id), [
            'tipe_sewa' => 'bulanan',
            'durasi' => 12,
        ]);
        $response3->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total_harga' => 22000800,
            ]);
    }

    /**
     * Test checkout service correctly writes discounted total to database.
     */
    public function test_reservation_checkout_writes_discounted_price_to_db(): void
    {
        $service = new ReservasiService();

        // 3 Months with 5% discount: Total 5,700,000; DP 30% = 1,710,000; Sisa = 3,990,000
        $dataInput = [
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d', strtotime('+1 day')),
            'durasi' => 3,
        ];

        $reservasi = $service->buatReservasi($dataInput);

        $this->assertDatabaseHas('reservasi', [
            'id' => $reservasi->id,
            'total_harga' => 5700000,
            'nominal_dp' => 1710000,
            'nominal_sisa' => 3990000,
        ]);
    }

    /**
     * Test admin can update these settings dynamically.
     */
    public function test_admin_can_update_promo_configurations(): void
    {
        $payload = [
            'logo_text' => 'Kost Asri Modern',
            'logo_icon' => '🏢',
            'hero_tagline' => '✨ Hunian Termewah',
            'hero_title' => 'Selamat Datang di Kost Asri',
            'hero_description' => 'Kost elite nomor satu.',
            'contact_address' => 'Jl. Baru No. 12',
            'contact_whatsapp' => '0895330031313',
            'contact_email' => 'admin@kostasrimodern.com',
            'google_maps_embed' => '<iframe src="https://www.google.com/maps/embed"></iframe>',
            'bank_name' => 'Bank Mandiri Baru',
            'bank_account_number' => '987-654-3210',
            'bank_account_owner' => 'Pemilik Kost Baru',
            
            // Tentang Kami
            'about_title' => 'Tentang Kami',
            'about_description' => 'Kost Asri Boarding House adalah hunian eksklusif...',
            'about_visi' => 'Menjadi pelopor...',
            'about_misi_1' => 'Misi 1',
            'about_misi_2' => 'Misi 2',
            'about_misi_3' => 'Misi 3',
            'about_misi_4' => 'Misi 4',

            // Promo Overrides
            'promo_pkg1_name' => 'Promo Bulanan Baru',
            'promo_pkg1_type' => 'bulanan',
            'promo_pkg1_duration' => 1,
            'promo_pkg1_discount' => 10, // Increased discount to 10%
            'promo_pkg1_desc' => 'Diskon perkenalan 10%',
            
            'promo_pkg2_name' => 'Promo Triwulan Baru',
            'promo_pkg2_type' => 'bulanan',
            'promo_pkg2_duration' => 3,
            'promo_pkg2_discount' => 15, // Increased discount to 15%
            'promo_pkg2_desc' => 'Hemat gila-gilaan 15%',

            'promo_pkg3_name' => 'Promo Tahunan Baru',
            'promo_pkg3_type' => 'bulanan',
            'promo_pkg3_duration' => 12,
            'promo_pkg3_discount' => 20, // Increased discount to 20%
            'promo_pkg3_desc' => 'Dapatkan free 2 bulan lebih sewa!',

            'promo_section_title' => 'Daftar Promo Super Gila',
            'promo_section_subtitle' => 'Pilih paket terbaik saat ini juga!',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings.edit'));
        $response->assertSessionHas('success');

        // Check if settings are updated in db
        $this->assertDatabaseHas('settings', [
            'key' => 'promo_pkg1_discount',
            'value' => '10',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'promo_pkg2_discount',
            'value' => '15',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'promo_section_title',
            'value' => 'Daftar Promo Super Gila',
        ]);

        // Verifikasi kalkulasi harga AJAX yang baru (Kamar 2,000,000/bulan)
        // 3 Months with 15% discount: 6,000,000 * 0.85 = 5,100,000
        $responsePrice = $this->postJson(route('landing.hitungHarga', $this->kamar->id), [
            'tipe_sewa' => 'bulanan',
            'durasi' => 3,
        ]);
        $responsePrice->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total_harga' => 5100000,
            ]);
    }

    /**
     * Test that setting promo package duration outside config limits fails validation.
     */
    public function test_promo_package_duration_must_conform_to_config_limits(): void
    {
        // Max duration for bulanan is 12 (as per config/reservasi.php)
        // If we try to set bulanan duration to 15, validation should fail.
        $payload = [
            'logo_text' => 'Kost Asri Modern',
            'logo_icon' => '🏢',
            'hero_tagline' => '✨ Hunian Termewah',
            'hero_title' => 'Selamat Datang di Kost Asri',
            'hero_description' => 'Kost elite nomor satu.',
            'contact_address' => 'Jl. Baru No. 12',
            'contact_whatsapp' => '0895330031313',
            'contact_email' => 'admin@kostasrimodern.com',
            'google_maps_embed' => '<iframe src="https://www.google.com/maps/embed"></iframe>',
            'bank_name' => 'Bank Mandiri Baru',
            'bank_account_number' => '987-654-3210',
            'bank_account_owner' => 'Pemilik Kost Baru',
            
            // Tentang Kami
            'about_title' => 'Tentang Kami',
            'about_description' => 'Kost Asri Boarding House adalah hunian eksklusif...',
            'about_visi' => 'Menjadi pelopor...',
            'about_misi_1' => 'Misi 1',
            'about_misi_2' => 'Misi 2',
            'about_misi_3' => 'Misi 3',
            'about_misi_4' => 'Misi 4',

            // Invalid Promo Configuration: bulanan duration = 15 (> 12 max bulanan)
            'promo_pkg1_name' => 'Promo Bulanan Baru',
            'promo_pkg1_type' => 'bulanan',
            'promo_pkg1_duration' => 15, // INVALID
            'promo_pkg1_discount' => 10,
            'promo_pkg1_desc' => 'Diskon perkenalan 10%',

            'promo_pkg2_name' => 'Promo Triwulan Baru',
            'promo_pkg2_type' => 'bulanan',
            'promo_pkg2_duration' => 3,
            'promo_pkg2_discount' => 15,
            'promo_pkg2_desc' => 'Hemat gila-gilaan 15%',

            'promo_pkg3_name' => 'Promo Tahunan Baru',
            'promo_pkg3_type' => 'bulanan',
            'promo_pkg3_duration' => 12,
            'promo_pkg3_discount' => 20,
            'promo_pkg3_desc' => 'Dapatkan free 2 bulan lebih sewa!',

            'promo_section_title' => 'Daftar Promo Super Gila',
            'promo_section_subtitle' => 'Pilih paket terbaik saat ini juga!',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $payload);

        $response->assertSessionHasErrors(['promo_pkg1_duration']);
        
        $errors = session('errors')->get('promo_pkg1_duration');
        $this->assertContains('Durasi Paket 1 harus antara 1 dan 12 untuk tipe sewa Bulanan.', $errors);
    }
}
