<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FasilitasCacheTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that changing a facility clears the landing page facilities cache.
     */
    public function test_perubahan_fasilitas_mengosongkan_cache_landing_page(): void
    {
        // Simpan data dummy ke dalam cache
        Cache::remember('fasilitas_aktif_landing', 3600, function () {
            return collect([
                ['nama' => 'WiFi Old', 'ikon' => 'wifi']
            ]);
        });

        // Simulasi perubahan data (buat baru atau update) pada model Fasilitas
        Fasilitas::create([
            'nama' => 'WiFi New',
            'ikon' => 'wifi-icon',
            'deskripsi' => 'Super fast internet',
            'is_active' => true,
        ]);

        // Assert bahwa cache didelete/bernilai null setelah perubahan data fasilitas
        $this->assertNull(Cache::get('fasilitas_aktif_landing'));
    }
}
