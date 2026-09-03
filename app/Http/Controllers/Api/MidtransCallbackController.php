<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    /**
     * Menangani webhook callback server-to-server dari Midtrans.
     */
    public function handle(Request $request): JsonResponse
    {
        $orderId = (string) $request->input('order_id');
        $statusCode = (string) $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $serverKey = (string) config('midtrans.server_key');
        $signatureKey = (string) $request->input('signature_key');

        $grossAmountFormatted = is_numeric($grossAmount)
            ? number_format((float)$grossAmount, 2, '.', '')
            : $grossAmount;

        $validSignature1 = hash("sha512", $orderId . $statusCode . $grossAmountFormatted . $serverKey);
        $validSignature2 = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);

        // Validasi keaslian callback via Signature Key Midtrans (SHA512 dengan perlindungan Timing Attack)
        if (!hash_equals($validSignature1, $signatureKey) && !hash_equals($validSignature2, $signatureKey)) {
            Log::warning('Midtrans callback invalid signature key attempt', [
                'order_id' => $orderId,
                'ip' => $request->ip(),
            ]);
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $transactionId = $request->input('transaction_id');

        // Fast-path IDEMPOTENCY CHECK (tanpa lock, untuk request biasa/lambat)
        $existingPembayaran = Pembayaran::where('transaction_id', $transactionId)->first();
        if ($existingPembayaran && in_array($existingPembayaran->status_midtrans, ['settlement', 'capture'])) {
            return response()->json(['message' => 'Already processed'], 200);
        }
        
        // Ekstrak timestamp pembuatan token Snap dari order_id (misal: TGH-123-202607-1721445700)
        $timestamp = null;
        if (preg_match('/-(\d{10,})$/', $orderId, $matches)) {
            $timestamp = (int)$matches[1];
        }

        // Gunakan regex untuk menghapus suffix timestamp dinamis (10 digit numerik) di akhir order_id
        $baseOrderId = preg_replace('/-\d{10,}$/', '', $orderId);
        $tagihan = Tagihan::where('order_id', $baseOrderId)->first();

        if (!$tagihan) {
            Log::warning('Midtrans callback tagihan not found', ['order_id' => $orderId, 'base_order_id' => $baseOrderId]);
            return response()->json(['message' => 'Tagihan not found'], 404);
        }

        $transactionStatus = $request->input('transaction_status');

        // Mappings status Midtrans ke status tagihan kost Asri Boarding House
        $statusMap = [
            'capture' => 'lunas',
            'settlement' => 'lunas',
            'pending' => 'pending',
            'deny' => 'gagal',
            'expire' => 'pending',
            'cancel' => 'pending',
        ];

        $tagihanStatus = $statusMap[$transactionStatus] ?? 'pending';

        $result = DB::transaction(function () use ($tagihan, $tagihanStatus, $grossAmount, $request, $timestamp, $transactionId, $transactionStatus) {
            // Lock data menggunakan lockForUpdate untuk mengantisipasi race condition
            $lockedTagihan = Tagihan::where('id', $tagihan->id)->lockForUpdate()->first();

            if (!$lockedTagihan) {
                return ['status' => 404, 'message' => 'Tagihan not found'];
            }

            // IDEMPOTENCY LOCK: Cek pembayaran di bawah protection lock pesimistik
            $lockedPembayaran = Pembayaran::where('transaction_id', $transactionId)
                ->lockForUpdate()
                ->first();

            if ($lockedPembayaran && in_array($lockedPembayaran->status_midtrans, ['settlement', 'capture'])) {
                return ['status' => 200, 'message' => 'Already processed'];
            }

            if ($lockedTagihan->status === 'lunas') {
                return ['status' => 200, 'message' => 'Already processed'];
            }

            // VALIDASI KEAMANAN FINANSIAL DI DALAM TRANSAKSI
            if ((int) $grossAmount !== (int) $lockedTagihan->nominal_total) {
                // Skenario Waiving Denda: Jika grossAmount sama dengan nominal_pokok dan ada denda,
                // hapus denda secara otomatis agar pembayaran Midtrans Snap yang terlanjur terbit tetap sukses.
                // KEAMANAN KRITIS: Hanya ijinkan waiving jika token dibuat pada bulan/periode kalender yang sama dengan saat ini.
                $tokenCreatedMonth = $timestamp ? (int)date('n', $timestamp) : null;
                $tokenCreatedYear = $timestamp ? (int)date('Y', $timestamp) : null;
                
                $isSameMonth = $tokenCreatedMonth && $tokenCreatedYear &&
                    ($tokenCreatedMonth === (int)date('n') && $tokenCreatedYear === (int)date('Y'));

                if ($lockedTagihan->nominal_denda > 0 && 
                    (int) $grossAmount === (int) $lockedTagihan->nominal_pokok && 
                    $isSameMonth) {
                    
                    Log::info('Midtrans callback matching nominal_pokok. Waiving denda since it was applied after snap token generation within the same month.', [
                        'tagihan_id' => $lockedTagihan->id,
                        'gross_amount' => $grossAmount,
                        'original_denda' => $lockedTagihan->nominal_denda
                    ]);
                    $lockedTagihan->update([
                        'nominal_denda' => 0,
                        'nominal_total' => $lockedTagihan->nominal_pokok,
                    ]);
                } else {
                    Log::error('Midtrans callback gross_amount mismatch with tagihan nominal_total inside transaction', [
                        'order_id' => $request->input('order_id'),
                        'received_gross_amount' => $grossAmount,
                        'expected_nominal_total' => $lockedTagihan->nominal_total,
                        'token_time' => $timestamp ? date('Y-m-d H:i:s', $timestamp) : 'N/A',
                        'is_same_month' => $isSameMonth ? 'Yes' : 'No',
                    ]);
                    return ['status' => 400, 'message' => 'Gross amount mismatch'];
                }
            }

            // Mencegah Status Regression: Jangan menurunkan status dari 'terlambat' kembali ke 'pending'
            $newStatus = $tagihanStatus;
            if ($lockedTagihan->status === 'terlambat' && $tagihanStatus === 'pending') {
                $newStatus = 'terlambat';
            }

            // Update status tagihan
            $lockedTagihan->update([
                'status' => $newStatus,
                'metode_pembayaran' => 'midtrans',
            ]);

            // Ekstrak bank & va_number jika ada
            [$bank, $vaNumber] = $this->parsePaymentDetails($request);

            // Simpan / update record pembayaran
            $pembayaran = Pembayaran::updateOrCreate([
                'transaction_id' => $transactionId,
            ], [
                'tagihan_id' => $lockedTagihan->id,
                'payment_type' => $request->input('payment_type'),
                'bank' => $bank,
                'va_number' => $vaNumber,
                'nominal' => $grossAmount,
                'status_midtrans' => $transactionStatus,
                'signature_key' => $request->input('signature_key'),
                'response_json' => $request->all(),
                'tanggal_bayar' => $request->input('settlement_time') ?? $request->input('transaction_time'),
            ]);

            if ($tagihanStatus === 'lunas') {
                event(new \App\Events\PembayaranBerhasil($pembayaran));
            }

            return ['status' => 200, 'message' => 'Callback handled successfully'];
        });

        return response()->json(['message' => $result['message']], $result['status']);
    }

    /**
     * Helper privat untuk mengekstrak informasi Bank dan VA Number dari Request Midtrans.
     */
    private function parsePaymentDetails(Request $request): array
    {
        if ($request->has('va_numbers') && is_array($request->input('va_numbers')) && !empty($request->input('va_numbers'))) {
            $vaNumbers = $request->input('va_numbers');
            return [
                $vaNumbers[0]['bank'] ?? null,
                $vaNumbers[0]['va_number'] ?? null,
            ];
        }

        if ($request->has('permata_va_number')) {
            return ['permata', $request->input('permata_va_number')];
        }

        if ($request->input('payment_type') === 'echannel') {
            return ['mandiri', $request->input('bill_key')];
        }

        if ($request->input('payment_type') === 'cstore') {
            return [$request->input('store') ?? 'cstore', $request->input('payment_code')];
        }

        return [$request->input('payment_type'), null];
    }
}
