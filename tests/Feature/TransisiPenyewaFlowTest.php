<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Reservasi;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Events\ReservasiDikonfirmasi;
use App\Events\PembayaranBerhasil;
use App\Services\TransisiPenyewaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TransisiPenyewaFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $this->user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
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
     * Test DP transition scheme (30% DP, 70% remaining invoiced as pending).
     */
    public function test_skema_transisi_pembayaran_dp_30(): void
    {
        Event::fake([ReservasiDikonfirmasi::class]);

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
            'order_id' => 'RSV-DP-FLOW-TEST'
        ]);

        request()->merge([
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Dummy',
            'no_wali' => '081234567890',
            'deposit' => 200000,
        ]);

        // Run transition
        $service = app(TransisiPenyewaService::class);
        $penyewa = $service->transisi($reservasi, $this->admin->id, request()->all());

        // 1. Assert Penyewa record created with status 'aktif'
        $this->assertDatabaseHas('penyewa', [
            'id' => $penyewa->id,
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'harga_sewa' => 1000000,
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Dummy',
            'no_wali' => '081234567890',
            'deposit' => 200000,
            'status' => 'aktif',
        ]);

        // 2. Assert Kamar status updated to 'terisi' (verifies PenyewaObserver)
        $this->kamar->refresh();
        $this->assertEquals('terisi', $this->kamar->status);

        // 3. Assert remaining 70% invoice injected as pending
        $this->assertDatabaseHas('tagihan', [
            'penyewa_id' => $penyewa->id,
            'nominal_total' => 700000,
            'status' => 'pending',
        ]);

        // 4. Assert Reservasi status mutated to 'dikonfirmasi'
        $reservasi->refresh();
        $this->assertEquals('dikonfirmasi', $reservasi->status);
        $this->assertEquals($this->admin->id, $reservasi->dikonfirmasi_oleh);
        $this->assertEquals($penyewa->id, $reservasi->penyewa_id);

        // Assert event dispatched
        Event::assertDispatched(ReservasiDikonfirmasi::class);
    }

    /**
     * Test full payment transition scheme (100% paid, creates paid bill & payment, dispatches PembayaranBerhasil).
     */
    public function test_skema_transisi_pembayaran_lunas_penuh(): void
    {
        Event::fake([ReservasiDikonfirmasi::class, PembayaranBerhasil::class]);

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
            'order_id' => 'RSV-FULL-FLOW-TEST',
            'metode_pembayaran' => 'midtrans',
            'transaction_id' => 'tx-999-full',
        ]);

        request()->merge([
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Dummy',
            'no_wali' => '081234567890',
            'deposit' => 0,
        ]);

        // Run transition
        $service = app(TransisiPenyewaService::class);
        $penyewa = $service->transisi($reservasi, $this->admin->id, request()->all());

        // Assert Penyewa record created
        $this->assertDatabaseHas('penyewa', [
            'id' => $penyewa->id,
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'harga_sewa' => 1000000,
            'status' => 'aktif',
        ]);

        // Assert Kamar status updated to 'terisi'
        $this->kamar->refresh();
        $this->assertEquals('terisi', $this->kamar->status);

        // TODO: Sediakan baris assertion untuk memverifikasi bahwa tabel tagihan dan pembayaran langsung terisi dengan status 'lunas' dan memicu pencetakan kwitansi otomatis.
        // Assert Tagihan and Pembayaran automatically recorded with 'lunas' status
        $this->assertDatabaseHas('tagihan', [
            'penyewa_id' => $penyewa->id,
            'nominal_total' => 1000000,
            'status' => 'lunas',
            'metode_pembayaran' => 'midtrans',
        ]);

        $tagihan = Tagihan::where('penyewa_id', $penyewa->id)->first();

        $this->assertDatabaseHas('pembayaran', [
            'tagihan_id' => $tagihan->id,
            'nominal' => 1000000,
            'payment_type' => 'midtrans',
            'status_midtrans' => 'settlement',
            'transaction_id' => 'tx-999-full',
        ]);

        // Assert events dispatched
        Event::assertDispatched(ReservasiDikonfirmasi::class);
        Event::assertDispatched(PembayaranBerhasil::class);
    }
}
