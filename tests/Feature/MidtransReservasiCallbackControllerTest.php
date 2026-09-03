<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Reservasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MidtransReservasiCallbackControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Kamar $kamar;
    private string $serverKey = 'dummy_server_key';

    protected function setUp(): void
    {
        parent::setUp();
        
        Config::set('midtrans.server_key', $this->serverKey);

        $this->user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
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
     * Helper to generate midtrans signature key.
     */
    private function generateSignature(string $orderId, string $statusCode, string $grossAmount): string
    {
        return hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
    }

    /**
     * Test webhook returns 403 on invalid signature.
     */
    public function test_callback_ditolak_jika_signature_tidak_valid(): void
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
            'order_id' => 'RSV-1-1234567890'
        ]);

        $response = $this->postJson(route('api.midtrans.callback-reservasi'), [
            'order_id' => $reservasi->order_id,
            'status_code' => '200',
            'gross_amount' => '1000000',
            'signature_key' => 'invalid_signature_key',
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card',
            'transaction_id' => 'tx-123'
        ]);

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Unauthorized']);
    }

    /**
     * Test webhook updates status to dp if is_dp is true.
     */
    public function test_callback_mengubah_status_menjadi_dp_jika_reservasi_dp(): void
    {
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
            'status' => 'pending',
            'order_id' => 'RSV-1-1234567890'
        ]);

        $signature = $this->generateSignature($reservasi->order_id, '200', '300000');

        $response = $this->postJson(route('api.midtrans.callback-reservasi'), [
            'order_id' => $reservasi->order_id,
            'status_code' => '200',
            'gross_amount' => '300000',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card',
            'transaction_id' => 'tx-123'
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Callback processed successfully']);

        $reservasi->refresh();
        $this->assertEquals('dp', $reservasi->status);
        $this->assertEquals('tx-123', $reservasi->transaction_id);
        $this->assertEquals('midtrans', $reservasi->metode_pembayaran);
        $this->assertNotNull($reservasi->tanggal_konfirmasi);
    }

    /**
     * Test webhook updates status to lunas if is_dp is false.
     */
    public function test_callback_mengubah_status_menjadi_lunas_jika_reservasi_lunas_penuh(): void
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
            'order_id' => 'RSV-2-1234567890'
        ]);

        $signature = $this->generateSignature($reservasi->order_id, '200', '1000000');

        $response = $this->postJson(route('api.midtrans.callback-reservasi'), [
            'order_id' => $reservasi->order_id,
            'status_code' => '200',
            'gross_amount' => '1000000',
            'signature_key' => $signature,
            'transaction_status' => 'capture',
            'payment_type' => 'credit_card',
            'transaction_id' => 'tx-456'
        ]);

        $response->assertStatus(200);

        $reservasi->refresh();
        $this->assertEquals('lunas', $reservasi->status);
        $this->assertEquals('tx-456', $reservasi->transaction_id);
    }

    /**
     * Test webhook returns 404 when reservation is not found.
     */
    public function test_callback_memberikan_404_jika_reservasi_tidak_ditemukan(): void
    {
        $signature = $this->generateSignature('RSV-UNKNOWN', '200', '1000000');

        $response = $this->postJson(route('api.midtrans.callback-reservasi'), [
            'order_id' => 'RSV-UNKNOWN',
            'status_code' => '200',
            'gross_amount' => '1000000',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card',
            'transaction_id' => 'tx-789'
        ]);

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Reservation not found']);
    }

    /**
     * Test webhook idempotency returns early with 200.
     */
    public function test_callback_idempotency_kembali_200_jika_sudah_diproses(): void
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
            'status' => 'lunas',
            'order_id' => 'RSV-3-1234567890'
        ]);

        $signature = $this->generateSignature($reservasi->order_id, '200', '1000000');

        $response = $this->postJson(route('api.midtrans.callback-reservasi'), [
            'order_id' => $reservasi->order_id,
            'status_code' => '200',
            'gross_amount' => '1000000',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card',
            'transaction_id' => 'tx-dup'
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Reservation transaction already handled']);

        $reservasi->refresh();
        $this->assertEquals('lunas', $reservasi->status);
        $this->assertNull($reservasi->transaction_id); // should not be updated since we returned early
    }

    /**
     * Test webhook cancels reservation.
     */
    public function test_callback_membatalkan_reservasi_jika_status_pembayaran_deny_cancel_expire(): void
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
            'order_id' => 'RSV-4-1234567890'
        ]);

        $signature = $this->generateSignature($reservasi->order_id, '200', '1000000');

        $response = $this->postJson(route('api.midtrans.callback-reservasi'), [
            'order_id' => $reservasi->order_id,
            'status_code' => '200',
            'gross_amount' => '1000000',
            'signature_key' => $signature,
            'transaction_status' => 'expire',
            'payment_type' => 'credit_card',
            'transaction_id' => 'tx-failed'
        ]);

        $response->assertStatus(200);

        $reservasi->refresh();
        $this->assertEquals('batal', $reservasi->status);
    }

    /**
     * Test webhook fallback matching using regex if database order_id differs (e.g. after refresh).
     */
    public function test_callback_berhasil_mencocokkan_order_id_lama_menggunakan_regex_fallback(): void
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
            'order_id' => 'RSV-TMP-WILL-CHANGE'
        ]);

        $reservasiId = $reservasi->id;
        $oldOrderId = "RSV-{$reservasiId}-1111111111";
        $newOrderId = "RSV-{$reservasiId}-2222222222";

        $reservasi->update(['order_id' => $newOrderId]);

        $signature = $this->generateSignature($oldOrderId, '200', '1000000');

        $response = $this->postJson(route('api.midtrans.callback-reservasi'), [
            'order_id' => $oldOrderId,
            'status_code' => '200',
            'gross_amount' => '1000000',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card',
            'transaction_id' => 'tx-fallback-ok'
        ]);

        $response->assertStatus(200);

        $reservasi->refresh();
        $this->assertEquals('lunas', $reservasi->status);
        $this->assertEquals('tx-fallback-ok', $reservasi->transaction_id);
    }
}
