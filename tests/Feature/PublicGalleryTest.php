<?php

namespace Tests\Feature;

use App\Models\Gallery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicGalleryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the public gallery page renders successfully.
     */
    public function test_gallery_page_renders_successfully(): void
    {
        $response = $this->get(route('landing.galeri'));

        $response->assertStatus(200);
        $response->assertSee('GALERI FOTO KOST ASRI');
    }

    /**
     * Test that the gallery page displays active galleries and hides inactive ones.
     */
    public function test_gallery_page_displays_active_galleries(): void
    {
        // Create active gallery
        Gallery::create([
            'judul' => 'Kamar VIP Super Mewah',
            'deskripsi' => 'Deskripsi kamar VIP super mewah terupdate.',
            'foto' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80',
            'urutan' => 1,
            'is_active' => true,
        ]);

        // Create inactive gallery
        Gallery::create([
            'judul' => 'Kamar Standar Tersembunyi',
            'deskripsi' => 'Deskripsi kamar standar tersembunyi yang disembunyikan.',
            'foto' => 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80',
            'urutan' => 2,
            'is_active' => false,
        ]);

        $response = $this->get(route('landing.galeri'));

        $response->assertStatus(200);
        $response->assertSee('Kamar VIP Super Mewah');
        $response->assertSee('Deskripsi kamar VIP super mewah terupdate.');
        $response->assertDontSee('Kamar Standar Tersembunyi');
    }

    /**
     * Test that the gallery page displays fallback static boxes when no galleries exist.
     */
    public function test_gallery_page_displays_fallback_when_empty(): void
    {
        // Ensure no galleries exist
        Gallery::query()->delete();

        $response = $this->get(route('landing.galeri'));

        $response->assertStatus(200);
        // Fallback static boxes check
        $response->assertSee('Kamar VIP Eksklusif');
        $response->assertSee('Kamar Deluxe Nyaman');
        $response->assertSee('Kamar Standar Fungsional');
    }

    /**
     * Test that the gallery page contains the booking CTA section and Lightbox elements.
     */
    public function test_gallery_page_contains_booking_cta_and_lightbox(): void
    {
        $response = $this->get(route('landing.galeri'));

        $response->assertStatus(200);
        
        // Assert CTA Section rendering
        $response->assertSee('Tertarik Menginap di Kost Asri?');
        $response->assertSee('Hubungi via WhatsApp 💬', false);
        
        // Assert Lightbox elements presence
        $response->assertSee('id="gallery-lightbox"', false);
        $response->assertSee('id="lightbox-img"', false);
        $response->assertSee('id="lightbox-title"', false);
        $response->assertSee('id="lightbox-desc"', false);
        $response->assertSee('id="lightbox-wa-btn"', false);
        $response->assertSee('id="lightbox-booking-btn"', false);
    }
}
