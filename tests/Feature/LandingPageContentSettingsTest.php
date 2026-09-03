<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageContentSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin.settings@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $this->user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa.settings@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
    }

    public function test_admin_bisa_mengakses_halaman_pengaturan_konten(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.settings.edit'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Konten Landing Page');
        $response->assertSee('Identitas & Branding Kost', false);
    }

    public function test_non_admin_tidak_bisa_mengakses_halaman_pengaturan_konten(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.settings.edit'));

        $response->assertStatus(403);
    }

    public function test_admin_bisa_mengupdate_pengaturan_konten(): void
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
            'about_title' => 'Profil Terbaru Asri',
            'about_description' => 'Kost eksklusif bergaya modern di Semarang.',
            'about_visi' => 'Menjadi hunian ternyaman se-Jateng.',
            'about_misi_1' => 'Kebersihan adalah prioritas.',
            'about_misi_2' => 'Keamanan terjamin.',
            'about_misi_3' => 'Kamar mandi dalam.',
            'about_misi_4' => 'Free WiFi 100 Mbps.',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('settings', [
            'key' => 'logo_text',
            'value' => 'Kost Asri Modern',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'logo_icon',
            'value' => '🏢',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'contact_whatsapp',
            'value' => '62895330031313',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'google_maps_embed',
            'value' => '<iframe src="https://www.google.com/maps/embed" width="100%" height="100%" loading="lazy" style="border:0;" class="w-full h-full"></iframe>',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'bank_name',
            'value' => 'Bank Mandiri Baru',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'bank_account_number',
            'value' => '987-654-3210',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'bank_account_owner',
            'value' => 'Pemilik Kost Baru',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'about_title',
            'value' => 'Profil Terbaru Asri',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'about_description',
            'value' => 'Kost eksklusif bergaya modern di Semarang.',
        ]);

        // Verifikasi perubahan tampil di landing page
        $landingResponse = $this->get(route('landing.index'));
        $landingResponse->assertStatus(200);
        $landingResponse->assertSee('Kost Asri Modern');
        $landingResponse->assertSee('🏢');
        $landingResponse->assertSee('✨ Hunian Termewah');
        $landingResponse->assertSee('SELAMAT DATANG DI KOST ASRI');
        $landingResponse->assertSee('Kost elite nomor satu.');
        $landingResponse->assertSee('Jl. Baru No. 12');
        $landingResponse->assertSee('admin@kostasrimodern.com');

        // Verifikasi perubahan tampil di tentang-kami page
        $aboutResponse = $this->get(route('landing.tentangKami'));
        $aboutResponse->assertStatus(200);
        $aboutResponse->assertSee('PROFIL TERBARU ASRI ⚡');
        $aboutResponse->assertSee('Kost eksklusif bergaya modern di Semarang.');
        $aboutResponse->assertSee('Menjadi hunian ternyaman se-Jateng.');
        $aboutResponse->assertSee('Kebersihan adalah prioritas.');
        $aboutResponse->assertSee('Keamanan terjamin.');
        $aboutResponse->assertSee('Kamar mandi dalam.');
        $aboutResponse->assertSee('Free WiFi 100 Mbps.');
    }

    public function test_validation_saat_update_pengaturan_konten(): void
    {
        $payload = [
            'logo_text' => '', // Empty
            'logo_icon' => '🏢',
            'hero_tagline' => '✨ Hunian Termewah',
            'hero_title' => 'Selamat Datang di Kost Asri',
            'hero_description' => 'Kost elite nomor satu.',
            'contact_address' => 'Jl. Baru No. 12',
            'contact_whatsapp' => 'invalid-wa-format', // Invalid
            'contact_email' => 'invalid-email', // Invalid
            'google_maps_embed' => '', // Empty
            'bank_name' => '', // Empty
            'bank_account_number' => '', // Empty
            'bank_account_owner' => '', // Empty
            'about_title' => '',
            'about_description' => '',
            'about_visi' => '',
            'about_misi_1' => '',
            'about_misi_2' => '',
            'about_misi_3' => '',
            'about_misi_4' => '',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $payload);

        $response->assertSessionHasErrors([
            'logo_text', 'contact_whatsapp', 'contact_email', 'google_maps_embed', 
            'bank_name', 'bank_account_number', 'bank_account_owner',
            'about_title', 'about_description', 'about_visi',
            'about_misi_1', 'about_misi_2', 'about_misi_3', 'about_misi_4'
        ]);
    }

    public function test_whatsapp_disanitasi_sebelum_validasi(): void
    {
        $payload = [
            'logo_text' => 'Kost Asri Modern',
            'logo_icon' => '🏢',
            'hero_tagline' => '✨ Hunian Termewah',
            'hero_title' => 'Selamat Datang di Kost Asri',
            'hero_description' => 'Kost elite nomor satu.',
            'contact_address' => 'Jl. Baru No. 12',
            'contact_whatsapp' => '+62 895-3300-31313', // Has spaces, dashes, and +
            'contact_email' => 'admin@kostasrimodern.com',
            'google_maps_embed' => '<iframe src="https://www.google.com/maps/embed"></iframe>',
            'bank_name' => 'Bank Mandiri Baru',
            'bank_account_number' => '987-654-3210',
            'bank_account_owner' => 'Pemilik Kost Baru',
            'about_title' => 'Tentang Kami',
            'about_description' => 'Kost Asri Boarding House adalah hunian eksklusif...',
            'about_visi' => 'Menjadi pelopor...',
            'about_misi_1' => 'Misi 1',
            'about_misi_2' => 'Misi 2',
            'about_misi_3' => 'Misi 3',
            'about_misi_4' => 'Misi 4',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings.edit'));
        $response->assertSessionHas('success');

        // Check if saved as sanitized digits
        $this->assertDatabaseHas('settings', [
            'key' => 'contact_whatsapp',
            'value' => '62895330031313',
        ]);
    }

    public function test_google_maps_embed_validasi_menolak_format_tidak_valid(): void
    {
        // 1. Script tag (stored XSS attempt)
        $payloadXss = [
            'logo_text' => 'Kost Asri Modern',
            'logo_icon' => '🏢',
            'hero_tagline' => '✨ Hunian Termewah',
            'hero_title' => 'Selamat Datang di Kost Asri',
            'hero_description' => 'Kost elite nomor satu.',
            'contact_address' => 'Jl. Baru No. 12',
            'contact_whatsapp' => '0895330031313',
            'contact_email' => 'admin@kostasrimodern.com',
            'google_maps_embed' => '<script>alert("hack")</script>', // XSS
            'bank_name' => 'Bank Mandiri Baru',
            'bank_account_number' => '987-654-3210',
            'bank_account_owner' => 'Pemilik Kost Baru',
            'about_title' => 'Tentang Kami',
            'about_description' => 'Kost Asri Boarding House adalah hunian eksklusif...',
            'about_visi' => 'Menjadi pelopor...',
            'about_misi_1' => 'Misi 1',
            'about_misi_2' => 'Misi 2',
            'about_misi_3' => 'Misi 3',
            'about_misi_4' => 'Misi 4',
        ];

        $response1 = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $payloadXss);
        $response1->assertSessionHasErrors(['google_maps_embed']);

        // 2. Iframe but not Google Maps
        $payloadNonGoogle = [
            'logo_text' => 'Kost Asri Modern',
            'logo_icon' => '🏢',
            'hero_tagline' => '✨ Hunian Termewah',
            'hero_title' => 'Selamat Datang di Kost Asri',
            'hero_description' => 'Kost elite nomor satu.',
            'contact_address' => 'Jl. Baru No. 12',
            'contact_whatsapp' => '0895330031313',
            'contact_email' => 'admin@kostasrimodern.com',
            'google_maps_embed' => '<iframe src="https://attacker.com/evil-map"></iframe>', // Non google map
            'bank_name' => 'Bank Mandiri Baru',
            'bank_account_number' => '987-654-3210',
            'bank_account_owner' => 'Pemilik Kost Baru',
            'about_title' => 'Tentang Kami',
            'about_description' => 'Kost Asri Boarding House adalah hunian eksklusif...',
            'about_visi' => 'Menjadi pelopor...',
            'about_misi_1' => 'Misi 1',
            'about_misi_2' => 'Misi 2',
            'about_misi_3' => 'Misi 3',
            'about_misi_4' => 'Misi 4',
        ];

        $response2 = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $payloadNonGoogle);
        $response2->assertSessionHasErrors(['google_maps_embed']);
    }

    public function test_google_maps_embed_menolak_domain_bypass_xss(): void
    {
        // 1. Attack via manipulated domain
        $payloadDomainBypass = [
            'logo_text' => 'Kost Asri Modern',
            'logo_icon' => '🏢',
            'hero_tagline' => '✨ Hunian Termewah',
            'hero_title' => 'Selamat Datang di Kost Asri',
            'hero_description' => 'Kost elite.',
            'contact_address' => 'Jl. Baru No. 12',
            'contact_whatsapp' => '0895330031313',
            'contact_email' => 'admin@kostasrimodern.com',
            'google_maps_embed' => '<iframe src="https://google.com.attacker-domain.dev/maps/embed"></iframe>',
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '123456',
            'bank_account_owner' => 'Owner',
            'about_title' => 'Tentang Kami',
            'about_description' => 'Deskripsi',
            'about_visi' => 'Visi',
            'about_misi_1' => 'Misi 1',
            'about_misi_2' => 'Misi 2',
            'about_misi_3' => 'Misi 3',
            'about_misi_4' => 'Misi 4',
        ];

        $response1 = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $payloadDomainBypass);
        $response1->assertSessionHasErrors(['google_maps_embed']);

        // 2. Attack via event handler injection
        $payloadOnloadBypass = [
            'logo_text' => 'Kost Asri Modern',
            'logo_icon' => '🏢',
            'hero_tagline' => '✨ Hunian Termewah',
            'hero_title' => 'Selamat Datang di Kost Asri',
            'hero_description' => 'Kost elite.',
            'contact_address' => 'Jl. Baru No. 12',
            'contact_whatsapp' => '0895330031313',
            'contact_email' => 'admin@kostasrimodern.com',
            'google_maps_embed' => '<iframe src="https://www.google.com/maps/embed" onload="alert(1)"></iframe>',
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '123456',
            'bank_account_owner' => 'Owner',
            'about_title' => 'Tentang Kami',
            'about_description' => 'Deskripsi',
            'about_visi' => 'Visi',
            'about_misi_1' => 'Misi 1',
            'about_misi_2' => 'Misi 2',
            'about_misi_3' => 'Misi 3',
            'about_misi_4' => 'Misi 4',
        ];

        $response2 = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $payloadOnloadBypass);
        $response2->assertSessionHasErrors(['google_maps_embed']);
    }

    public function test_google_maps_embed_normalisasi_otomatis_atribut(): void
    {
        $payload = [
            'logo_text' => 'Kost Asri Modern',
            'logo_icon' => '🏢',
            'hero_tagline' => '✨ Hunian Termewah',
            'hero_title' => 'Selamat Datang di Kost Asri',
            'hero_description' => 'Kost elite.',
            'contact_address' => 'Jl. Baru No. 12',
            'contact_whatsapp' => '0895330031313',
            'contact_email' => 'admin@kostasrimodern.com',
            'google_maps_embed' => '<iframe src="https://www.google.com/maps/embed?pb=123" width="600" height="450" title="Peta Kost"></iframe>',
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '123456',
            'bank_account_owner' => 'Owner',
            'about_title' => 'Tentang Kami',
            'about_description' => 'Deskripsi',
            'about_visi' => 'Visi',
            'about_misi_1' => 'Misi 1',
            'about_misi_2' => 'Misi 2',
            'about_misi_3' => 'Misi 3',
            'about_misi_4' => 'Misi 4',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings.edit'));
        
        $savedValue = Setting::get('google_maps_embed');
        
        // Assert standardized attributes are present
        $this->assertStringContainsString('width="100%"', $savedValue);
        $this->assertStringContainsString('height="100%"', $savedValue);
        $this->assertStringContainsString('loading="lazy"', $savedValue);
        $this->assertStringContainsString('class="w-full h-full"', $savedValue);
        $this->assertStringContainsString('style="border:0;"', $savedValue);
        
        // Assert other attributes are preserved/cleaned
        $this->assertStringContainsString('title="Peta Kost"', $savedValue);
    }

    public function test_setting_save_invalidates_cache(): void
    {
        \Illuminate\Support\Facades\Cache::shouldReceive('forget')
            ->with('setting:logo_text')
            ->atLeast()->once();

        Setting::updateOrCreate(
            ['key' => 'logo_text'],
            ['value' => 'New Logo Text']
        );
    }

    public function test_setting_delete_invalidates_cache(): void
    {
        \Illuminate\Support\Facades\Cache::shouldReceive('forget')
            ->with('setting:logo_text')
            ->atLeast()->once();

        $setting = Setting::updateOrCreate(
            ['key' => 'logo_text'],
            ['value' => 'New Logo Text']
        );

        $setting->delete();
    }
}
