<?php

namespace Tests\Feature;


use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

class LandingComponentsTest extends TestCase
{
    #[Test]
    public function cta_whatsapp_renders_default_content_safely()
    {
        Config::set('reservasi.admin_wa', '628999999999');

        $view = $this->blade('<x-landing.cta-whatsapp />');

        $view->assertSee('Tunggu Apa Lagi?');
        $view->assertSee('https://wa.me/628999999999', false);
    }

    #[Test]
    public function facility_card_renders_data_safely()
    {
        $facility = [
            'emoji' => '📶',
            'nama' => 'Wi-Fi Cepat',
            'deskripsi' => 'Koneksi hingga 100Mbps.'
        ];

        $view = $this->blade('<x-landing.facility-card :facility="$facility" />', ['facility' => $facility]);

        $view->assertSee('📶');
        $view->assertSee('Wi-Fi Cepat');
        $view->assertSee('Koneksi hingga 100Mbps.');
    }

    #[Test]
    public function kamar_card_handles_unloaded_facilities_relation_without_crashing()
    {
        $room = (object) [
            'id' => 10,
            'nomor_kamar' => 'A101',
            'tipe' => 'Deluxe',
            'luas_m2' => 18,
            'harga_bulan' => 1500000,
            'status' => 'tersedia',
            'foto_url' => 'https://example.com/room.jpg',
            'deskripsi' => 'Kamar bersih dan nyaman.',
            'lantai' => 1,
            'fasilitas' => null // Relasi null / tidak di-load
        ];

        $view = $this->blade('<x-landing.kamar-card :room="$room" />', ['room' => $room]);

        $view->assertSee('Kamar A101');
        $view->assertSee('Rp 1.500.000');
        $view->assertSee('Pesan Unit');
    }

    #[Test]
    public function review_card_handles_empty_name_and_missing_photo()
    {
        $review = (object) [
            'bintang' => 4,
            'ulasan' => 'Pelayanan ramah dan bersih.',
            'nama' => '',
            'foto' => null,
            'pekerjaan' => null
        ];

        $view = $this->blade('<x-landing.review-card :review="$review" />', ['review' => $review]);

        $view->assertSee('Pelayanan ramah dan bersih.');
        $view->assertSee('Penyewa Kost');
        $view->assertSee('A'); // Inisial default untuk nama kosong
    }

    #[Test]
    public function faq_item_renders_sanitized_question_and_answer()
    {
        $view = $this->blade('<x-landing.faq-item index="0" pertanyaan="Apakah bisa bulanan?" jawaban="Ya, pembayaran fleksibel." />');

        $view->assertSee('Apakah bisa bulanan?');
        $view->assertSee('Ya, pembayaran fleksibel.');
    }
}
