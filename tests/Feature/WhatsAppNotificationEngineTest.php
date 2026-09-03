<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Services\FonnteService;
use App\Services\NotifikasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppNotificationEngineTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Penyewa $penyewa;
    private Kamar $kamar;
    private NotifikasiService $notifikasiService;
    private FonnteService $fonnteService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fonnteService = app(FonnteService::class);
        $this->notifikasiService = app(NotifikasiService::class);

        // 1. Setup mock Kamar
        $this->kamar = Kamar::create([
            'nomor_kamar' => '202',
            'lantai' => 2,
            'tipe' => 'deluxe',
            'luas_m2' => 15.0,
            'harga_bulan' => 950000,
            'status' => 'tersedia',
        ]);

        // 2. Setup mock User (Penyewa)
        $this->user = User::create([
            'nama' => 'Rian Hidayat',
            'email' => 'rian@example.com',
            'password' => bcrypt('password123'),
            'no_hp' => '6282219575575',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        // 3. Setup mock Penyewa
        $this->penyewa = Penyewa::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '3201234567890001',
            'tanggal_masuk' => '2026-06-01',
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'deposit' => 500000,
            'nama_wali' => 'Budi Hidayat',
            'no_wali' => '6281298765432',
        ]);

        Config::set('app.url', 'http://localhost');
        Config::set('reservasi.admin_wa', '62895330031313');

        Http::fake([
            'api.fonnte.com/*' => Http::response([
                'status' => true,
                'message' => 'Pesan berhasil diisolasikan oleh Anti-Gravity Guard (HP Fisik Aman)',
            ], 200),
        ]);
    }

    /**
     * Test Phone Number Normalisation logic in FonnteService.
     */
    public function test_normalisasi_nomor_telepon_fonnte(): void
    {
        $inputs = [
            '082219575575',
            '0822 1957 5575',
            '0822-1957-5575',
            '+6282219575575',
            '6282219575575',
        ];

        foreach ($inputs as $input) {
            $formatted = $this->fonnteService->formatNomor($input);
            $this->assertEquals('6282219575575', $formatted);
        }
    }

    /**
     * Test all 9 Event WhatsApp templates output to check interpolation and ensure no raw tokens.
     */
    public function test_semua_template_whatsapp_terurai_tanpa_ada_token_mentah(): void
    {
        // Setup Tagihan
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-MOCK-01',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 950000,
            'nominal_denda' => 50000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        // Setup Pembayaran
        $pembayaran = Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'transaction_id' => 'TX-MOCK-01',
            'payment_type' => 'midtrans',
            'nominal' => 1000000,
            'status_midtrans' => 'settlement',
            'tanggal_bayar' => now(),
        ]);

        // Setup Reservasi
        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-07-01',
            'durasi' => 1,
            'total_harga' => 950000,
            'is_dp' => true,
            'nominal_dp' => 285000,
            'nominal_sisa' => 665000,
            'status' => 'dp',
            'order_id' => 'RSV-MOCK-01',
        ]);

        // List of all template builders and assertions
        
        // 1. Tagihan Baru
        $t1 = $this->notifikasiService->templateTagihanBaru($tagihan, $this->penyewa);
        // TODO: Sediakan baris assertion untuk memverifikasi detail token Tagihan Baru terurai
        $this->assertStringNotContainsString('{nama}', $t1);
        $this->assertStringNotContainsString('{nomor_kamar}', $t1);
        $this->assertStringNotContainsString('{nominal_total}', $t1);
        $this->assertStringContainsString('Rian Hidayat', $t1);
        $this->assertStringContainsString('6/2026', $t1);
        $this->assertStringContainsString('202', $t1);
        $this->assertStringContainsString('Rp 1.000.000', $t1);
        $this->assertStringContainsString('2026-06-10', $t1);
        $this->assertStringContainsString('http://localhost/penyewa/tagihan', $t1);

        // 2. Pembayaran Berhasil
        $t2 = $this->notifikasiService->templatePembayaranBerhasil($pembayaran);
        // TODO: Sediakan baris assertion untuk memverifikasi detail token Pembayaran Berhasil terurai
        $this->assertStringNotContainsString('{payment_type}', $t2);
        $this->assertStringNotContainsString('{nominal}', $t2);
        $this->assertStringNotContainsString('{link_nota}', $t2);
        $this->assertStringContainsString('✅', $t2);
        $this->assertStringContainsString('midtrans', $t2);
        $this->assertStringContainsString('Rp 1.000.000', $t2);
        $this->assertStringContainsString('http://localhost/penyewa/nota/' . $pembayaran->id . '/download', $t2);

        // 3. Reminder Jatuh Tempo / Bulan 1
        $t3 = $this->notifikasiService->templateReminderJatuhTempo($tagihan, $this->penyewa);
        // TODO: Sediakan baris assertion untuk memverifikasi detail token Reminder Jatuh Tempo terurai
        $this->assertStringNotContainsString('{nomor_kamar}', $t3);
        $this->assertStringNotContainsString('{nominal_total}', $t3);
        $this->assertStringContainsString('⚠️', $t3);
        $this->assertStringContainsString('202', $t3);
        $this->assertStringContainsString('Rp 950.000', $t3);

        // 4. Notifikasi ke Wali — Bulan 2
        $t4 = $this->notifikasiService->templateNotifikasiWali($tagihan, $this->penyewa);
        // TODO: Sediakan baris assertion untuk memverifikasi detail token Notifikasi Wali terurai
        $this->assertStringNotContainsString('{nama_wali}', $t4);
        $this->assertStringNotContainsString('{nama_penyewa}', $t4);
        $this->assertStringContainsString('Budi Hidayat', $t4);
        $this->assertStringContainsString('Rian Hidayat', $t4);
        $this->assertEquals('6281298765432', $this->fonnteService->formatNomor($this->penyewa->no_wali));

        // 5. Denda Bulan 3+
        $t5 = $this->notifikasiService->templateDenda($tagihan, $this->penyewa);
        // TODO: Sediakan baris assertion untuk memverifikasi detail token Denda Bulan 3+ terurai
        $this->assertStringNotContainsString('{nominal_pokok}', $t5);
        $this->assertStringNotContainsString('{nominal_denda}', $t5);
        $this->assertStringNotContainsString('{nominal_total}', $t5);
        $this->assertStringContainsString('⚠️', $t5);
        $this->assertStringContainsString('Rp 950.000', $t5);
        $this->assertStringContainsString('Rp 50.000', $t5);
        $this->assertStringContainsString('Rp 1.000.000', $t5);

        // 6. Welcome Penyewa Baru
        $t6 = $this->notifikasiService->templateWelcomePenyewa($this->penyewa);
        // TODO: Sediakan baris assertion untuk memverifikasi detail token Welcome Penyewa terurai
        $this->assertStringNotContainsString('{email}', $t6);
        $this->assertStringNotContainsString('{no_hp}', $t6);
        $this->assertStringContainsString('rian@example.com', $t6);
        $this->assertStringContainsString('6282219575575', $t6);

        // 7. ReservasiDibuat (BARU v2.1)
        $t7 = $this->notifikasiService->templateReservasiDibuat($reservasi);
        // TODO: Sediakan baris assertion untuk memverifikasi detail token ReservasiDibuat terurai
        $this->assertStringNotContainsString('{nama_user}', $t7);
        $this->assertStringNotContainsString('{nomor_kamar}', $t7);
        $this->assertStringNotContainsString('{tipe_sewa}', $t7);
        $this->assertStringNotContainsString('{durasi}', $t7);
        $this->assertStringContainsString('Rian Hidayat', $t7);
        $this->assertStringContainsString('202', $t7);
        $this->assertStringContainsString('bulanan', $t7);
        $this->assertStringContainsString('1', $t7);
        $this->assertStringContainsString('http://localhost/admin/reservasi/' . $reservasi->id, $t7);
        $this->assertEquals('62895330031313', $this->fonnteService->formatNomor(config('reservasi.admin_wa')));

        // 8. ReservasiDibayar (BARU v2.1)
        $t8 = $this->notifikasiService->templateReservasiDibayar($reservasi);
        // TODO: Sediakan baris assertion untuk memverifikasi detail token ReservasiDibayar terurai
        $this->assertStringNotContainsString('{nominal_dp}', $t8);
        $this->assertStringContainsString('✅ Pembayaran Reservasi DITERIMA!', $t8);
        $this->assertStringContainsString('Rp 285.000', $t8);

        // 9. ReservasiDikonfirmasi (BARU v2.1)
        $t9 = $this->notifikasiService->templateReservasiDikonfirmasi($reservasi);
        // TODO: Sediakan baris assertion untuk memverifikasi detail token ReservasiDikonfirmasi terurai
        $this->assertStringNotContainsString('{APP_URL}', $t9);
        $this->assertStringContainsString('http://localhost/penyewa/dashboard', $t9);
    }

    /**
     * Test Null Safety and special characters in user names.
     */
    public function test_null_safety_dan_karakter_khusus(): void
    {
        $this->user->update([
            'nama' => "D'Artagnan-O'Reilly bin Syarifuddin & Co. yang memiliki nama sangat panjang melebihi batas rata-rata database",
        ]);

        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-MOCK-SPECIAL',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 950000,
            'nominal_total' => 950000,
            'status' => 'pending',
        ]);

        $text = $this->notifikasiService->templateTagihanBaru($tagihan, $this->penyewa);

        $this->assertStringContainsString("D'Artagnan-O'Reilly", $text);
        $this->assertStringNotContainsString('{nama}', $text);
    }

    /**
     * Test Currency Formatting (Rupiah layout check).
     */
    public function test_currency_formatting(): void
    {
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-MOCK-CURR',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1500000,
            'nominal_total' => 1550000,
            'nominal_denda' => 50000,
            'status' => 'pending',
        ]);

        $text = $this->notifikasiService->templateDenda($tagihan, $this->penyewa);

        $this->assertStringContainsString('Rp 1.500.000', $text);
        $this->assertStringContainsString('Rp 50.000', $text);
        $this->assertStringContainsString('Rp 1.550.000', $text);
    }

    /**
     * Test URL Formatting to prevent double slashes.
     */
    public function test_url_formatting_anti_double_slash(): void
    {
        Config::set('app.url', 'http://localhost/');

        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-MOCK-URL',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 950000,
            'nominal_total' => 950000,
            'status' => 'pending',
        ]);

        $text = $this->notifikasiService->templateTagihanBaru($tagihan, $this->penyewa);

        $this->assertStringContainsString('http://localhost/penyewa/tagihan', $text);
        $this->assertStringNotContainsString('http://localhost//penyewa', $text);
    }

    /**
     * Test that KirimWelcomeMessageJob is dispatched when creating a manual tenant.
     */
    public function test_welcome_notification_dispatches_queue_job(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        // Create a new room with status tersedia specifically for this test
        $kamarBaru = Kamar::create([
            'nomor_kamar' => '303',
            'lantai' => 3,
            'tipe' => 'deluxe',
            'luas_m2' => 15.0,
            'harga_bulan' => 950000,
            'status' => 'tersedia',
        ]);

        // Admin logins and posts new tenant data
        $admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin_test@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567800',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $payload = [
            'nama' => 'Penyewa Asinkron',
            'email' => 'async@example.com',
            'no_hp' => '081223344559',
            'nik' => '1234567890123454',
            'kamar_id' => $kamarBaru->id,
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Nama Wali Async',
            'no_wali' => '081122334455',
            'deposit' => 200000,
            'tipe_sewa' => 'bulanan',
            'durasi' => 3,
        ];

        $this->actingAs($admin)
            ->post(route('admin.penyewa.store'), $payload);

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\KirimWelcomeMessageJob::class, function ($job) {
            return $job->penyewa->user->email === 'async@example.com';
        });
    }

    /**
     * Test that KirimWelcomeMessageJob throws exception when Fonnte API fails.
     */
    public function test_welcome_notification_job_throws_exception_on_api_failure(): void
    {
        // Clear log table to bypass idempotency check since it ran during setUp
        \App\Models\LogNotifikasi::query()->delete();

        // Force Http::post to throw an exception
        Http::fake([
            'api.fonnte.com/*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
            },
        ]);

        $job = new \App\Jobs\KirimWelcomeMessageJob($this->penyewa);

        $this->expectException(\Illuminate\Http\Client\ConnectionException::class);
        $this->expectExceptionMessage('Connection timed out');

        $job->handle($this->notifikasiService);
    }
}
