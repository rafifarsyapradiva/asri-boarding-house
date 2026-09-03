<?php

namespace Tests\Feature;


use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Kamar;
use App\Models\Fasilitas;
use App\Models\Faq;
use App\Models\CustomerReview;
use App\Models\Gallery;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LandingViewsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_landing_page_renders_successfully()
    {
        $response = $this->get(route('landing.index'));

        $response->assertStatus(200);
        $response->assertViewIs('landing.index');
        $response->assertViewHas('logoText');
        $response->assertViewHas('waNumber');
    }

    #[Test]
    public function kamar_catalog_page_renders_successfully()
    {
        $kamar = Kamar::create([
            'nomor_kamar' => 'A101',
            'tipe' => 'vip',
            'status' => 'tersedia',
            'harga_bulan' => 1500000,
            'lantai' => 1,
            'luas_m2' => 20,
        ]);

        $response = $this->get(route('landing.kamar'));

        $response->assertStatus(200);
        $response->assertViewIs('landing.kamar-list');
        $response->assertSee('A101');
        $response->assertSee('VIP');
    }

    #[Test]
    public function kamar_detail_page_renders_successfully()
    {
        $kamar = Kamar::create([
            'nomor_kamar' => 'B202',
            'tipe' => 'deluxe',
            'status' => 'tersedia',
            'harga_bulan' => 1200000,
            'lantai' => 2,
            'luas_m2' => 16,
        ]);

        $response = $this->get(route('landing.show', $kamar->id));

        $response->assertStatus(200);
        $response->assertViewIs('landing.show');
        $response->assertSee('Kamar B202');
    }

    #[Test]
    public function faq_page_renders_successfully()
    {
        Faq::create([
            'pertanyaan' => 'Bagaimana cara booking?',
            'jawaban' => 'Klik tombol pesan unit pada katalog kamar.',
            'urutan' => 1,
            'is_active' => true,
        ]);

        $response = $this->get(route('landing.faq'));

        $response->assertStatus(200);
        $response->assertViewIs('landing.faq');
        $response->assertSee('Bagaimana cara booking?');
    }

    #[Test]
    public function fasilitas_page_renders_successfully()
    {
        Fasilitas::create([
            'nama' => 'Wi-Fi Cepat',
            'emoji' => '📶',
            'deskripsi' => 'Internet fiber optic 100Mbps.',
        ]);

        $response = $this->get(route('landing.fasilitas'));

        $response->assertStatus(200);
        $response->assertViewIs('landing.fasilitas');
        $response->assertSee('Wi-Fi Cepat');
    }

    #[Test]
    public function galeri_page_renders_successfully()
    {
        Gallery::create([
            'judul' => 'Kamar VIP Super',
            'deskripsi' => 'Fasilitas mewah lengkap.',
            'foto' => 'gallery/sample.jpg',
            'urutan' => 1,
            'is_active' => true,
        ]);

        $response = $this->get(route('landing.galeri'));

        $response->assertStatus(200);
        $response->assertViewIs('landing.galeri');
        $response->assertSee('Kamar VIP Super');
    }

    #[Test]
    public function cara_booking_page_renders_successfully()
    {
        $response = $this->get(route('landing.caraBooking'));

        $response->assertStatus(200);
        $response->assertViewIs('landing.cara-booking');
        $response->assertSee('CARA BOOKING KOST');
    }

    #[Test]
    public function tentang_kami_page_renders_successfully()
    {
        $response = $this->get(route('landing.tentangKami'));

        $response->assertStatus(200);
        $response->assertViewIs('landing.tentang-kami');
        $response->assertSee('PROFIL KOST ASRI');
    }

    #[Test]
    public function testimoni_page_renders_successfully()
    {
        CustomerReview::create([
            'nama' => 'Budi Santoso',
            'ulasan' => 'Kost sangat nyaman dan aman.',
            'bintang' => 5,
            'is_approved' => true,
        ]);

        $response = $this->get(route('landing.testimoni'));

        $response->assertStatus(200);
        $response->assertViewIs('landing.testimoni');
        $response->assertSee('Budi Santoso');
    }

    #[Test]
    public function kamar_card_component_renders_correctly()
    {
        $kamar = Kamar::make([
            'id' => 999,
            'nomor_kamar' => 'C303',
            'tipe' => 'standar',
            'status' => 'tersedia',
            'harga_bulan' => 900000,
            'lantai' => 3,
            'luas_m2' => 12,
        ]);

        $view = $this->blade('<x-landing.kamar-card :room="$room" cleanWa="62895330031313" />', [
            'room' => $kamar
        ]);

        $view->assertSee('Kamar C303');
        $view->assertSee('standar');
        $view->assertSee('✓ Tersedia');
        $view->assertSee('Pesan Unit →');
    }
}
