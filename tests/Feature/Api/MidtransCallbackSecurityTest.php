<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MidtransCallbackSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_midtrans_callback_rejects_invalid_signature()
    {
        $payload = [
            'order_id' => 'TGH-999-202607-1721445700',
            'status_code' => '200',
            'gross_amount' => '500000',
            'transaction_status' => 'settlement',
            'transaction_id' => 'TRX-INVALID-SIG',
            'signature_key' => 'fake_invalid_sha512_hash',
        ];

        $response = $this->postJson('/api/midtrans/callback', $payload);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Unauthorized']);
    }

    public function test_midtrans_reservasi_callback_rejects_invalid_signature()
    {
        $payload = [
            'order_id' => 'RSV-999-1721445700',
            'status_code' => '200',
            'gross_amount' => '300000',
            'transaction_status' => 'settlement',
            'transaction_id' => 'TRX-RSV-INVALID-SIG',
            'signature_key' => 'fake_invalid_sha512_hash',
        ];

        $response = $this->postJson('/api/midtrans/callback-reservasi', $payload);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Unauthorized']);
    }
}
