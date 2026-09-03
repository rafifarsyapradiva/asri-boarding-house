<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Reservasi;
use App\Events\ReservasiDibuat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MarketSegmentationValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tenant;
    private Kamar $kamar;
    private Kamar $kamar2;

    protected function setUp(): void
    {
        parent::setUp();

        // CONFIGURATION: QUEUE_CONNECTION=sync
        Config::set('queue.default', 'sync');

        // Target phone numbers from prompt
        Config::set('reservasi.admin_wa', '62895330031313');

        // Prevent network outbound calls with Mocking HTTP
        Http::fake([
            'api.fonnte.com/*' => Http::response([
                'status' => true,
                'message' => 'Pesan berhasil diisolasikan oleh Anti-Gravity Guard (HP Fisik Aman)',
            ], 200),
        ]);

        // Inisialisasi User Admin
        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '62895330031313', // ADMIN WA NUMBER
            'role' => 'admin',
            'is_active' => 1,
        ]);

        // Inisialisasi User Penyewa
        $this->tenant = User::create([
            'nama' => 'Penyewa Digital',
            'email' => 'tenant.digital@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '6282219575575', // TARGET TEST NO
            'role' => 'penyewa',
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Digital',
            'no_wali' => '628111111111',
            'require_password_change' => 0,
            'is_active' => 1,
        ]);

        // Inisialisasi Kamar
        $this->kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $this->kamar2 = Kamar::create([
            'nomor_kamar' => '103',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);
    }

    /**
     * Skenario 1: Penegakan Aturan Segmen Konvensional (Walk-In / Offline)
     * Aksi Uji A (Validasi Gagal - Melebihi Batas)
     */
    public function test_conventional_segment_fails_validation_for_duration_exceeding_max_bounds(): void
    {
        $payloadShort = [
            'nama' => 'Penyewa Walk-in Short',
            'email' => 'walkin.short@example.com',
            'no_hp' => '081223344551',
            'nik' => '1234567890123451',
            'kamar_id' => $this->kamar->id,
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Wali Offline',
            'no_wali' => '08122334455',
            'tipe_sewa' => 'bulanan',
            'durasi' => 13, // Fails: exceeds 12 months limit
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.penyewa.store'), $payloadShort);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['durasi']);
        $response->assertJsonFragment([
            'durasi' => ['Durasi sewa bulanan maksimal 12 bulan.']
        ]);

        $this->assertDatabaseMissing('users', ['email' => 'walkin.short@example.com']);
    }

    /**
     * Skenario 1: Penegakan Aturan Segmen Konvensional (Walk-In / Offline)
     * Aksi Uji B (Validasi Berhasil & Alokasi Deposit)
     */
    public function test_conventional_segment_succeeds_for_flexible_durations_and_allocates_deposit(): void
    {
        $payloadValid = [
            'nama' => 'Penyewa Walk-in Valid',
            'email' => 'walkin.valid@example.com',
            'no_hp' => '081223344552',
            'nik' => '1234567890123452',
            'kamar_id' => $this->kamar->id,
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Wali Offline',
            'no_wali' => '08122334455',
            'tipe_sewa' => 'bulanan',
            'durasi' => 2, // Valid: 2 months (previously rejected)
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), $payloadValid);

        $response->assertRedirect(route('admin.penyewa.index'));
        $this->assertDatabaseHas('users', ['email' => 'walkin.valid@example.com']);
        
        $penyewa = Penyewa::where('nik', '1234567890123452')->first();
        $this->assertNotNull($penyewa);
        
        // Assert financial rule: Month 1 is allocated as deposit (equal to kamar's monthly price)
        $this->assertEquals(1000000, (float)$penyewa->deposit);
    }

    /**
     * Skenario 2: Fleksibilitas Segmen Digital (Reservasi Online v2.1)
     * Aksi Uji A (Fleksibilitas Harian/Mingguan)
     */
    public function test_digital_segment_allows_short_rental_durations(): void
    {
        // 1. Test short daily rental (3 days)
        $payloadHarian = [
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'harian',
            'tanggal_mulai' => date('Y-m-d'),
            'durasi' => 3, // 3 days
            'is_dp' => 0,
        ];

        $responseHarian = $this->actingAs($this->tenant)
            ->post(route('penyewa.reservasi.store'), $payloadHarian);

        $responseHarian->assertStatus(302); // Redirects to payment page
        $responseHarian->assertSessionHasNoErrors();

        // 2. Test short weekly rental (1 week) using kamar2 to avoid overlap validation
        $payloadMingguan = [
            'kamar_id' => $this->kamar2->id,
            'tipe_sewa' => 'mingguan',
            'tanggal_mulai' => date('Y-m-d'),
            'durasi' => 1, // 1 week
            'is_dp' => 0,
        ];

        $responseMingguan = $this->actingAs($this->tenant)
            ->post(route('penyewa.reservasi.store'), $payloadMingguan);

        $responseMingguan->assertStatus(302);
        $responseMingguan->assertSessionHasNoErrors();

        // Assert reservations are successfully stored
        $this->assertDatabaseHas('reservasi', [
            'user_id' => $this->tenant->id,
            'tipe_sewa' => 'harian',
            'durasi' => 3,
        ]);
        $this->assertDatabaseHas('reservasi', [
            'user_id' => $this->tenant->id,
            'tipe_sewa' => 'mingguan',
            'durasi' => 1,
        ]);
    }

    /**
     * Skenario 2: Fleksibilitas Segmen Digital (Reservasi Online v2.1)
     * Aksi Uji B (Pemicu Event Notifikasi)
     */
    public function test_digital_segment_dispatches_event_and_sends_whatsapp_notification_to_admin(): void
    {
        Event::fake([ReservasiDibuat::class]);

        $payload = [
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'harian',
            'tanggal_mulai' => date('Y-m-d'),
            'durasi' => 3,
            'is_dp' => 0,
        ];

        $this->actingAs($this->tenant)
            ->post(route('penyewa.reservasi.store'), $payload);

        // Verify Event is triggered
        Event::assertDispatched(ReservasiDibuat::class, function ($event) {
            return $event->reservasi->user_id === $this->tenant->id;
        });

        // Run the queue job manually to verify the WA message is sent to the admin number
        $reservasi = Reservasi::where('user_id', $this->tenant->id)->first();
        
        $job = new \App\Jobs\KirimNotifikasiAdminReservasiJob($reservasi);
        $job->handle(app(\App\Services\NotifikasiService::class));

        // Assert WhatsApp notification via Fonnte is sent directly to the Admin number
        Http::assertSent(function ($request) {
            // TODO: Kirim notifikasi WA ke admin mengenai adanya hunian dinamis berdurasi pendek
            return str_contains($request->url(), 'api.fonnte.com/send') &&
                   $request['target'] === '62895330031313' &&
                   str_contains(strtolower($request['message']), 'reservasi baru');
        });
    }

    /**
     * Audit: Pastikan pemisahan aturan tidak hardcoded di Controller melainkan
     * menggunakan Custom Form Request yang terisolasi.
     */
    public function test_architecture_uses_isolated_form_requests_for_validation(): void
    {
        // 1. Audit PenyewaController (Offline)
        $penyewaRef = new \ReflectionMethod(\App\Http\Controllers\Admin\PenyewaController::class, 'store');
        $this->assertGreaterThan(0, $penyewaRef->getNumberOfParameters());
        $penyewaParam = $penyewaRef->getParameters()[0];
        $this->assertEquals(\App\Http\Requests\StorePenyewaRequest::class, $penyewaParam->getType()?->getName());

        // 2. Audit ReservasiController (Online)
        $reservasiRef = new \ReflectionMethod(\App\Http\Controllers\Penyewa\ReservasiController::class, 'store');
        $this->assertGreaterThan(0, $reservasiRef->getNumberOfParameters());
        $reservasiParam = $reservasiRef->getParameters()[0];
        $this->assertEquals(\App\Http\Requests\StoreReservasiRequest::class, $reservasiParam->getType()?->getName());
    }
}
