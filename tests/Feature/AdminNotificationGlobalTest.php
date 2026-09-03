<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Pengumuman;
use App\Models\LogNotifikasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationGlobalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tenantUser;
    private Kamar $kamar;
    private Penyewa $penyewa;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin User
        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin.notif@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        // Create Tenant User
        $this->tenantUser = User::create([
            'nama' => 'Penyewa Aktif',
            'email' => 'penyewa.notif@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        // Create Kamar
        $this->kamar = Kamar::create([
            'nomor_kamar' => '104',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1200000,
            'status' => 'terisi'
        ]);

        // Create Penyewa record
        $this->penyewa = Penyewa::create([
            'user_id' => $this->tenantUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Test',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);
    }

    public function test_admin_bisa_mengakses_halaman_notifikasi(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.notifikasi.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Notifikasi &amp; Pengumuman Global', false);
        $response->assertSee('Kirim Broadcast / Pengumuman');
        $response->assertSee('Log Riwayat Notifikasi');
    }

    public function test_non_admin_tidak_bisa_mengakses_halaman_notifikasi(): void
    {
        $response = $this->actingAs($this->tenantUser)
            ->get(route('admin.notifikasi.index'));

        $response->assertStatus(403);
    }

    public function test_admin_bisa_mengirim_broadcast_posting_web(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.notifikasi.broadcast'), [
                'target' => 'all',
                'pesan' => 'Ini adalah pengumuman penting untuk semua penyewa kost.',
                'judul' => 'Pengumuman Kerja Bakti',
                'post_to_web' => '1',
            ]);

        $response->assertRedirect(route('admin.notifikasi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengumuman', [
            'judul' => 'Pengumuman Kerja Bakti',
            'isi' => 'Ini adalah pengumuman penting untuk semua penyewa kost.',
            'is_active' => true,
        ]);
    }

    public function test_admin_bisa_mengirim_broadcast_wa_dan_email(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.notifikasi.broadcast'), [
                'target' => (string) $this->penyewa->id,
                'pesan' => 'Ini adalah pesan kustom via WhatsApp dan Email.',
                'send_wa' => '1',
                'send_email' => '1',
            ]);

        $response->assertRedirect(route('admin.notifikasi.index'));
        $response->assertSessionHas('success');

        // Check if logs are successfully created
        $this->assertDatabaseHas('log_notifikasi', [
            'penyewa_id' => $this->penyewa->id,
            'channel' => 'whatsapp',
            'event' => 'broadcast_admin',
            'status' => 'sukses',
            'pesan' => 'Ini adalah pesan kustom via WhatsApp dan Email.',
        ]);

        $this->assertDatabaseHas('log_notifikasi', [
            'penyewa_id' => $this->penyewa->id,
            'channel' => 'email',
            'event' => 'broadcast_admin',
            'status' => 'sukses',
            'pesan' => 'Ini adalah pesan kustom via WhatsApp dan Email.',
        ]);
    }

    public function test_admin_bisa_menghapus_pengumuman(): void
    {
        $pengumuman = Pengumuman::create([
            'judul' => 'Pengumuman Untuk Dihapus',
            'isi' => 'Konten pengumuman',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.notifikasi.destroyPengumuman', $pengumuman->id));

        $response->assertRedirect(route('admin.notifikasi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('pengumuman', [
            'id' => $pengumuman->id,
        ]);
    }

    public function test_penyewa_bisa_melihat_pengumuman_di_dashboard(): void
    {
        // Create an announcement
        Pengumuman::create([
            'judul' => 'Pengumuman Kerja Bakti',
            'isi' => 'Pengumuman kerja bakti hari Minggu jam 8 pagi.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->tenantUser)
            ->get(route('penyewa.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pengumuman Kerja Bakti');
        $response->assertSee('Pengumuman kerja bakti hari Minggu jam 8 pagi.');
    }

    public function test_penyewa_bisa_mengakses_halaman_notifikasi_saya(): void
    {
        $response = $this->actingAs($this->tenantUser)
            ->get(route('penyewa.notifikasi.index'));

        $response->assertStatus(200);
        $response->assertSee('Notifikasi Saya');
        $response->assertSee('Riwayat Notifikasi Saya');
    }

    public function test_admin_bisa_mengirim_ulang_notifikasi_yang_gagal(): void
    {
        $log = LogNotifikasi::create([
            'penyewa_id' => $this->penyewa->id,
            'tagihan_id' => null,
            'channel' => 'whatsapp',
            'event' => 'broadcast_admin',
            'status' => 'gagal',
            'pesan' => 'Pesan gagal dikirim sebelumnya.',
            'error_msg' => 'Fonnte error',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.notifikasi.retry', $log->id));

        $response->assertRedirect(route('admin.notifikasi.index'));
        $response->assertSessionHas('success');

        $log->refresh();
        $this->assertEquals('sukses', $log->status);
        $this->assertNull($log->error_msg);
    }

    public function test_admin_bisa_mengirim_ulang_pembayaran_sukses(): void
    {
        // 1. Create a Tagihan
        $tagihan = \App\Models\Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-TEST-RETRY',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => date('Y-m-d'),
            'tanggal_jatuh_tempo' => date('Y-m-d'),
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'lunas',
        ]);

        // 2. Create a Pembayaran
        $pembayaran = \App\Models\Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'transaction_id' => 'TX-TEST-RETRY',
            'nominal' => 1000000,
            'payment_type' => 'cash',
            'tanggal_bayar' => now(),
            'status_midtrans' => 'settlement',
        ]);

        // 3. Create a failed log
        $log = LogNotifikasi::create([
            'penyewa_id' => $this->penyewa->id,
            'tagihan_id' => $tagihan->id,
            'channel' => 'whatsapp',
            'event' => 'pembayaran_sukses',
            'status' => 'gagal',
            'pesan' => null, // empty, will be generated dynamically
            'error_msg' => 'Fonnte error',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.notifikasi.retry', $log->id));

        $response->assertRedirect(route('admin.notifikasi.index'));
        $response->assertSessionHas('success');

        $log->refresh();
        $this->assertEquals('sukses', $log->status);
        $this->assertNull($log->error_msg);
        $this->assertNotNull($log->pesan);
        $this->assertStringContainsString('pembayaran sewa Anda untuk periode *6/2026* telah diterima', $log->pesan);
    }

    public function test_penyewa_bisa_melihat_pesan_notifikasi_otomatis_dan_dinamis_aman_dari_error(): void
    {
        // 1. Create a Tagihan
        $tagihan = \App\Models\Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-TEST-DYNAMIC',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => date('Y-m-d'),
            'tanggal_jatuh_tempo' => date('Y-m-d'),
            'nominal_pokok' => 1200000,
            'nominal_total' => 1200000,
            'status' => 'pending',
        ]);

        // 2. Log with null pesan (legacy/existing log)
        LogNotifikasi::create([
            'penyewa_id' => $this->penyewa->id,
            'tagihan_id' => $tagihan->id,
            'channel' => 'whatsapp',
            'event' => 'tagihan_baru',
            'status' => 'sukses',
            'pesan' => null, // null, will trigger fallback on index
        ]);

        // 3. Log with special quotes and newlines
        LogNotifikasi::create([
            'penyewa_id' => $this->penyewa->id,
            'tagihan_id' => null,
            'channel' => 'email',
            'event' => 'broadcast_admin',
            'status' => 'sukses',
            'pesan' => "Halo \"Penyewa\"!\nAda pengumuman 'penting' dari pengelola.",
        ]);

        $response = $this->actingAs($this->tenantUser)
            ->get(route('penyewa.notifikasi.index'));

        $response->assertStatus(200);
        
        // Fallback checks
        $response->assertSee('Tagihan sewa Anda untuk periode bulan *6/2026* telah diterbitkan');
        
        // Special character rendering checks
        $response->assertSee('Halo');
        $response->assertSee('Penyewa');
        $response->assertSee('penting');
    }
}


