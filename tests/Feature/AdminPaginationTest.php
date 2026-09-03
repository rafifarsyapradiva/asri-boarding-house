<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Fasilitas;
use App\Models\Penyewa;
use App\Models\Tagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AdminPaginationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tenantUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Admin
        $this->admin = User::create([
            'nama' => 'Admin Asep',
            'email' => 'asri-asep@gmail.com',
            'password' => bcrypt('asriasep48'),
            'no_hp' => '6281234567890',
            'role' => 'admin',
            'require_password_change' => false,
            'is_active' => 1,
        ]);

        // 2. Create Tenant User
        $this->tenantUser = User::create([
            'nama' => 'Tenant Budi',
            'email' => 'tenant-budi@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '6281234567891',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
    }

    /**
     * Test pagination on the admin dashboard for latest tagihan.
     */
    public function test_admin_dashboard_tagihan_pagination(): void
    {
        // Create a room and tenant to associate with tagihan
        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'terisi'
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $this->tenantUser->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Budi',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // Create 15 tagihan records with unique monthly periods to respect database unique composite constraint
        for ($i = 1; $i <= 15; $i++) {
            Tagihan::create([
                'penyewa_id' => $penyewa->id,
                'order_id' => 'TGH-PAG-' . $i,
                'periode_bulan' => ($i % 12) + 1,
                'periode_tahun' => 2020 + (int)($i / 12),
                'tanggal_tagihan' => '2026-06-01',
                'tanggal_jatuh_tempo' => '2026-06-10',
                'nominal_pokok' => 1000000,
                'nominal_total' => 1000000,
                'status' => 'pending',
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Page 1 should show showing 1 to 10
        $response->assertSee('Showing');
        $response->assertSee('1');
        $response->assertSee('10');
        $response->assertSee('of');
        $response->assertSee('15');
        // Check for page 2 link
        $response->assertSee('?page=2');
    }

    /**
     * Test pagination and server-side filtering on the kamar index page.
     */
    public function test_admin_kamar_pagination_and_filters(): void
    {
        // Create 15 standard chambers (some floor 1, some floor 2)
        for ($i = 1; $i <= 15; $i++) {
            Kamar::create([
                'nomor_kamar' => '10' . $i,
                'lantai' => ($i % 2 === 0) ? 2 : 1,
                'tipe' => ($i <= 5) ? 'vip' : 'standar',
                'luas_m2' => 12.0,
                'harga_bulan' => 1000000,
                'status' => ($i <= 8) ? 'tersedia' : 'terisi'
            ]);
        }

        // Test basic pagination (limit 12)
        $response = $this->actingAs($this->admin)
            ->get(route('admin.kamar.index'));

        $response->assertStatus(200);
        $response->assertSee('Showing');
        $response->assertSee('1');
        $response->assertSee('12');
        $response->assertSee('15');
        $response->assertSee('?page=2');

        // Test filtering by tipe (should show 5 vip rooms, no pagination links on first page since total is 5 <= 12)
        $responseFiltered = $this->actingAs($this->admin)
            ->get(route('admin.kamar.index', ['tipe' => 'vip']));

        $responseFiltered->assertStatus(200);
        $responseFiltered->assertSee('vip');
        $responseFiltered->assertDontSee('?page=2');

        // Test filtering by status and floor
        $responseMultiFiltered = $this->actingAs($this->admin)
            ->get(route('admin.kamar.index', ['status' => 'tersedia', 'lantai' => 1]));

        $responseMultiFiltered->assertStatus(200);
    }

    /**
     * Test pagination on the master fasilitas page and JSON output bypass.
     */
    public function test_admin_fasilitas_pagination_and_json(): void
    {
        // Create 15 facilities
        for ($i = 1; $i <= 15; $i++) {
            Fasilitas::create([
                'nama' => 'Fasilitas ' . $i,
                'ikon' => 'wifi',
                'deskripsi' => 'Deskripsi fasilitas ' . $i,
                'is_active' => true,
            ]);
        }

        // Test HTML response (limit 10)
        $response = $this->actingAs($this->admin)
            ->get(route('admin.fasilitas.index'));

        $response->assertStatus(200);
        $response->assertSee('Showing');
        $response->assertSee('1');
        $response->assertSee('10');
        $response->assertSee('15');
        $response->assertSee('?page=2');

        // Test JSON response (should bypass pagination and return all 15)
        $responseJson = $this->actingAs($this->admin)
            ->json('GET', route('admin.fasilitas.index'));

        $responseJson->assertStatus(200);
        $this->assertCount(15, $responseJson->json());
    }

    /**
     * Test pagination and search/filtering on the penyewa index page.
     */
    public function test_admin_penyewa_pagination_and_search(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'terisi'
        ]);

        // Create 15 tenant users and tenant records with unique phone numbers and emails to avoid conflict with seed data
        for ($i = 1; $i <= 15; $i++) {
            $user = User::create([
                'nama' => ($i === 5) ? 'Tenant Khusus' : 'Penyewa Ke-' . $i,
                'email' => 'penyewa.page.' . $i . '@example.com',
                'password' => bcrypt('password123'),
                'no_hp' => '628999999' . sprintf('%03d', $i),
                'role' => 'penyewa',
                'is_active' => 1,
            ]);

            Penyewa::create([
                'user_id' => $user->id,
                'kamar_id' => $kamar->id,
                'nik' => '1234567890123' . sprintf('%03d', $i),
                'tanggal_masuk' => date('Y-m-d'),
                'status' => ($i <= 8) ? 'aktif' : 'nonaktif',
                'no_wali' => '081234567890',
                'nama_wali' => 'Wali Dummy',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
            ]);
        }

        // Test basic pagination (limit 10)
        $response = $this->actingAs($this->admin)
            ->get(route('admin.penyewa.index'));

        $response->assertStatus(200);
        $response->assertSee('Showing');
        $response->assertSee('1');
        $response->assertSee('10');
        $response->assertSee('15');
        $response->assertSee('?page=2');

        // Test filtering by search term (search matches 'Khusus')
        $responseSearch = $this->actingAs($this->admin)
            ->get(route('admin.penyewa.index', ['search' => 'Khusus']));

        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Tenant Khusus');
        $responseSearch->assertDontSee('Penyewa Ke-1');

        // Test filtering by status
        $responseStatus = $this->actingAs($this->admin)
            ->get(route('admin.penyewa.index', ['status' => 'nonaktif']));

        $responseStatus->assertStatus(200);
        // There are 7 nonaktif tenants (15 - 8 = 7)
        $responseStatus->assertDontSee('?page=2');
    }

    /**
     * Test pagination and search/filtering on Admin Notifikasi (Global Notifications logs) index page.
     */
    public function test_admin_notifikasi_global_pagination_and_search(): void
    {
        // Non-admin / Guest rejection check
        $responseGuest = $this->get(route('admin.notifikasi.index'));
        $responseGuest->assertRedirect();

        $responseNonAdmin = $this->actingAs($this->tenantUser)
            ->get(route('admin.notifikasi.index'));
        $responseNonAdmin->assertStatus(403);

        // Create rooms and tenant
        $kamar = Kamar::create([
            'nomor_kamar' => 'N-101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'terisi'
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $this->tenantUser->id,
            'kamar_id' => $kamar->id,
            'nik' => '9876543210987654',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Dummy',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // Create another tenant to search for
        $anotherUser = User::create([
            'nama' => 'Penyewa Spesifik Search',
            'email' => 'spesifik.search@example.com',
            'password' => bcrypt('password123'),
            'no_hp' => '628999999999',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
        $anotherPenyewa = Penyewa::create([
            'user_id' => $anotherUser->id,
            'kamar_id' => $kamar->id,
            'nik' => '9876543210987655',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Dummy',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // Create 12 log entries
        for ($i = 1; $i <= 12; $i++) {
            \App\Models\LogNotifikasi::create([
                'penyewa_id' => ($i === 5) ? $anotherPenyewa->id : $penyewa->id,
                'channel' => ($i % 2 === 0) ? 'whatsapp' : 'email',
                'event' => 'tagihan_baru',
                'status' => ($i <= 6) ? 'sukses' : 'gagal',
                'pesan' => 'Pesan ke-' . $i,
            ]);
        }

        // Access as Admin
        $response = $this->actingAs($this->admin)
            ->get(route('admin.notifikasi.index'));
        $response->assertStatus(200);

        // Check strict pagination (limit 10)
        $response->assertSee('Showing');
        $response->assertSee('1');
        $response->assertSee('10');
        $response->assertSee('12');
        $response->assertSee('?page=2');

        // Test search filter
        $responseSearch = $this->actingAs($this->admin)
            ->get(route('admin.notifikasi.index', ['search' => 'Spesifik']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Penyewa Spesifik Search');
        
        // Assert state preservation
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\LogNotifikasi::create([
                'penyewa_id' => $anotherPenyewa->id,
                'channel' => 'whatsapp',
                'event' => 'tagihan_baru',
                'status' => 'sukses',
                'pesan' => 'Pesan Spesifik ke-' . $i,
            ]);
        }
        $responseSearchMulti = $this->actingAs($this->admin)
            ->get(route('admin.notifikasi.index', ['search' => 'Spesifik']));
        $responseSearchMulti->assertStatus(200);
        $responseSearchMulti->assertSee('search=Spesifik');
        $responseSearchMulti->assertSee('page=2');
    }

    /**
     * Test pagination and search/filtering on Admin Notifikasi Khusus (System Activity Log) index page.
     */
    public function test_admin_notifikasi_khusus_pagination_and_search(): void
    {
        $responseGuest = $this->get(route('admin.notifikasi-khusus.index'));
        $responseGuest->assertRedirect();

        $responseNonAdmin = $this->actingAs($this->tenantUser)
            ->get(route('admin.notifikasi-khusus.index'));
        $responseNonAdmin->assertStatus(403);

        // Create 12 system activity logs
        for ($i = 1; $i <= 12; $i++) {
            \App\Models\NotifikasiKhusus::create([
                'sumber' => ($i <= 6) ? 'reservasi' : 'tagihan',
                'tipe_aktivitas' => ($i === 5) ? 'aktivitas_khusus' : 'pendaftaran_baru',
                'deskripsi' => 'Deskripsi log aktivitas ke-' . $i,
                'user_id' => $this->admin->id,
                'data_detail' => ['test' => $i]
            ]);
        }

        // Access as Admin
        $response = $this->actingAs($this->admin)
            ->get(route('admin.notifikasi-khusus.index'));
        $response->assertStatus(200);

        // Check strict pagination (limit 10)
        $response->assertSee('Showing');
        $response->assertSee('1');
        $response->assertSee('10');
        $response->assertSee('12');
        $response->assertSee('?page=2');

        // Test search filter
        $responseSearch = $this->actingAs($this->admin)
            ->get(route('admin.notifikasi-khusus.index', ['search' => 'aktivitas_khusus']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('AKTIVITAS KHUSUS');

        // Test state preservation
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\NotifikasiKhusus::create([
                'sumber' => 'reservasi',
                'tipe_aktivitas' => 'aktivitas_khusus',
                'deskripsi' => 'Deskripsi khusus ' . $i,
                'user_id' => $this->admin->id,
            ]);
        }
        $responseSearchMulti = $this->actingAs($this->admin)
            ->get(route('admin.notifikasi-khusus.index', ['search' => 'khusus']));
        $responseSearchMulti->assertStatus(200);
        $responseSearchMulti->assertSee('search=khusus');
        $responseSearchMulti->assertSee('page=2');
    }

    /**
     * Test pagination and search/filtering on Peraturan index page.
     */
    public function test_admin_peraturan_pagination_and_search(): void
    {
        $responseGuest = $this->get(route('admin.peraturan.index'));
        $responseGuest->assertRedirect();

        $responseNonAdmin = $this->actingAs($this->tenantUser)
            ->get(route('admin.peraturan.index'));
        $responseNonAdmin->assertStatus(403);

        // Create 12 Peraturan entries
        for ($i = 1; $i <= 12; $i++) {
            \App\Models\Peraturan::create([
                'judul' => ($i === 3) ? 'Aturan Khusus Merokok' : 'Aturan Kebersihan Ke-' . $i,
                'deskripsi' => 'Deskripsi aturan ke-' . $i,
                'ikon' => 'sparkles',
                'urutan' => $i,
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.peraturan.index'));
        $response->assertStatus(200);

        // Check strict pagination (limit 10)
        $response->assertSee('Showing');
        $response->assertSee('1');
        $response->assertSee('10');
        $response->assertSee('12');
        $response->assertSee('?page=2');

        // Test search filter
        $responseSearch = $this->actingAs($this->admin)
            ->get(route('admin.peraturan.index', ['search' => 'Merokok']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Aturan Khusus Merokok');

        // Test state preservation
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\Peraturan::create([
                'judul' => 'Aturan Khusus Tambahan ' . $i,
                'deskripsi' => 'Deskripsi ' . $i,
                'ikon' => 'sparkles',
                'urutan' => 20 + $i,
            ]);
        }
        $responseSearchMulti = $this->actingAs($this->admin)
            ->get(route('admin.peraturan.index', ['search' => 'Khusus']));
        $responseSearchMulti->assertStatus(200);
        $responseSearchMulti->assertSee('search=Khusus');
        $responseSearchMulti->assertSee('page=2');
    }

    /**
     * Test pagination and search/filtering on Gallery index page.
     */
    public function test_admin_gallery_pagination_and_search(): void
    {
        $responseGuest = $this->get(route('admin.gallery.index'));
        $responseGuest->assertRedirect();

        $responseNonAdmin = $this->actingAs($this->tenantUser)
            ->get(route('admin.gallery.index'));
        $responseNonAdmin->assertStatus(403);

        // Create 12 Gallery entries
        for ($i = 1; $i <= 12; $i++) {
            \App\Models\Gallery::create([
                'judul' => ($i === 4) ? 'Foto Kamar Khusus' : 'Suasana Kost Ke-' . $i,
                'deskripsi' => 'Deskripsi foto ke-' . $i,
                'foto' => 'galleries/dummy' . $i . '.jpg',
                'urutan' => $i,
                'is_active' => ($i <= 6),
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.gallery.index'));
        $response->assertStatus(200);

        // Check strict pagination (limit 10)
        $response->assertSee('Showing');
        $response->assertSee('1');
        $response->assertSee('10');
        $response->assertSee('12');
        $response->assertSee('?page=2');

        // Test search filter
        $responseSearch = $this->actingAs($this->admin)
            ->get(route('admin.gallery.index', ['search' => 'Kamar']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Foto Kamar Khusus');

        // Test status active filter
        $responseActive = $this->actingAs($this->admin)
            ->get(route('admin.gallery.index', ['status' => 'inactive']));
        $responseActive->assertStatus(200);
        $responseActive->assertDontSee('?page=2');

        // Test state preservation
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\Gallery::create([
                'judul' => 'Foto Khusus Tambahan ' . $i,
                'deskripsi' => 'Deskripsi ' . $i,
                'foto' => 'galleries/dummy_extra_' . $i . '.jpg',
                'urutan' => 20 + $i,
                'is_active' => true,
            ]);
        }
        $responseSearchMulti = $this->actingAs($this->admin)
            ->get(route('admin.gallery.index', ['search' => 'Khusus']));
        $responseSearchMulti->assertStatus(200);
        $responseSearchMulti->assertSee('search=Khusus');
        $responseSearchMulti->assertSee('page=2');
    }
}
