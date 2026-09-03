<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Kamar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FasilitasRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_desktop_facility_icon_renders_correct_emoji_on_public_pages(): void
    {
        // Clear landing cache before testing
        Cache::forget('fasilitas_aktif_landing');

        // Create a facility with 'desktop' icon
        $fasilitas = Fasilitas::create([
            'nama' => 'Meja Belajar',
            'ikon' => 'desktop',
            'deskripsi' => 'Meja belajar minimalis dengan kursi nyaman.',
            'is_active' => true,
        ]);

        // Create a room and attach this facility
        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'tersedia',
        ]);
        $kamar->fasilitas()->attach($fasilitas->id);

        // 1. Check facilities page (/fasilitas)
        $responseFasilitas = $this->get(route('landing.fasilitas'));
        $responseFasilitas->assertStatus(200);
        $responseFasilitas->assertSee('🖥️');

        // 2. Check homepage (/)
        $responseHome = $this->get(route('landing.index'));
        $responseHome->assertStatus(200);
        $responseHome->assertSee('🖥️');

        // 3. Check room details page (/kamar/{id})
        $responseDetail = $this->get(route('landing.show', $kamar->id));
        $responseDetail->assertStatus(200);
        $responseDetail->assertSee('🖥️');
    }
}
