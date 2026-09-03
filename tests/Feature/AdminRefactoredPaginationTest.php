<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\Pengeluaran;
use App\Models\CustomerReview;
use App\Models\Keluhan;
use App\Models\GuestChatThread;
use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRefactoredPaginationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tenantUser;
    private Kamar $kamar;
    private Penyewa $penyewa;

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

        // 3. Create Kamar
        $this->kamar = Kamar::create([
            'nomor_kamar' => '201',
            'lantai' => 2,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'terisi'
        ]);

        // 4. Create Penyewa
        $this->penyewa = Penyewa::create([
            'user_id' => $this->tenantUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Budi',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);
    }

    /**
     * 1. Test pagination on the Laporan Keuangan module.
     */
    public function test_admin_laporan_keuangan_pagination_and_overall_summary(): void
    {
        // Create 15 unique tenant users, tenants and tagihan records (lunas) in June 2026 to see total masuk overall sum
        for ($i = 1; $i <= 15; $i++) {
            $user = User::create([
                'nama' => 'Penyewa Ke-' . $i,
                'email' => 'penyewa' . $i . '@example.com',
                'password' => bcrypt('password123'),
                'no_hp' => '628999999' . sprintf('%03d', $i),
                'role' => 'penyewa',
                'is_active' => 1,
            ]);

            $penyewa = Penyewa::create([
                'user_id' => $user->id,
                'kamar_id' => $this->kamar->id,
                'nik' => '1234567890123' . sprintf('%03d', $i),
                'tanggal_masuk' => date('Y-m-d'),
                'status' => 'aktif',
                'no_wali' => '081234567890',
                'nama_wali' => 'Wali Dummy',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
            ]);

            Tagihan::create([
                'penyewa_id' => $penyewa->id,
                'order_id' => 'TGH-LPR-' . $i,
                'periode_bulan' => 6,
                'periode_tahun' => 2026,
                'tanggal_tagihan' => '2026-06-01',
                'tanggal_jatuh_tempo' => '2026-06-10',
                'nominal_pokok' => 100000,
                'nominal_total' => 100000,
                'status' => 'lunas',
            ]);
        }

        // Create 12 pengeluaran records (nominal 50000 each)
        for ($i = 1; $i <= 12; $i++) {
            Pengeluaran::create([
                'tanggal_pengeluaran' => '2026-06-02',
                'nama_pengeluaran' => 'Pengeluaran Ke-' . $i,
                'kategori' => 'operasional',
                'nominal' => 50000,
                'keterangan' => 'Keterangan ' . $i
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.index', ['bulan' => 6, 'tahun' => 2026]));

        $response->assertStatus(200);

        // Verify overall summaries (Total Pemasukan: 1.5M, Total Pengeluaran: 600k, Saldo: 900k)
        $response->assertViewHas('totalMasuk', 1500000);
        $response->assertViewHas('totalKeluar', 600000);
        $response->assertViewHas('saldoBersih', 900000);

        // Verify independent pagination page parameters exist in HTML
        $response->assertSee('page_tagihan=2');
        $response->assertSee('page_pengeluaran=2');
    }

    /**
     * 2. Test pagination and search on Customer Reviews module.
     */
    public function test_admin_customer_reviews_pagination_and_search(): void
    {
        // Create 12 reviews
        for ($i = 1; $i <= 12; $i++) {
            CustomerReview::create([
                'nama' => ($i === 5) ? 'Reviewer Khusus' : 'Pelanggan Ke-' . $i,
                'pekerjaan' => 'Penyewa',
                'bintang' => 5,
                'ulasan' => 'Ulasan Kost Asri ' . $i,
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reviews.index'));

        $response->assertStatus(200);
        // Verify pagination (limit 10)
        $response->assertSee('Showing');
        $response->assertSee('10');
        $response->assertSee('12');
        $response->assertSee('?page=2');

        // Test search
        $responseSearch = $this->actingAs($this->admin)
            ->get(route('admin.reviews.index', ['search' => 'Khusus']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Reviewer Khusus');
        $responseSearch->assertDontSee('Pelanggan Ke-1');
    }

    /**
     * 3. Test pagination, filtering, and search on Keluhan module.
     */
    public function test_admin_keluhan_pagination_and_search(): void
    {
        // Create 12 keluhan
        for ($i = 1; $i <= 12; $i++) {
            Keluhan::create([
                'penyewa_id' => $this->penyewa->id,
                'judul' => ($i === 7) ? 'Keluhan Kamar Bocor Khusus' : 'Masalah Air Ke-' . $i,
                'kategori' => 'kamar',
                'deskripsi' => 'Deskripsi keluhan ' . $i,
                'status' => 'pending',
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.keluhan.index'));

        $response->assertStatus(200);
        // Verify pagination (limit 10)
        $response->assertSee('Showing');
        $response->assertSee('10');
        $response->assertSee('12');
        $response->assertSee('?page=2');

        // Test search
        $responseSearch = $this->actingAs($this->admin)
            ->get(route('admin.keluhan.index', ['search' => 'Bocor']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Keluhan Kamar Bocor Khusus');
        $responseSearch->assertDontSee('Masalah Air Ke-1');
    }

    /**
     * 4. Test pagination on Workspace Guest Chats threads AJAX API.
     */
    public function test_admin_guest_chats_threads_ajax_pagination(): void
    {
        // Create 12 guest chat threads
        for ($i = 1; $i <= 12; $i++) {
            GuestChatThread::create([
                'name' => 'Guest Ke-' . $i,
                'no_hp' => '08123456789' . $i,
                'session_token' => 'token-' . $i,
                'status' => 'active',
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->json('GET', route('admin.guest-chats.threads'));

        $response->assertStatus(200);

        // Verify JSON contains standard pagination keys
        $threadsData = $response->json('threads');
        $this->assertEquals(1, $threadsData['current_page']);
        $this->assertEquals(2, $threadsData['last_page']);
        $this->assertCount(10, $threadsData['data']);
        $this->assertEquals(12, $threadsData['total']);
    }

    /**
     * 5. Test pagination and search on FAQ module.
     */
    public function test_admin_faq_pagination_and_search(): void
    {
        // Create 12 FAQ entries
        for ($i = 1; $i <= 12; $i++) {
            Faq::create([
                'pertanyaan' => ($i === 8) ? 'Apakah bisa bayar harian khusus?' : 'Pertanyaan Ke-' . $i,
                'jawaban' => 'Jawaban Ke-' . $i,
                'urutan' => $i,
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.faq.index'));

        $response->assertStatus(200);
        // Verify pagination (limit 10)
        $response->assertSee('Showing');
        $response->assertSee('10');
        $response->assertSee('12');
        $response->assertSee('?page=2');

        // Test search
        $responseSearch = $this->actingAs($this->admin)
            ->get(route('admin.faq.index', ['search' => 'harian']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Apakah bisa bayar harian khusus?');
        $responseSearch->assertDontSee('Pertanyaan Ke-1');
    }
}
