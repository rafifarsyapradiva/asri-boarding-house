<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Events\PembayaranBerhasil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MidtransCallbackControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Kamar $kamar;
    private Penyewa $penyewa;
    private string $serverKey = 'dummy_server_key';

    protected function setUp(): void
    {
        parent::setUp();
        
        Config::set('midtrans.server_key', $this->serverKey);

        $this->user = User::create([
            'nama' => 'Penyewa Budi',
            'email' => 'budi.tagihan@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $this->kamar = Kamar::create([
            'nomor_kamar' => '201',
            'lantai' => 2,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'terisi'
        ]);

        $this->penyewa = Penyewa::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
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
     * Helper to generate midtrans signature key.
     */
    private function generateSignature(string $orderId, string $statusCode, string $grossAmount): string
    {
        return hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
    }

    /**
     * Test webhook returns 403 on invalid signature.
     */
    public function test_callback_tagihan_ditolak_jika_signature_tidak_valid(): void
    {
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-BUDI-201',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $response = $this->postJson(route('api.midtrans.callback'), [
            'order_id' => $tagihan->order_id,
            'status_code' => '200',
            'gross_amount' => '1000000.00',
            'signature_key' => 'invalid_signature_key',
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card',
            'transaction_id' => 'tx-123'
        ]);

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Unauthorized']);
    }

    /**
     * Test webhook updates status to lunas and creates Pembayaran record.
     */
    public function test_callback_tagihan_mengubah_status_menjadi_lunas_dan_membuat_pembayaran(): void
    {
        Event::fake();

        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-BUDI-202',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $signature = $this->generateSignature($tagihan->order_id, '200', '1000000.00');

        $response = $this->postJson(route('api.midtrans.callback'), [
            'order_id' => $tagihan->order_id,
            'status_code' => '200',
            'gross_amount' => '1000000.00',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'tx-settle-102',
            'va_numbers' => [
                [
                    'bank' => 'bca',
                    'va_number' => '1234567890'
                ]
            ],
            'settlement_time' => '2026-06-17 18:00:00'
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Callback handled successfully']);

        $tagihan->refresh();
        $this->assertEquals('lunas', $tagihan->status);
        $this->assertEquals('midtrans', $tagihan->metode_pembayaran);

        $this->assertDatabaseHas('pembayaran', [
            'tagihan_id' => $tagihan->id,
            'transaction_id' => 'tx-settle-102',
            'payment_type' => 'bank_transfer',
            'bank' => 'bca',
            'va_number' => '1234567890',
            'nominal' => 1000000.00,
            'status_midtrans' => 'settlement'
        ]);

        Event::assertDispatched(PembayaranBerhasil::class);
    }

    /**
     * Test webhook idempotency returns early.
     */
    public function test_callback_tagihan_idempotency_kembali_200_jika_sudah_lunas(): void
    {
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-BUDI-203',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'lunas',
        ]);

        $pembayaran = Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'transaction_id' => 'tx-dup-203',
            'payment_type' => 'credit_card',
            'nominal' => 1000000.00,
            'status_midtrans' => 'settlement'
        ]);

        $signature = $this->generateSignature($tagihan->order_id, '200', '1000000.00');

        $response = $this->postJson(route('api.midtrans.callback'), [
            'order_id' => $tagihan->order_id,
            'status_code' => '200',
            'gross_amount' => '1000000.00',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card',
            'transaction_id' => 'tx-dup-203'
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Already processed']);
    }

    /**
     * Test fallback matches order_id with timestamp suffix.
     */
    public function test_callback_tagihan_berhasil_mencocokkan_order_id_menggunakan_fallback_suffix(): void
    {
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-BUDI-204',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $dynamicOrderId = $tagihan->order_id . '-' . time();
        $signature = $this->generateSignature($dynamicOrderId, '200', '1000000.00');

        $response = $this->postJson(route('api.midtrans.callback'), [
            'order_id' => $dynamicOrderId,
            'status_code' => '200',
            'gross_amount' => '1000000.00',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card',
            'transaction_id' => 'tx-fallback-204'
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Callback handled successfully']);

        $tagihan->refresh();
        $this->assertEquals('lunas', $tagihan->status);
    }

    /**
     * Data provider for testing multiple payment channels.
     */
    public static function paymentChannelProvider(): array
    {
        return [
            'Permata Virtual Account' => [
                [
                    'payment_type' => 'bank_transfer',
                    'permata_va_number' => '87780000001',
                ],
                'permata',
                '87780000001'
            ],
            'Mandiri Bill / E-Channel' => [
                [
                    'payment_type' => 'echannel',
                    'bill_key' => '99000101',
                ],
                'mandiri',
                '99000101'
            ],
            'Convenience Store (Alfamart)' => [
                [
                    'payment_type' => 'cstore',
                    'store' => 'alfamart',
                    'payment_code' => 'ALFA98765',
                ],
                'alfamart',
                'ALFA98765'
            ],
            'GoPay / QRIS' => [
                [
                    'payment_type' => 'gopay',
                ],
                'gopay',
                null
            ]
        ];
    }

    #[DataProvider('paymentChannelProvider')]
    public function test_callback_tagihan_mengekstrak_bank_dan_va_yang_tepat_untuk_berbagai_channel(array $payload, ?string $expectedBank, ?string $expectedVa): void
    {
        $tagihan = Tagihan::create([
            'penyewa_id' => $this->penyewa->id,
            'order_id' => 'TGH-CHANNEL-' . rand(1000, 9999),
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $signature = $this->generateSignature($tagihan->order_id, '200', '1000000.00');

        $basePayload = [
            'order_id' => $tagihan->order_id,
            'status_code' => '200',
            'gross_amount' => '1000000.00',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'transaction_id' => 'tx-' . uniqid(),
            'settlement_time' => '2026-06-17 18:00:00'
        ];

        $fullPayload = array_merge($basePayload, $payload);

        $response = $this->postJson(route('api.midtrans.callback'), $fullPayload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('pembayaran', [
            'tagihan_id' => $tagihan->id,
            'bank' => $expectedBank,
            'va_number' => $expectedVa,
            'status_midtrans' => 'settlement'
        ]);
    }
}
