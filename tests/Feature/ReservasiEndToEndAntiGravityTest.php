<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Events\ReservasiDikonfirmasi;
use App\Events\PembayaranBerhasil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReservasiEndToEndAntiGravityTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $tenantUser;
    private User $userB;
    private Kamar $kamarDeluxe;
    private Kamar $kamarStandard;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Konfigurasi Sinkronisasi Antrean & Konstanta Lingkungan
        Config::set('queue.default', 'sync');
        Config::set('app.url', 'http://localhost');
        Config::set('reservasi.admin_wa', '62895330031313');
        Config::set('app.wa_owner', '62895330031313');
        Config::set('midtrans.server_key', 'e2e_midtrans_server_key');

        // 2. Mencegah Network Outbound dengan Mocking HTTP Call Fonnte & Midtrans
        Http::fake([
            'api.fonnte.com/*' => Http::response([
                'status' => true,
                'message' => 'Pesan berhasil diisolasikan oleh Anti-Gravity Guard (HP Fisik Aman)',
            ], 200),
            'app.sandbox.midtrans.com/*' => Http::response(['token' => 'fake_midtrans_snap_token_123'], 200),
            'app.midtrans.com/*' => Http::response(['token' => 'fake_midtrans_snap_token_123'], 200),
        ]);

        // 3. Seeding Database Kamar & Fasilitas Awal
        (new \Database\Seeders\FasilitasSeeder())->run();

        $this->kamarDeluxe = Kamar::create([
            'nomor_kamar' => '202',
            'lantai' => 2,
            'tipe' => 'deluxe',
            'luas_m2' => 15.0,
            'harga_bulan' => 950000,
            'status' => 'tersedia',
        ]);

        $this->kamarStandard = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'tersedia',
        ]);

        // 4. Inisialisasi Data Pengguna Sesuai Peta Nomor WhatsApp Asli (Override Kritis)
        $this->adminUser = User::factory()->create([
            'no_hp' => '62895330031313',
            'role' => 'admin',
            'require_password_change' => 0,
        ]);

        $this->tenantUser = User::factory()->create([
            'no_hp' => '6282219575575',
            'email' => 'tenant.test@gmail.com',
            'role' => 'penyewa',
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Tenant',
            'no_wali' => '628111111111',
            'require_password_change' => 0,
        ]);

        $this->userB = User::factory()->create([
            'no_hp' => '6289998887776',
            'role' => 'penyewa',
            'nik' => '1234567890123457',
            'nama_wali' => 'Wali User B',
            'no_wali' => '628111111112',
            'require_password_change' => 0,
        ]);
    }

    /**
     * Skenario A: "The Flawless Digital Journey" (Pembayaran Lunas Penuh)
     */
    public function test_scenario_a_flawless_digital_journey_lunas(): void
    {
        $tenantUser = $this->tenantUser;
        $adminUser = $this->adminUser;

        // 1. Fase Katalog & Cache
        $response = $this->get('/');
        $response->assertStatus(200);
        $this->assertTrue(Cache::has('fasilitas_aktif_landing'));
        $response->assertSee('Hubungi Admin via WhatsApp');
        
        // Assert komponen x-wa-float-button merender target sentuh minimum 56px (w-14 = 56px)
        $response->assertSee('w-14');
        $response->assertSee('h-14');

        // 2. Fase Booking & Anti-Double Booking
        $payloadBooking = [
            'kamar_id' => $this->kamarDeluxe->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'durasi' => 2,
            'is_dp' => 0, // Lunas penuh (is_dp = false)
        ];

        $responseBooking = $this->actingAs($tenantUser)
            ->post(route('penyewa.reservasi.store'), $payloadBooking);

        $reservasi = Reservasi::where('user_id', $tenantUser->id)->latest()->first();
        $this->assertNotNull($reservasi);
        $responseBooking->assertRedirect(route('penyewa.reservasi.pembayaran', $reservasi->id));

        // Simulasikan Race Condition (booking ganda untuk rentang tanggal yang bertabrakan)
        try {
            app(\App\Services\ReservasiService::class)->buatReservasi(array_merge($payloadBooking, [
                'user_id' => $tenantUser->id
            ]));
            $this->fail('Sistem membiarkan double booking tanpa melempar ValidationException.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('kamar_id', $e->errors());
        }

        // 3. Fase Midtrans & Webhook Idempotency
        $midtransService = app(\App\Services\MidtransService::class);
        $snapTokenResult = $midtransService->createSnapTokenReservasi($reservasi);
        $this->assertEquals('fake_midtrans_snap_token_123', $snapTokenResult['snap_token']);

        // Refresh reservasi untuk mensinkronkan order_id baru dari Midtrans Snap Token
        $reservasi->refresh();
        $this->assertEquals($snapTokenResult['order_id'], $reservasi->order_id);

        $signature = hash('sha512', $reservasi->order_id . '200' . '1900000' . config('midtrans.server_key'));

        $payloadWebhook = [
            'order_id' => $reservasi->order_id,
            'status_code' => '200',
            'gross_amount' => '1900000',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card',
            'transaction_id' => 'tx-e2e-full-123'
        ];

        // Kirim Webhook ke-1 (Normal Flow)
        $responseWebhook1 = $this->postJson(route('api.midtrans.callback-reservasi'), $payloadWebhook);
        $responseWebhook1->assertStatus(200)
            ->assertJsonFragment(['message' => 'Callback processed successfully']);

        $reservasi->refresh();
        $this->assertEquals('lunas', $reservasi->status);
        $this->assertEquals('tx-e2e-full-123', $reservasi->transaction_id);

        // Kirim Webhook ke-2 (Duplicate network / Idempotency check)
        $responseWebhook2 = $this->postJson(route('api.midtrans.callback-reservasi'), $payloadWebhook);
        $responseWebhook2->assertStatus(200)
            ->assertJsonFragment(['message' => 'Reservation transaction already handled']);

        // 4. Fase Orkestrasi Transisi Data (TransisiPenyewaService) via Konfirmasi Admin
        $responseConfirm = $this->actingAs($adminUser)
            ->post(route('admin.reservasi.konfirmasi', $reservasi->id), [
                'nik' => '1234567890123456',
                'nama_wali' => 'Wali Rian',
                'no_wali' => '628111111111',
            ]);

        $responseConfirm->assertRedirect(route('admin.reservasi.index'));

        // Assert: Data user bermutasi masuk ke tabel penyewa dengan status aktif
        $this->assertDatabaseHas('penyewa', [
            'user_id' => $tenantUser->id,
            'status' => 'aktif',
            'nik' => '1234567890123456',
        ]);

        $penyewa = $reservasi->refresh()->penyewa;
        $this->assertNotNull($penyewa);

        // Assert: PenyewaObserver terpicu secara natural dan mengunci status kamar dari tersedia ke terisi
        $this->kamarDeluxe->refresh();
        $this->assertEquals('terisi', $this->kamarDeluxe->status);

        // Assert: Event ReservasiDikonfirmasi terpicu dan mengirimkan notifikasi selamat datang ke no HP target
        Http::assertSent(function ($request) use ($tenantUser) {
            // TODO: Pastikan string isi pesan WhatsApp terurai dengan benar dan bebas dari token mentah
            return str_contains($request->url(), 'api.fonnte.com/send') &&
                   $request['target'] === $tenantUser->no_hp &&
                   (str_contains($request['message'], 'selamat datang') || str_contains($request['message'], 'Selamat Datang')) &&
                   !str_contains($request['message'], '{') &&
                   !str_contains($request['message'], '}');
        });
    }

    /**
     * Skenario B: "The Down-Payment Dispute" (Pembayaran DP 30%)
     */
    public function test_scenario_b_down_payment_dispute(): void
    {
        $tenantUser = $this->tenantUser;
        $adminUser = $this->adminUser;

        // 1. Buat reservasi dengan opsi DP 30% (is_dp = true)
        $payloadBooking = [
            'kamar_id' => $this->kamarStandard->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'durasi' => 1,
            'is_dp' => 1, // DP 30%
        ];

        $this->actingAs($tenantUser)
            ->post(route('penyewa.reservasi.store'), $payloadBooking);

        $reservasi = Reservasi::where('user_id', $tenantUser->id)->latest()->first();
        $this->assertNotNull($reservasi);
        $this->assertTrue((bool)$reservasi->is_dp);
        $this->assertEquals(225000.0, (float) $reservasi->nominal_dp);
        $this->assertEquals(525000.0, (float) $reservasi->nominal_sisa);

        // 2. Webhook Pembayaran DP dari Midtrans
        $signature = hash('sha512', $reservasi->order_id . '200' . '225000' . config('midtrans.server_key'));

        $payloadWebhook = [
            'order_id' => $reservasi->order_id,
            'status_code' => '200',
            'gross_amount' => '225000',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card',
            'transaction_id' => 'tx-e2e-dp-123'
        ];

        $responseWebhook = $this->postJson(route('api.midtrans.callback-reservasi'), $payloadWebhook);
        $responseWebhook->assertStatus(200);

        $reservasi->refresh();
        $this->assertEquals('dp', $reservasi->status);

        // 3. Konfirmasi Admin untuk mengaktifkan penyewa
        $this->actingAs($adminUser)
            ->post(route('admin.reservasi.konfirmasi', $reservasi->id), [
                'nik' => '1234567890123458',
                'nama_wali' => 'Wali Rian DP',
                'no_wali' => '628111111111',
            ]);

        $penyewa = $reservasi->refresh()->penyewa;
        $this->assertNotNull($penyewa);

        // 4. Assert Tagihan yang di-inject bernilai sisa pelunasan (70% dari harga sewa)
        $this->assertDatabaseHas('tagihan', [
            'penyewa_id' => $penyewa->id,
            'status' => 'pending',
            'nominal_total' => 525000,
        ]);

        $tagihan = Tagihan::where('penyewa_id', $penyewa->id)->first();
        $this->assertStringContainsString('Rp 525.000', $tagihan->keterangan);
    }

    /**
     * Skenario C: "The Isolated Communication Leak Test" (Chat Box Gatekeeper)
     */
    public function test_scenario_c_isolated_communication_leak(): void
    {
        $tenantUser = $this->tenantUser;

        // 1. Buat Reservasi atas nama tenant
        $reservasi = Reservasi::create([
            'user_id' => $tenantUser->id,
            'kamar_id' => $this->kamarStandard->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 750000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-LEAK-TEST'
        ]);

        // 2. Coba tembak url chat_fetch milik tenant sebagai User B
        $responseLeak = $this->actingAs($this->userB)
            ->get(route('api.chat.fetch', $reservasi->id));

        // 3. Pastikan sistem memblokir dengan respons 403 Forbidden
        $responseLeak->assertStatus(403);
    }
}
