<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Events\PembayaranCashDikonfirmasi;

class AdminTagihanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tenantUser;
    private Kamar $kamar;
    private Penyewa $penyewa;

    protected function setUp(): void
    {
        parent::setUp();

        // Isolasi HTTP request external
        Http::fake([
            'api.fonnte.com/*' => Http::response([
                'status' => true,
                'message' => 'Pesan terisolasi oleh Fonnte Mock',
            ], 200),
        ]);

        // Create Admin
        $this->admin = User::create([
            'nama' => 'Admin Asep',
            'email' => 'asri-asep@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567890',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        // Create Tenant User
        $this->tenantUser = User::create([
            'nama' => 'Penyewa Budi',
            'email' => 'budi@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567891',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        // Create Kamar
        $this->kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'terisi',
        ]);

        // Create Penyewa
        $this->penyewa = Penyewa::create([
            'user_id' => $this->tenantUser->id,
            'kamar_id' => $this->kamar->id,
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
    }

    /**
     * Test admin dapat mengakses halaman daftar tagihan.
     */
    public function test_admin_can_access_billing_index(): void
    {
        Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-TEST-101',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tagihan.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Tagihan Kost');
        $response->assertSee('TGH-TEST-101');
        $response->assertSee('Penyewa Budi');
        $response->assertSee('Kamar 101');
    }

    /**
     * Test non-admin tidak dapat mengakses halaman daftar tagihan.
     */
    public function test_non_admin_cannot_access_billing_index(): void
    {
        $response = $this->actingAs($this->tenantUser)
            ->get(route('admin.tagihan.index'));

        $response->assertStatus(403); // Forbidden atau redirect ke dashboard
    }

    /**
     * Test admin dapat melihat detail tagihan tertentu.
     */
    public function test_admin_can_access_billing_show(): void
    {
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-TEST-102',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.tagihan.show', $tagihan->id));

        $response->assertStatus(200);
        $response->assertSee('TGH-TEST-102');
        $response->assertSee('Detail Tagihan');
        $response->assertSee('Konfirmasi Pembayaran Cash');
    }

    /**
     * Test admin dapat mengonfirmasi pembayaran cash secara manual.
     */
    public function test_admin_can_confirm_cash_payment(): void
    {
        Event::fake([PembayaranCashDikonfirmasi::class]);

        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-TEST-103',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tagihan.konfirmasiCash', $tagihan->id), [
                'catatan' => 'Pembayaran lunas cash ke Asep',
            ]);

        $response->assertRedirect(route('admin.tagihan.show', $tagihan->id));
        $response->assertSessionHas('success', 'Pembayaran cash dikonfirmasi.');

        $tagihan->refresh();
        $this->assertEquals('lunas', $tagihan->status);
        $this->assertEquals('cash', $tagihan->metode_pembayaran);
        $this->assertEquals('Pembayaran lunas cash ke Asep', $tagihan->keterangan);

        $this->assertDatabaseHas('pembayaran', [
            'tagihan_id' => $tagihan->id,
            'payment_type' => 'cash',
            'nominal' => 1000000,
            'status_midtrans' => 'cash_confirmed',
            'dikonfirmasi_oleh' => $this->admin->id,
        ]);

        Event::assertDispatched(PembayaranCashDikonfirmasi::class, function ($event) use ($tagihan) {
            return $event->pembayaran->tagihan_id === $tagihan->id;
        });
    }

    /**
     * Test admin tidak dapat mengonfirmasi tagihan yang sudah lunas.
     */
    public function test_admin_cannot_confirm_cash_payment_for_already_lunas_bill(): void
    {
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-TEST-104',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'lunas',
            'metode_pembayaran' => 'cash',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tagihan.konfirmasiCash', $tagihan->id), [
                'catatan' => 'Mencoba konfirmasi ulang',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Tagihan sudah lunas atau tidak valid.');
    }

    /**
     * Test validasi catatan pada konfirmasi cash.
     */
    public function test_cash_confirmation_requires_valid_catatan(): void
    {
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-TEST-105',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        // Test catatan terlalu panjang (> 500 karakter)
        $response = $this->actingAs($this->admin)
            ->post(route('admin.tagihan.konfirmasiCash', $tagihan->id), [
                'catatan' => str_repeat('a', 501),
            ]);

        $response->assertSessionHasErrors(['catatan']);
        $tagihan->refresh();
        $this->assertEquals('pending', $tagihan->status);
    }
}
