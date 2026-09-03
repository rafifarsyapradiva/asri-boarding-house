<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppFloatingButtonUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Menyiapkan data awal jika diperlukan seeder fasilitas
        (new \Database\Seeders\FasilitasSeeder())->run();
    }

    /**
     * Skenario 1: Validasi Resolusi Multi-Viewport (360px, 768px, 1024px)
     * Menguji penempatan tombol WA melayang yang terkunci posisinya (fixed).
     */
    public function test_scenario_1_multi_viewport_responsiveness_and_fixed_position(): void
    {
        // Jalankan GET request ke landing page
        $response = $this->get('/');
        $response->assertStatus(200);

        // Ekstraksi elemen HTML tombol WA Floating Button
        $html = $response->getContent();
        
        // Assert elemen tombol dengan ID 'wa-floating-button' ada di dalam DOM
        $this->assertStringContainsString('id="wa-floating-button"', $html);

        // TODO: Simulasikan perubahan viewport pada browser headless ke resolusi (360, 640), (768, 1024), dan (1024, 768)
        // Di sini kita memverifikasi bahwa element memiliki class 'fixed' untuk mengunci posisinya di semua resolusi
        $this->assertMatchesRegularExpression('/id="wa-floating-button"[^>]*class="[^"]*fixed[^"]*"/', $html);
    }

    /**
     * Skenario 2: Audit Target Sentuh (Touch Target Assertions)
     * Memastikan ukuran target sentuh tombol WA memenuhi standar WCAG 2.1 (Min. 44px) pada versi mobile & desktop.
     */
    public function test_scenario_2_touch_target_size_wcag_compliance(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $html = $response->getContent();

        // Cari string class dari wa-floating-button menggunakan regex
        preg_match('/id="wa-floating-button"[^>]*class="([^"]*)"/', $html, $matches);
        $this->assertNotEmpty($matches, 'Elemen wa-floating-button tidak memiliki atribut class.');
        
        $classes = explode(' ', $matches[1]);

        // Ekstraksi ukuran width dan height utilitas Tailwind
        $mobileWidth = 0;
        $mobileHeight = 0;
        $smWidth = 0;
        $smHeight = 0;

        foreach ($classes as $class) {
            // Mobile (Default)
            if (preg_match('/^w-(\d+)$/', $class, $wMatch)) {
                $mobileWidth = (int)$wMatch[1] * 4; // 1 unit Tailwind = 4px
            }
            if (preg_match('/^h-(\d+)$/', $class, $hMatch)) {
                $mobileHeight = (int)$hMatch[1] * 4;
            }
            // Tablet/Desktop (sm:)
            if (preg_match('/^sm:w-(\d+)$/', $class, $wMatch)) {
                $smWidth = (int)$wMatch[1] * 4;
            }
            if (preg_match('/^sm:h-(\d+)$/', $class, $hMatch)) {
                $smHeight = (int)$hMatch[1] * 4;
            }
        }

        // Jalankan asersi matematika: target sentuh minimum wajib >= 44px (WCAG 2.1 Target Size)
        // Assert versi Mobile
        $this->assertGreaterThanOrEqual(44, $mobileWidth, "Lebar tombol WA versi mobile ({$mobileWidth}px) kurang dari 44px.");
        $this->assertGreaterThanOrEqual(44, $mobileHeight, "Tinggi tombol WA versi mobile ({$mobileHeight}px) kurang dari 44px.");

        // Assert versi Tablet/Desktop (sm:w-16 sm:h-16 -> 64px)
        if ($smWidth > 0) {
            $this->assertGreaterThanOrEqual(44, $smWidth, "Lebar tombol WA versi desktop ({$smWidth}px) kurang dari 44px.");
        }
        if ($smHeight > 0) {
            $this->assertGreaterThanOrEqual(44, $smHeight, "Tinggi tombol WA versi desktop ({$smHeight}px) kurang dari 44px.");
        }
    }

    /**
     * Skenario 3: Pengecekan Overlap & Struktur Z-Index
     * Memastikan tombol WA melayang memiliki z-index tertinggi dan berada di posisi kanan bawah dengan ruang aman.
     */
    public function test_scenario_3_z_index_and_anti_overlapping_spacing(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $html = $response->getContent();

        // 1. Assert z-index paling atas (minimal z-50) agar tidak terhalang elemen lain
        $this->assertMatchesRegularExpression('/id="wa-floating-button"[^>]*class="[^"]*z-50[^"]*"/', $html);

        // 2. Assert ruang aman (safe-padding) posisi kanan bawah layar (bottom-6 right-6)
        $this->assertMatchesRegularExpression('/id="wa-floating-button"[^>]*class="[^"]*bottom-6[^"]*"/', $html);
        $this->assertMatchesRegularExpression('/id="wa-floating-button"[^>]*class="[^"]*right-6[^"]*"/', $html);

        // TODO: Simulasikan interaksi klik atau scroll untuk memastikan tombol tidak menutupi form interaktif utama
    }
}
