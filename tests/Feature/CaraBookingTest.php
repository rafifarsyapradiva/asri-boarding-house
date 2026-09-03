<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaraBookingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the booking guide page renders successfully and contains the correct contents.
     */
    public function test_booking_guide_page_renders_successfully(): void
    {
        $response = $this->get(route('landing.caraBooking'));

        $response->assertStatus(200);

        // Verify key headers and step descriptions
        $response->assertSee('CARA BOOKING KOST');
        $response->assertSee('Pilih & Cek Kamar');
        $response->assertSee('Isi Data Diri');
        $response->assertSee('Bayar Instan');
        $response->assertSee('Terima Nota');

        // Verify FAQs
        $response->assertSee('Bagaimana cara pembayaran DP atau Pelunasan?');
        $response->assertSee('Berapa lama batas waktu pembayaran setelah mengisi formulir?');
        $response->assertSee('Apakah saya bisa melakukan pembatalan booking?');
        $response->assertSee('Bagaimana cara mendapatkan Kode Akses Kamar digital?');

        // Verify CTA links
        $response->assertSee(route('landing.kamar'));
        $response->assertSee('https://wa.me/');
    }
}
