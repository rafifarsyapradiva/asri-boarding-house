<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class TentangKamiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the Tentang Kami page renders successfully for guests.
     */
    public function test_tentang_kami_page_renders_successfully_for_guest(): void
    {
        $response = $this->get(route('landing.tentangKami'));

        $response->assertStatus(200);

        // Verify page layout sections and headings
        $response->assertSee('TENTANG KAMI ⚡');
        $response->assertSee('PROFIL KOST ASRI');
        $response->assertSee('VISI KAMI');
        $response->assertSee('MISI KAMI');
        $response->assertSee('MENGAPA MEMILIH KAMI? 🌟');
        $response->assertSee('PEMILIK KOST 🤝');
        $response->assertSee('Pak Asep (48 Tahun)');
        $response->assertSee('GALERI HUNIAN KAMI 📸');

        // Verify CTAs and links
        $response->assertSee(route('landing.kamar'));
        $response->assertSee('https://wa.me/');
    }

    /**
     * Test that the Tentang Kami page renders successfully for authenticated users.
     */
    public function test_tentang_kami_page_renders_successfully_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '081234567890',
        ]);

        $response = $this->actingAs($user)->get(route('landing.tentangKami'));

        $response->assertStatus(200);
        $response->assertSee('TENTANG KAMI ⚡');
    }

    /**
     * Test that the Tentang Kami page renders dynamically based on settings model.
     */
    public function test_tentang_kami_page_renders_dynamically_based_on_settings(): void
    {
        \App\Models\Setting::updateOrCreate(['key' => 'about_title'], ['value' => 'Tentang Asri']);
        \App\Models\Setting::updateOrCreate(['key' => 'about_description'], ['value' => 'Hunian nyaman, asri, dan aman.']);
        \App\Models\Setting::updateOrCreate(['key' => 'about_visi'], ['value' => 'Menjadi kost terpercaya di Semarang.']);
        \App\Models\Setting::updateOrCreate(['key' => 'about_misi_1'], ['value' => 'Menjaga ketertiban kost.']);
        \App\Models\Setting::updateOrCreate(['key' => 'about_misi_2'], ['value' => 'Memberikan servis terbaik.']);
        \App\Models\Setting::updateOrCreate(['key' => 'about_misi_3'], ['value' => 'Fasilitas super lengkap.']);
        \App\Models\Setting::updateOrCreate(['key' => 'about_misi_4'], ['value' => 'Harga terjangkau mahasiswa.']);

        $response = $this->get(route('landing.tentangKami'));

        $response->assertStatus(200);
        $response->assertSee('TENTANG ASRI ⚡');
        $response->assertSee('Hunian nyaman, asri, dan aman.');
        $response->assertSee('Menjadi kost terpercaya di Semarang.');
        $response->assertSee('Menjaga ketertiban kost.');
        $response->assertSee('Memberikan servis terbaik.');
        $response->assertSee('Fasilitas super lengkap.');
        $response->assertSee('Harga terjangkau mahasiswa.');
    }
}
