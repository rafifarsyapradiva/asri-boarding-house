<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Fasilitas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KamarListRenderingTest extends TestCase
{
    use RefreshDatabase;

    private User $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // Inisialisasi User Penyewa
        $this->tenant = User::create([
            'nama' => 'Penyewa Digital',
            'email' => 'tenant.digital@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '6282219575575',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
    }

    /**
     * Test catalog page renders successfully for guest.
     */
    public function test_catalog_page_renders_successfully_for_guest(): void
    {
        // Setup room data
        $vipRoom = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'vip',
            'luas_m2' => 24.0,
            'harga_bulan' => 1400000,
            'status' => 'tersedia',
        ]);

        $deluxeRoom = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'deluxe',
            'luas_m2' => 18.0,
            'harga_bulan' => 950000,
            'status' => 'terisi',
        ]);

        $maintenanceRoom = Kamar::create([
            'nomor_kamar' => '103',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'maintenance',
        ]);

        // Access route as guest
        $response = $this->get(route('landing.kamar'));
        $response->assertStatus(200);

        // Assert title and key text elements
        $response->assertSee('KATALOG UNIT KAMAR');
        $response->assertSee('Semua Tipe');

        // Assert room status representations
        // Available room should show "Pesan Unit"
        $response->assertSee('Kamar 101');
        $response->assertSee('✓ Tersedia');
        $response->assertSee('Pesan Unit');

        // Occupied room should show "Tanya WA" or "TERISI"
        $response->assertSee('Kamar 102');
        $response->assertSee('TERISI');
        $response->assertSee('Tanya WA');

        // Maintenance room should not be displayed
        $response->assertDontSee('Kamar 103');
    }

    /**
     * Test catalog page renders successfully for authenticated tenant.
     */
    public function test_catalog_page_renders_successfully_for_authenticated_tenant(): void
    {
        $room = Kamar::create([
            'nomor_kamar' => '104',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'tersedia',
        ]);

        // Access route as logged in tenant
        $response = $this->actingAs($this->tenant)->get(route('landing.kamar'));
        $response->assertStatus(200);
        $response->assertSee('Kamar 104');
        $response->assertSee('Pesan Unit');
    }

    /**
     * Test that correct UI elements and filters are present.
     */
    public function test_catalog_page_contains_interactive_filter_elements(): void
    {
        $response = $this->get(route('landing.kamar'));
        $response->assertStatus(200);

        // Assert existence of category button IDs
        $response->assertSee('id="btn-cat-all"', false);
        $response->assertSee('id="btn-cat-vip"', false);
        $response->assertSee('id="btn-cat-deluxe"', false);
        $response->assertSee('id="btn-cat-standar"', false);

        // Assert search input ID
        $response->assertSee('id="kamar-search-input"', false);
        $response->assertSee('id="kamar-not-found"', false);
    }
}
