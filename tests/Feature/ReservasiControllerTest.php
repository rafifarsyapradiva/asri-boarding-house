<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Services\ReservasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservasiControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test booking submission fails if double booking occurs.
     */
    public function test_submisi_booking_gagal_jika_terjadi_double_booking(): void
    {
        // TODO: Buat data dummy user (role penyewa), kamar, dan satu record reservasi eksis di database. Jalankan actingAs($user) untuk mensimulasikan login, kirim POST request ke endpoint `store` dengan rentang tanggal yang bertabrakan, lalu gunakan `assertSessionHasErrors(['tanggal_mulai'])` untuk memastikan sistem menolak booking ganda tersebut.

        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Dummy',
            'no_wali' => '081122334455',
            'require_password_change' => 0,
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        // Mocking the ReservasiService to throw collision exception
        $this->mock(ReservasiService::class, function ($mock) {
            $mock->shouldReceive('buatReservasi')
                ->once()
                ->andThrow(new \Exception('Kamar sudah ter-booking pada rentang tanggal tersebut.'));
        });

        $response = $this->actingAs($user)
            ->post(route('penyewa.reservasi.store'), [
                'kamar_id' => $kamar->id,
                'tipe_sewa' => 'bulanan',
                'tanggal_mulai' => date('Y-m-d'),
                'durasi' => 1,
            ]);

        $response->assertSessionHasErrors(['tanggal_mulai']);
    }

    /**
     * Test AJAX pricing endpoint returns accurate JSON format and values.
     */
    public function test_endpoint_ajax_kalkulasi_harga_memberikan_respons_json_yang_akurat(): void
    {
        // TODO: Kirim POST / GET AJAX request (dengan header X-Requested-With) ke endpoint `landing.hitungHarga` (Tahap 21), berikan payload tipe_sewa 'bulanan' dan durasi 2, lalu pasang `assertJsonStructure()` dan `assertJsonFragment()` untuk memverifikasi nilai total sewa dan nominal minimal DP terhitung dengan tepat.

        $kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $response = $this->postJson(route('landing.hitungHarga', $kamar->id), [
            'tipe_sewa' => 'bulanan',
            'durasi' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'total_harga'
            ])
            ->assertJsonFragment([
                'success' => true,
                'total_harga' => 2000000
            ]);
    }

    public function test_show_page_html(): void
    {
        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $reservasi = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-DUMMY'
        ]);

        $response = $this->actingAs($user)
            ->get(route('penyewa.reservasi.show', $reservasi->id));

        $response->assertStatus(200);
        $html = $response->getContent();
        file_put_contents(base_path('test_response.html'), $html);
    }

    public function test_penyewa_bisa_membatalkan_reservasi_pending(): void
    {
        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $reservasi = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-DUMMY'
        ]);

        $response = $this->actingAs($user)
            ->post(route('penyewa.reservasi.batal', $reservasi->id));

        $response->assertRedirect(route('landing.index'));
        $this->assertEquals('batal', $reservasi->fresh()->status);
    }

    public function test_admin_bisa_membatalkan_reservasi(): void
    {
        $admin = User::create([
            'nama' => 'Admin Asep',
            'email' => 'asri-asep@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567890',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $reservasi = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-DUMMY'
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.reservasi.batal', $reservasi->id));

        $response->assertRedirect(route('admin.reservasi.index'));
        $this->assertEquals('batal', $reservasi->fresh()->status);
    }

    public function test_admin_bisa_menghapus_reservasi(): void
    {
        $admin = User::create([
            'nama' => 'Admin Asep',
            'email' => 'asri-asep@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567890',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $reservasi = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-DUMMY'
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.reservasi.destroy', $reservasi->id));

        $response->assertRedirect(route('admin.reservasi.index'));
        $this->assertSoftDeleted('reservasi', ['id' => $reservasi->id]);
        
        $deletedReservasi = \App\Models\Reservasi::withTrashed()->find($reservasi->id);
        $this->assertStringContainsString('_deleted_', $deletedReservasi->order_id);
    }

    public function test_penyewa_bisa_mengakses_indeks_reservasi(): void
    {
        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $response = $this->actingAs($user)
            ->get(route('penyewa.reservasi.index'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Pemesanan Kamar');
    }

    public function test_penyewa_bisa_mengakses_riwayat_pembayaran(): void
    {
        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $penyewa = \App\Models\Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
            'deposit' => 500000,
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Dummy',
        ]);

        $tagihan = \App\Models\Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'INV-DUMMY',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => date('Y-m-d'),
            'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+7 days')),
            'nominal_pokok' => 1000000,
            'nominal_denda' => 0,
            'nominal_total' => 1000000,
            'status' => 'lunas',
        ]);

        $pembayaran = \App\Models\Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'transaction_id' => 'TX-DUMMY',
            'payment_type' => 'bank_transfer',
            'nominal' => 1000000,
            'status_midtrans' => 'settlement',
            'tanggal_bayar' => date('Y-m-d H:i:s'),
        ]);

        $response = $this->actingAs($user)
            ->get(route('penyewa.pembayaran'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Pembayaran');
        $response->assertSee('INV-DUMMY');
    }

    public function test_penyewa_bisa_mengakses_halaman_chat_reservasi(): void
    {
        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $reservasi = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-DUMMY'
        ]);

        $response = $this->actingAs($user)
            ->get(route('penyewa.reservasi.chat', $reservasi->id));

        $response->assertStatus(200);
        $response->assertSee('Obrolan Reservasi');
    }

    public function test_penyewa_bisa_mengakses_riwayat_chat(): void
    {
        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $reservasi = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-DUMMY'
        ]);

        $message = \App\Models\ChatMessage::create([
            'reservasi_id' => $reservasi->id,
            'sender_id' => $user->id,
            'message' => 'Halo Admin, kamar ini masih ready?',
            'is_read' => false,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $response = $this->actingAs($user)
            ->get(route('penyewa.chat.history'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Chat');
        $response->assertSee('Halo Admin, kamar ini masih ready?');
    }

    public function test_riwayat_chat_menampilkan_pesan_terakhir_untuk_semua_reservasi(): void
    {
        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar1 = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $kamar2 = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $reservasi1 = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar1->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-DUMMY-1'
        ]);

        $reservasi2 = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar2->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-DUMMY-2'
        ]);

        \App\Models\ChatMessage::create([
            'reservasi_id' => $reservasi1->id,
            'sender_id' => $user->id,
            'message' => 'Pesan Reservasi Satu',
            'is_read' => false,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        \App\Models\ChatMessage::create([
            'reservasi_id' => $reservasi2->id,
            'sender_id' => $user->id,
            'message' => 'Pesan Reservasi Dua',
            'is_read' => false,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $response = $this->actingAs($user)
            ->get(route('penyewa.chat.history'));

        $response->assertStatus(200);
        $response->assertSee('Pesan Reservasi Satu');
        $response->assertSee('Pesan Reservasi Dua');
    }

    public function test_snap_token_reservasi_di_reuse_jika_kurang_dari_24_jam(): void
    {
        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $reservasi = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-TMP-123',
            'snap_token' => 'token-cached-12345'
        ]);

        $reservasi->updated_at = now();
        $reservasi->save();

        $response = $this->actingAs($user)
            ->get(route('penyewa.reservasi.show', $reservasi->id));

        $response->assertStatus(200);
        $this->assertEquals('token-cached-12345', $reservasi->fresh()->snap_token);
    }

    public function test_penyewa_bisa_melihat_obrolan_riwayat_meskipun_dikonfirmasi_atau_batal(): void
    {
        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $reservasiConfirm = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'dikonfirmasi',
            'order_id' => 'RSV-CONFIRMED'
        ]);

        $reservasiBatal = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'batal',
            'order_id' => 'RSV-CANCELLED'
        ]);

        $response = $this->actingAs($user)
            ->get(route('penyewa.chat.history'));

        $response->assertStatus(200);
        $response->assertSee('Lihat Obrolan');
        
        // Assert they can actually open the chat page
        $responseChat = $this->actingAs($user)
            ->get(route('penyewa.reservasi.chat', $reservasiConfirm->id));
        $responseChat->assertStatus(200);
        $responseChat->assertSee('Obrolan dinonaktifkan karena reservasi ini telah dikonfirmasi');

        $responseChatBatal = $this->actingAs($user)
            ->get(route('penyewa.reservasi.chat', $reservasiBatal->id));
        $responseChatBatal->assertStatus(200);
        $responseChatBatal->assertSee('Obrolan dinonaktifkan karena reservasi ini telah dibatalkan');
    }

    /**
     * Test payment route renders successfully and shows active sidebar link.
     */
    public function test_pembayaran_route_renders_same_page_and_contains_sidebar_link(): void
    {
        $user = User::create([
            'nama' => 'Penyewa Dummy Baru',
            'email' => 'penyewa_baru@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567895',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '109',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $reservasi = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-DUMMY-PAY'
        ]);

        $response = $this->actingAs($user)
            ->get(route('penyewa.reservasi.pembayaran', $reservasi->id));

        $response->assertStatus(200);
        $response->assertSee('Pembayaran Reservasi');
        $response->assertSee(route('penyewa.reservasi.pembayaran', $reservasi->id));
    }
}
