<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TenantLifecycleFixesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    private Kamar $kamar;
    private string $serverKey = 'dummy_server_key';

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('midtrans.server_key', $this->serverKey);

        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@asrikost.com',
            'password' => bcrypt('password'),
            'no_hp' => '081111111111',
            'role' => 'admin',
            'is_active' => 1,
            'require_password_change' => false,
        ]);

        $this->user = User::create([
            'nama' => 'Penyewa Andi',
            'email' => 'andi@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
            'require_password_change' => false,
            'nama_wali' => 'Wali Andi',
            'no_wali' => '081122334455',
            'nik' => '1234567890123456',
        ]);

        $this->kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);
    }

    /**
     * Test 1: Future billing prevention
     */
    public function test_future_tenant_billing_prevention(): void
    {
        // Setup a future tenant entering next month
        $futurePenyewa = Penyewa::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->addMonth()->startOfMonth()->toDateString(),
            'nama_wali' => 'Wali Andi',
            'no_wali' => '081122334455',
            'deposit' => 2000000,
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        $billingService = new BillingService();
        $billingService->generateTagihanBulanan();

        // There should be no bills created for this tenant for this current month
        $billsCount = Tagihan::where('penyewa_id', $futurePenyewa->id)
            ->where('periode_bulan', now()->month)
            ->where('periode_tahun', now()->year)
            ->count();

        $this->assertEquals(0, $billsCount);
    }

    /**
     * Test 2: Returning tenant registration
     */
    public function test_returning_tenant_registration_succeeds(): void
    {
        // 1. First tenancy
        $penyewa1 = Penyewa::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->subMonths(6)->toDateString(),
            'nama_wali' => 'Wali Andi',
            'no_wali' => '081122334455',
            'deposit' => 1000000,
            'status' => 'nonaktif', // Checked out
            'tipe_sewa' => 'bulanan',
            'durasi' => 6,
        ]);

        // 2. Returning tenancy using the same user_id and NIK
        $penyewa2 = null;
        try {
            $penyewa2 = Penyewa::create([
                'user_id' => $this->user->id,
                'kamar_id' => $this->kamar->id,
                'nik' => '1234567890123456',
                'tanggal_masuk' => now()->toDateString(),
                'nama_wali' => 'Wali Andi',
                'no_wali' => '081122334455',
                'deposit' => 1000000,
                'status' => 'aktif',
                'tipe_sewa' => 'bulanan',
                'durasi' => 3,
            ]);
        } catch (\Exception $e) {
            $this->fail('Failed to register returning tenant due to constraint exception: ' . $e->getMessage());
        }

        $this->assertNotNull($penyewa2);
        $this->assertEquals('aktif', $penyewa2->status);
    }

    /**
     * Test 3: Checkout validation is blocked by unpaid bills
     */
    public function test_checkout_validation_blocked_by_unpaid_bills(): void
    {
        $penyewa = Penyewa::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Andi',
            'no_wali' => '081122334455',
            'deposit' => 1000000,
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // Create an unpaid bill
        Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-ANDI-101',
            'periode_bulan' => now()->month,
            'periode_tahun' => now()->year,
            'tanggal_tagihan' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(9)->toDateString(),
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        // Act as admin
        $response = $this->actingAs($this->admin)
            ->post("/admin/penyewa/{$penyewa->id}/checkout", [
                'apakah_ada_kerusakan' => 0,
            ]);

        // Assert redirect with error
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals('aktif', $penyewa->fresh()->status);
    }

    /**
     * Test 4: Checkout validation succeeds and logs refund when bills paid
     */
    public function test_checkout_validation_succeeds_when_bills_paid(): void
    {
        $penyewa = Penyewa::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Andi',
            'no_wali' => '081122334455',
            'deposit' => 1000000,
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // Act as admin
        $response = $this->actingAs($this->admin)
            ->post("/admin/penyewa/{$penyewa->id}/checkout", [
                'apakah_ada_kerusakan' => 0,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertEquals('nonaktif', $penyewa->fresh()->status);
        $this->assertEquals(0, $penyewa->fresh()->deposit);

        // Assert remaining deposit refund is recorded as an expense
        $expense = Pengeluaran::where('kategori', 'operasional')
            ->where('nominal', 1000000)
            ->first();

        $this->assertNotNull($expense);
        $this->assertStringContainsString('Pengembalian Jaminan Deposit Penyewa', $expense->nama_pengeluaran);
    }

    /**
     * Test 5: Midtrans callback exploit prevention (Different Month)
     */
    public function test_midtrans_callback_exploit_prevention_different_month(): void
    {
        $penyewa = Penyewa::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Andi',
            'no_wali' => '081122334455',
            'deposit' => 1000000,
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        $tagihan = Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-EXPLOIT-101',
            'periode_bulan' => now()->month,
            'periode_tahun' => now()->year,
            'tanggal_tagihan' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(9)->toDateString(),
            'nominal_pokok' => 1000000,
            'nominal_denda' => 50000,
            'nominal_total' => 1050000,
            'status' => 'terlambat',
        ]);

        // Exploit token timestamp is from last month
        $lastMonthTimestamp = now()->subMonth()->timestamp;
        $orderId = "TGH-EXPLOIT-101-{$lastMonthTimestamp}";

        // Webhook request payload
        $signature = hash('sha512', $orderId . '200' . '1000000.00' . $this->serverKey);
        
        $response = $this->postJson('/api/midtrans/callback', [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => '1000000.00',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'transaction_id' => 'tr-123456',
            'payment_type' => 'credit_card',
            'transaction_time' => now()->toDateTimeString(),
        ]);

        // Exploit should fail because token timestamp month is different from database billing denda month
        $response->assertStatus(400);
        $this->assertEquals('terlambat', $tagihan->fresh()->status);
    }

    /**
     * Test 6: Tenant history access read-only
     */
    public function test_tenant_history_access_read_only(): void
    {
        $penyewa = Penyewa::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Andi',
            'no_wali' => '081122334455',
            'deposit' => 1000000,
            'status' => 'nonaktif', // Non-active
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // Unactive tenant should be allowed to view their bills page (EnsureTenantIsActive bypass)
        $response = $this->actingAs($this->user)
            ->get('/penyewa/tagihan');

        $response->assertStatus(200);
    }
}
