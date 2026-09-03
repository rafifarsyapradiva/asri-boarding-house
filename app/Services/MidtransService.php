<?php

namespace App\Services;

use App\Models\Tagihan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class MidtransService
{
    /**
     * Membuat token pembayaran reguler (Snap Token) dari Midtrans.
     */
    public function createSnapToken(Tagihan $tagihan): array
    {
        $penyewa = $tagihan->penyewa;
        $user = $penyewa ? $penyewa->user : null;
        $kamar = $penyewa ? $penyewa->kamar : null;

        $baseUrl = config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $itemDetails = [];
        $nomorKamar = $tagihan->penyewa?->kamar?->nomor_kamar ?? '-';

        $pricePokok = (int) $tagihan->nominal_pokok;
        $priceDenda = (int) $tagihan->nominal_denda;
        $grossAmount = (int) $tagihan->nominal_total;

        // Antisipasi ketidakcocokan: Jika nominal_total tidak sesuai dengan jumlahan pokok + denda,
        // kita sesuaikan pokok agar jumlah total item detail selalu sama dengan gross_amount (nominal_total)
        if ($grossAmount !== ($pricePokok + $priceDenda)) {
            $pricePokok = $grossAmount - $priceDenda;
        }

        $itemDetails[] = [
            'id' => 'POKOK-' . $tagihan->id,
            'name' => 'Sewa Kamar ' . $nomorKamar,
            'price' => $pricePokok,
            'quantity' => 1,
        ];

        if ($priceDenda > 0) {
            $itemDetails[] = [
                'id' => 'DENDA-' . $tagihan->id,
                'name' => 'Denda Keterlambatan Kalender',
                'price' => $priceDenda,
                'quantity' => 1,
            ];
        }

        $timestamp = time();
        $orderId = $tagihan->order_id . '-' . $timestamp;

        $now = Carbon::now();
        $hoursUntilEndOfMonth = $now->diffInHours($now->copy()->endOfMonth());
        $duration = $hoursUntilEndOfMonth < 24 ? max(1, $hoursUntilEndOfMonth) : 24;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $tagihan->penyewa?->user?->nama ?? 'Guest',
                'email' => $tagihan->penyewa?->user?->email ?? '',
                'phone' => $tagihan->penyewa?->user?->no_hp ?? '',
            ],
            'expiry' => [
                'start_time' => $now->format('Y-m-d H:i:s O'),
                'duration' => $duration,
                'unit' => 'hours',
            ],
            'item_details' => $itemDetails,
        ];

        $serverKey = config('midtrans.server_key');
        $notificationUrl = route('api.midtrans.callback');

        $response = Http::withBasicAuth($serverKey, '')
            ->withHeaders([
                'X-Override-Notification' => $notificationUrl,
            ])
            ->post($baseUrl, $params);

        if (!$response->successful()) {
            throw new \Exception('Gagal membuat token Snap Midtrans: ' . $response->body());
        }

        return [
            'snap_token' => $response->json('token'),
            'order_id' => $orderId,
        ];
    }

    /**
     * Membuat token pembayaran (Snap Token) dari Midtrans khusus untuk Reservasi Online.
     */
    public function createSnapTokenReservasi(\App\Models\Reservasi $reservasi): array
    {
        $user = $reservasi->user;
        $kamar = $reservasi->kamar;

        $baseUrl = config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $grossAmount = $reservasi->is_dp ? $reservasi->nominal_dp : $reservasi->total_harga;
        
        $timestamp = time();
        $orderId = "RSV-{$reservasi->id}-{$timestamp}";

        $itemName = sprintf('Reservasi Kamar %s — %s (%d %s)',
            $kamar ? $kamar->nomor_kamar : '-',
            ucfirst($reservasi->tipe_sewa),
            $reservasi->durasi,
            $reservasi->tipe_sewa
        );
        if ($reservasi->is_dp) {
            $itemName .= ' [DP 30%]';
        } else {
            $itemName .= ' [Full Payment]';
        }

        // Midtrans Snap name length limit is 50 characters
        if (strlen($itemName) > 50) {
            $itemName = substr($itemName, 0, 47) . '...';
        }

        $itemDetails = [[
            'id' => 'RSV-' . $reservasi->id,
            'price' => (int) $grossAmount,
            'quantity' => 1,
            'name' => $itemName,
        ]];

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $user ? $user->nama : 'Guest',
                'email' => $user ? $user->email : '',
                'phone' => $user ? $user->no_hp : '',
            ],
            'expiry' => [
                'start_time' => Carbon::now()->format('Y-m-d H:i:s O'),
                'duration' => 24,
                'unit' => 'hours',
            ],
            'item_details' => $itemDetails,
        ];

        $serverKey = config('midtrans.server_key');
        $notificationUrl = route('api.midtrans.callback-reservasi');

        $response = Http::withBasicAuth($serverKey, '')
            ->withHeaders([
                'X-Override-Notification' => $notificationUrl,
            ])
            ->post($baseUrl, $params);

        if (!$response->successful()) {
            throw new \Exception('Gagal membuat token Snap Midtrans untuk Reservasi: ' . $response->body());
        }

        $snapToken = $response->json('token');

        // Update order_id dan snap_token di database agar sinkron saat callback terpicu
        $reservasi->update([
            'order_id' => $orderId,
            'snap_token' => $snapToken,
        ]);

        return [
            'snap_token' => $snapToken,
            'order_id' => $orderId,
        ];
    }
}
