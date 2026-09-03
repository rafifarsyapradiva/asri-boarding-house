<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PenyewaTagihanTest extends TestCase
{
    use RefreshDatabase;

    private User $activeTenantUser;
    private User $inactiveTenantUser;
    private User $otherTenantUser;
    private User $adminUser;
    private Kamar $kamar1;
    private Kamar $kamar2;
    private Penyewa $penyewaActive;
    private Penyewa $penyewaOther;

    protected function setUp(): void
    {
        parent::setUp();

        // Isolasi HTTP requests (Midtrans & Fonnte WA API)
        Http::fake([
            'api.fonnte.com/*' => Http::response([
                'status' => true,
                'message' => 'Pesan terisolasi oleh Fonnte Mock',
            ], 200),
            '*midtrans.com/*' => Http::response([
                'token' => 'mock-snap-token-xyz123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/mock-snap-token-xyz123',
            ], 200),
        ]);

        // Setup Kamar
        $this->kamar1 = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'terisi',
        ]);

        $this->kamar2 = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'terisi',
        ]);

        // Setup Users
        $this->activeTenantUser = User::create([
            'nama' => 'Penyewa Aktif Budi',
            'email' => 'budi.aktif@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567891',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $this->inactiveTenantUser = User::create([
            'nama' => 'Penyewa Non-Aktif',
            'email' => 'tidak.aktif@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567892',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $this->otherTenantUser = User::create([
            'nama' => 'Penyewa Lain',
            'email' => 'lain@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567893',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $this->adminUser = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin.billing@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567894',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        // Bind Penyewa Profiles
        $this->penyewaActive = Penyewa::create([
            'user_id' => $this->activeTenantUser->id,
            'kamar_id' => $this->kamar1->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => Carbon::now()->toDateString(),
            'nama_wali' => 'Wali Budi',
            'no_wali' => '081122334455',
            'deposit' => 200000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        $this->penyewaOther = Penyewa::create([
            'user_id' => $this->otherTenantUser->id,
            'kamar_id' => $this->kamar2->id,
            'nik' => '1234567890123458',
            'tanggal_masuk' => Carbon::now()->toDateString(),
            'nama_wali' => 'Wali Lain',
            'no_wali' => '081122334456',
            'deposit' => 200000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        Penyewa::create([
            'user_id' => $this->inactiveTenantUser->id,
            'kamar_id' => $this->kamar1->id,
            'nik' => '1234567890123457',
            'tanggal_masuk' => Carbon::now()->toDateString(),
            'nama_wali' => 'Wali Non-Aktif',
            'no_wali' => '081122334457',
            'deposit' => 200000,
            'status' => 'nonaktif',
            'tanggal_billing' => 1,
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);
    }

    /**
     * Test active tenant can access their bills index page.
     */
    public function test_active_tenant_can_access_billing_index(): void
    {
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewaActive->id,
            'order_id' => 'TGH-BUDI-101',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->activeTenantUser)
            ->get(route('penyewa.tagihan.index'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Tagihan Sewa');
        $response->assertSee('TGH-BUDI-101');
    }

    /**
     * Test inactive tenant can access billing index.
     */
    public function test_inactive_tenant_can_access_billing_index(): void
    {
        $response = $this->actingAs($this->inactiveTenantUser)
            ->get(route('penyewa.tagihan.index'));

        $response->assertStatus(200);
    }

    /**
     * Test guest is redirected to login.
     */
    public function test_guest_cannot_access_billing_index(): void
    {
        $response = $this->get(route('penyewa.tagihan.index'));

        $response->assertRedirect(route('penyewa.login'));
    }

    /**
     * Test active tenant can view details of their own bill.
     */
    public function test_active_tenant_can_view_own_bill_detail(): void
    {
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewaActive->id,
            'order_id' => 'TGH-BUDI-102',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->activeTenantUser)
            ->get(route('penyewa.tagihan.show', $tagihan->id));

        $response->assertStatus(200);
        $response->assertSee('TGH-BUDI-102');
        $response->assertSee('Bayar Online Sekarang');
    }

    /**
     * Test tenant cannot view other tenant's bill.
     */
    public function test_tenant_cannot_view_other_tenant_bill(): void
    {
        $tagihanLain = Tagihan::create([
            'penyewa_id' => $this->penyewaOther->id,
            'order_id' => 'TGH-LAIN-101',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->activeTenantUser)
            ->get(route('penyewa.tagihan.show', $tagihanLain->id));

        $response->assertStatus(403);
    }

    /**
     * Test active tenant can request Midtrans snap token for payment.
     */
    public function test_active_tenant_can_generate_snap_token(): void
    {
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewaActive->id,
            'order_id' => 'TGH-BUDI-103',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->activeTenantUser)
            ->post(route('penyewa.pembayaran.token', $tagihan->id));

        $response->assertStatus(200);
        $this->assertEquals('mock-snap-token-xyz123', $response->json('snap_token'));
        $this->assertStringStartsWith('TGH-BUDI-103', $response->json('order_id'));
    }

    /**
     * Test active tenant can download invoice PDF for lunas bills.
     */
    public function test_active_tenant_can_download_invoice_pdf(): void
    {
        Storage::fake();

        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewaActive->id,
            'order_id' => 'TGH-BUDI-104',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'lunas',
        ]);

        $pembayaran = Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'transaction_id' => 'TRX-BUDI-104',
            'payment_type' => 'bank_transfer',
            'bank' => 'bca',
            'nominal' => 1000000,
            'status_midtrans' => 'settlement',
            'tanggal_bayar' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->activeTenantUser)
            ->get(route('penyewa.nota.download', $pembayaran->id));

        $response->assertStatus(200);
        $this->assertTrue($response->headers->get('content-type') === 'application/pdf' || 
                           str_contains($response->headers->get('content-disposition'), '.pdf'));
        
        // Assert file was saved to storage
        $pembayaran->refresh();
        $this->assertNotEmpty($pembayaran->pdf_path);
        Storage::assertExists('public/' . $pembayaran->pdf_path);
    }

    /**
     * Test tenant cannot generate snap token for another tenant's bill (Anti-IDOR).
     */
    public function test_tenant_cannot_generate_snap_token_for_other_tenant_bill(): void
    {
        $tagihanLain = Tagihan::create([
            'penyewa_id' => $this->penyewaOther->id,
            'order_id' => 'TGH-LAIN-102',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->activeTenantUser)
            ->post(route('penyewa.pembayaran.token', $tagihanLain->id));

        $response->assertStatus(403);
    }

    /**
     * Test tenant cannot generate snap token for another user's reservation (Anti-IDOR).
     */
    public function test_tenant_cannot_generate_snap_token_for_other_user_reservation(): void
    {
        $reservasiLain = \App\Models\Reservasi::create([
            'user_id' => $this->otherTenantUser->id,
            'kamar_id' => $this->kamar2->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => Carbon::now()->addDays(5)->toDateString(),
            'tanggal_selesai' => Carbon::now()->addDays(35)->toDateString(),
            'durasi' => 1,
            'total_harga' => 1000000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->activeTenantUser)
            ->post(route('reservasi.pembayaran.token', $reservasiLain->id));

        $response->assertStatus(403);
    }

    /**
     * Test that deposit is not wiped when late payment denda is applied.
     */
    public function test_deposit_is_not_wiped_when_denda_is_applied(): void
    {
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewaActive->id,
            'order_id' => 'TGH-BUDI-DEPOSIT',
            'periode_bulan' => Carbon::now()->subMonth()->month,
            'periode_tahun' => Carbon::now()->subMonth()->year,
            'tanggal_tagihan' => Carbon::now()->subMonth()->startOfMonth()->toDateString(),
            'tanggal_jatuh_tempo' => Carbon::now()->subMonth()->startOfMonth()->addDays(9)->toDateString(),
            'nominal_pokok' => 1000000,
            'nominal_total' => 1200000, // Includes 200,000 deposit
            'status' => 'pending',
        ]);

        // Run the overdue fine calculation process
        app(\App\Services\BillingService::class)->prosesKeterlambatan();

        $tagihan->refresh();
        
        // Assert denda is 5% of nominal_pokok (1,000,000 * 5% = 50,000)
        $this->assertEquals(50000, $tagihan->nominal_denda);
        
        // Assert nominal_total has accumulated the denda (1,200,000 + 50,000 = 1,250,000)
        // If the bug was active, it would have reset the total to 1,050,000, losing the deposit.
        $this->assertEquals(1250000, $tagihan->nominal_total);
    }
}
