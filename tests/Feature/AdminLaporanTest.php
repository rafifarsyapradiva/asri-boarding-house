<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Tagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLaporanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    private Kamar $kamar;
    private Penyewa $penyewa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin.laporan@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $this->user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa.laporan@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $this->kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $this->penyewa = Penyewa::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Dummy',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // Create a lunas tagihan
        Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-LUNAS',
            'periode_bulan' => 5,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-05-01',
            'tanggal_jatuh_tempo' => '2026-05-10',
            'nominal_pokok' => 1000000,
            'nominal_denda' => 0,
            'nominal_total' => 1000000,
            'status' => 'lunas',
            'metode_pembayaran' => 'cash',
        ]);

        // Create a pending tagihan (not late, since we set its due date to tomorrow)
        Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-PENDING',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => date('Y-m-d'),
            'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+5 days')),
            'nominal_pokok' => 1000000,
            'nominal_denda' => 0,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        // Create a late (terlambat) tagihan
        Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-LATE',
            'periode_bulan' => 4,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-04-01',
            'tanggal_jatuh_tempo' => '2026-04-10',
            'nominal_pokok' => 1000000,
            'nominal_denda' => 50000,
            'nominal_total' => 1050000,
            'status' => 'pending',
            'bulan_keterlambatan' => 1,
        ]);
    }

    public function test_admin_bisa_mengakses_halaman_laporan(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Penagihan Bulanan');
        $response->assertSee('TGH-LUNAS');
        $response->assertSee('TGH-PENDING');
        $response->assertSee('TGH-LATE');
    }

    public function test_non_admin_tidak_bisa_mengakses_halaman_laporan(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.laporan.index'));

        $response->assertStatus(403);
    }

    public function test_filter_laporan_berfungsi(): void
    {
        // Filter status lunas
        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.index', ['status' => 'lunas']));

        $response->assertStatus(200);
        $response->assertSee('TGH-LUNAS');
        $response->assertDontSee('TGH-PENDING');
        $response->assertDontSee('TGH-LATE');

        // Filter status pending (should only show non-late pending)
        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertDontSee('TGH-LUNAS');
        $response->assertSee('TGH-PENDING');
        $response->assertDontSee('TGH-LATE');

        // Filter status terlambat (should only show late pending)
        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.index', ['status' => 'terlambat']));

        $response->assertStatus(200);
        $response->assertDontSee('TGH-LUNAS');
        $response->assertDontSee('TGH-PENDING');
        $response->assertSee('TGH-LATE');

        // Filter bulan 5
        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.index', ['bulan' => 5]));

        $response->assertStatus(200);
        $response->assertSee('TGH-LUNAS');
        $response->assertDontSee('TGH-PENDING');
        $response->assertDontSee('TGH-LATE');
    }

    public function test_ekspor_pdf_berfungsi(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.exportPdf', ['status' => 'lunas']));

        $response->assertStatus(200);
        
        // Assert Content-Type is application/pdf
        $response->assertHeader('Content-Type', 'application/pdf');
        
        // Assert filename in Content-Disposition header
        $response->assertHeader('Content-Disposition', 'attachment; filename=laporan-keuangan.pdf');
    }

    public function test_ekspor_csv_berfungsi(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.exportExcel', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=laporan-keuangan.csv');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('TGH-PENDING', $content);
        $this->assertStringNotContainsString('TGH-LUNAS', $content);
        $this->assertStringNotContainsString('TGH-LATE', $content);
    }

    public function test_laporan_keuangan_menghitung_dp_dan_reservasi(): void
    {
        // Setup Reservasi dengan DP yang diselesaikan di bulan Juni 2026
        \App\Models\Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-07-01',
            'durasi' => 1,
            'total_harga' => 1000000,
            'status' => 'dp',
            'metode_pembayaran' => 'midtrans',
            'is_dp' => true,
            'nominal_dp' => 300000,
            'nominal_sisa' => 700000,
            'order_id' => 'RSV-DP-TEST',
            'transaction_id' => 'TRX-DP-TEST',
            'tanggal_konfirmasi' => '2026-06-02 10:00:00',
        ]);

        // Setup Reservasi Full Payment yang berstatus lunas di bulan Juni 2026
        \App\Models\Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => '2026-06-05',
            'tanggal_selesai' => '2026-07-05',
            'durasi' => 1,
            'total_harga' => 1000000,
            'status' => 'lunas',
            'metode_pembayaran' => 'midtrans',
            'is_dp' => false,
            'nominal_dp' => 0,
            'nominal_sisa' => 0,
            'order_id' => 'RSV-FULL-TEST',
            'transaction_id' => 'TRX-FULL-TEST',
            'tanggal_konfirmasi' => '2026-06-05 11:00:00',
        ]);

        // Panggil laporan bulan Juni 2026
        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.index', ['bulan' => 6, 'tahun' => 2026]));

        $response->assertStatus(200);

        // Kas masuk terhitung dari:
        // - Tagihan lunas periode Juni 2026: 0
        // - Reservasi DP Juni 2026: 300.000
        // - Reservasi Full Juni 2026: 1.000.000
        // Total = 1.300.000
        $response->assertViewHas('totalMasuk', 1300000);
    }

    public function test_filter_laporan_status_kadaluarsa(): void
    {
        // Buat tagihan kadaluarsa
        Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-EXPIRED',
            'periode_bulan' => 3,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-03-01',
            'tanggal_jatuh_tempo' => '2026-03-10',
            'nominal_pokok' => 1000000,
            'nominal_denda' => 0,
            'nominal_total' => 1000000,
            'status' => 'kadaluarsa',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.index', ['status' => 'kadaluarsa']));

        $response->assertStatus(200);
        $response->assertSee('TGH-EXPIRED');
        $response->assertDontSee('TGH-LUNAS');
        $response->assertDontSee('TGH-PENDING');
    }

    public function test_laporan_keuangan_menghitung_reservasi_full_payment_dikonfirmasi(): void
    {
        $newUser = User::create([
            'nama' => 'Penyewa Baru Laporan',
            'email' => 'penyewa.baru.laporan@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567899',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $newPenyewa = Penyewa::create([
            'user_id' => $newUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123459',
            'tanggal_masuk' => '2026-06-01',
            'status' => 'aktif',
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Dummy',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // Setup Reservasi Full Payment yang berstatus dikonfirmasi di bulan Juni 2026
        \App\Models\Reservasi::create([
            'user_id' => $newUser->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => '2026-06-05',
            'tanggal_selesai' => '2026-07-05',
            'durasi' => 1,
            'total_harga' => 1250000,
            'status' => 'dikonfirmasi',
            'metode_pembayaran' => 'midtrans',
            'is_dp' => false,
            'nominal_dp' => 0,
            'nominal_sisa' => 0,
            'order_id' => 'RSV-CONFIRMED-FULL',
            'transaction_id' => 'TRX-CONFIRMED-FULL',
            'tanggal_konfirmasi' => '2026-06-05 11:00:00',
        ]);

        // Since the reservation is confirmed, it should have a corresponding lunas tagihan (as created by the transition service)
        Tagihan::create([
            'penyewa_id' => $newPenyewa->id,
            'order_id' => 'TGH-RSV-CONFIRMED-FULL',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1250000,
            'nominal_total' => 1250000,
            'status' => 'lunas',
            'metode_pembayaran' => 'midtrans',
        ]);

        // Panggil laporan bulan Juni 2026
        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.index', ['bulan' => 6, 'tahun' => 2026]));

        $response->assertStatus(200);
        // Harus terhitung 1.250.000 karena sudah berstatus dikonfirmasi (melalui tagihan lunasnya)
        $response->assertViewHas('totalMasuk', 1250000);
    }

    public function test_ekspor_csv_memiliki_bom_utf8(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.exportExcel'));

        $response->assertStatus(200);
        $content = $response->streamedContent();
        
        // Assert content starts with UTF-8 BOM
        $this->assertEquals(0, strpos($content, "\xEF\xBB\xBF"));
    }
}
