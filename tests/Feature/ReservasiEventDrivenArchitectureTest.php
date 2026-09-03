<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Reservasi;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Events\ReservasiDibuat;
use App\Events\ReservasiDibayar;
use App\Events\ReservasiDikonfirmasi;
use App\Events\PembayaranBerhasil;
use App\Services\TransisiPenyewaService;
use App\Services\NotifikasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ReservasiEventDrivenArchitectureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'api.fonnte.com/*' => Http::response([
                'status' => true,
                'message' => 'Pesan berhasil diisolasikan oleh Anti-Gravity Guard (HP Fisik Aman)',
            ], 200),
        ]);

        Config::set('reservasi.admin_wa', '6289876543210');

        $this->user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $this->admin = User::create([
            'nama' => 'Admin Dummy',
            'email' => 'admin@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '089876543210',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $this->kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);
    }

    /**
     * Test that ReservasiDibuat event dispatches and fires WhatsApp to admin.
     */
    public function test_reservasi_dibuat_mengirimkan_notifikasi_ke_admin(): void
    {
        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-DIBUAT-123'
        ]);

        // Dispatch Event
        event(new ReservasiDibuat($reservasi));

        // Verify Fonnte API was called for the admin number
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.fonnte.com/send') &&
                   $request['target'] === '6289876543210' &&
                   str_contains($request['message'], 'RESERVASI BARU MASUK');
        });
    }

    /**
     * Test that ReservasiDibayar event dispatches and fires WhatsApp to tenant.
     */
    public function test_reservasi_dibayar_mengirimkan_notifikasi_ke_penyewa(): void
    {
        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-DIBAYAR-123'
        ]);

        // Dispatch Event
        event(new ReservasiDibayar($reservasi));

        // Verify Fonnte API was called for the tenant number
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.fonnte.com/send') &&
                   $request['target'] === '6281234567890' &&
                   str_contains($request['message'], 'Pembayaran Reservasi DITERIMA');
        });
    }

    /**
     * Test that TransisiPenyewaService successfully transitions a reservation to a tenant.
     */
    public function test_transisi_reservasi_menjadi_penyewa_aktif(): void
    {
        $this->actingAs($this->admin);

        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'lunas',
            'order_id' => 'RSV-TRANSISI-123'
        ]);

        request()->merge([
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Dummy',
            'no_wali' => '081234567890',
            'deposit' => 200000,
        ]);

        // Call the transition service
        $service = app(TransisiPenyewaService::class);
        $service->transisi($reservasi, $this->admin->id, request()->all());

        // Verify Penyewa record was created
        $this->assertDatabaseHas('penyewa', [
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Dummy',
            'no_wali' => '081234567890',
            'deposit' => 200000,
            'status' => 'aktif',
        ]);

        // Verify Kamar status updated to terisi (via PenyewaObserver)
        $this->kamar->refresh();
        $this->assertEquals('terisi', $this->kamar->status);

        // Verify Reservasi status updated to dikonfirmasi
        $reservasi->refresh();
        $this->assertEquals('dikonfirmasi', $reservasi->status);
        $this->assertEquals($this->admin->id, $reservasi->dikonfirmasi_oleh);
        $this->assertNotNull($reservasi->penyewa_id);
    }

    /**
     * Test that TransisiPenyewaService with DP injects sisa DP tagihan.
     */
    public function test_transisi_dengan_dp_menginjeksi_tagihan_sisa(): void
    {
        $this->actingAs($this->admin);

        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => true,
            'nominal_dp' => 300000,
            'nominal_sisa' => 700000,
            'status' => 'dp',
            'order_id' => 'RSV-DP-123'
        ]);

        request()->merge([
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Dummy',
            'no_wali' => '081234567890',
        ]);

        $service = app(TransisiPenyewaService::class);
        $service->transisi($reservasi, $this->admin->id, request()->all());

        $reservasi->refresh();
        $this->assertNotNull($reservasi->penyewa_id);

        // Verify sisa DP tagihan was created
        $this->assertDatabaseHas('tagihan', [
            'penyewa_id' => $reservasi->penyewa_id,
            'nominal_total' => 700000,
            'status' => 'pending',
        ]);
    }

    /**
     * Test that TransisiPenyewaService with Full Payment creates a lunas bill/pembayaran and triggers PembayaranBerhasil event.
     */
    public function test_transisi_dengan_lunas_penuh_menginjeksi_tagihan_lunas_dan_pembayaran(): void
    {
        Event::fake([PembayaranBerhasil::class]);

        $this->actingAs($this->admin);

        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'lunas',
            'order_id' => 'RSV-FULL-123',
            'metode_pembayaran' => 'midtrans',
            'transaction_id' => 'tx-999',
        ]);

        request()->merge([
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Dummy',
            'no_wali' => '081234567890',
        ]);

        $service = app(TransisiPenyewaService::class);
        $service->transisi($reservasi, $this->admin->id, request()->all());

        $reservasi->refresh();
        $this->assertNotNull($reservasi->penyewa_id);

        // Verify Tagihan created with 'lunas' status
        $this->assertDatabaseHas('tagihan', [
            'penyewa_id' => $reservasi->penyewa_id,
            'nominal_total' => 1000000,
            'status' => 'lunas',
            'metode_pembayaran' => 'midtrans',
        ]);

        // Verify Pembayaran record was created
        $this->assertDatabaseHas('pembayaran', [
            'nominal' => 1000000,
            'payment_type' => 'midtrans',
            'status_midtrans' => 'settlement',
            'transaction_id' => 'tx-999',
        ]);

        // Verify event was fired
        Event::assertDispatched(PembayaranBerhasil::class);
    }
}
