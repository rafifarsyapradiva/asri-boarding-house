<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MidtransReservasiCallbackController extends Controller
{
    /**
     * Handle the server-to-server webhook callback from Midtrans.
     */
    public function handle(Request $request)
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
            \Illuminate\Support\Facades\Log::warning('Midtrans reservasi callback invalid signature key attempt', [
                'order_id' => $orderId,
                'ip' => $request->ip(),
            ]);
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $transactionStatus = $request->input('transaction_status');
        $paymentType = $request->input('payment_type');
        $transactionId = $request->input('transaction_id');

        // Logika Database Transaction dengan Row Lock
        $result = DB::transaction(function () use ($orderId, $transactionStatus, $paymentType, $transactionId, $grossAmount) {
            $reservasi = Reservasi::where('order_id', $orderId)->lockForUpdate()->first();

            if (!$reservasi) {
                // Fallback: extract reservation ID from order_id format RSV-{id}-{timestamp}
                if (preg_match('/^RSV-(\d+)/', $orderId, $matches)) {
                    $reservasi = Reservasi::where('id', $matches[1])->lockForUpdate()->first();
                }
            }

            if (!$reservasi) {
                return ['status' => 404, 'message' => 'Reservation not found'];
            }

            // IDEMPOTENCY CHECK (Aturan Wajib) di bawah perlindungan Row Lock
            if (in_array($reservasi->status, ['dp', 'lunas', 'dikonfirmasi'])) {
                return ['status' => 200, 'message' => 'Reservation transaction already handled'];
            }

            // VALIDASI STRICT FINANSIAL: Verifikasi gross_amount cocok dengan tagihan reservasi
            $expectedAmount = $reservasi->is_dp ? $reservasi->nominal_dp : $reservasi->total_harga;
            if (abs((float)$grossAmount - (float)$expectedAmount) > 0.01) {
                \Illuminate\Support\Facades\Log::error('Midtrans reservasi callback gross_amount mismatch', [
                    'order_id' => $orderId,
                    'received_gross_amount' => $grossAmount,
                    'expected_amount' => $expectedAmount,
                ]);
                return ['status' => 400, 'message' => 'Gross amount mismatch'];
            }

            // FILTER CALLBACK LAMA: Abaikan callback kegagalan/pembatalan jika user sudah melakukan retry (order_id di database sudah diperbarui ke yang lebih baru)
            if (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                if ($reservasi->order_id !== $orderId) {
                    return ['status' => 200, 'message' => 'Ignored old failure callback'];
                }
            }

            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $statusTarget = $reservasi->is_dp ? 'dp' : 'lunas';
                
                // PROTEKSI DOUBLE BOOKING / OVERLAP:
                // Jika reservasi bernilai 'batal' atau 'pending', pastikan kamar tidak di-booking oleh pihak lain pada rentang tanggal yang sama
                $hasDoubleBooking = Reservasi::isKamarTerbooking(
                    $reservasi->kamar_id,
                    $reservasi->tanggal_mulai->toDateString(),
                    $reservasi->tanggal_selesai->toDateString(),
                    $reservasi->id
                );

                if ($hasDoubleBooking) {
                    \Illuminate\Support\Facades\Log::error('Midtrans reservasi callback: Room double-booking detected on payment settlement', [
                        'reservasi_id' => $reservasi->id,
                        'order_id' => $orderId,
                    ]);
                    $reservasi->update([
                        'status' => 'batal',
                        'transaction_id' => $transactionId,
                        'metode_pembayaran' => 'midtrans',
                        'catatan_admin' => 'Double-booking terdeteksi pada saat konfirmasi pembayaran Midtrans. Diperlukan refund atau pemindahan kamar manual oleh admin.',
                    ]);
                    return ['status' => 200, 'message' => 'Double-booking detected. Reservation marked as canceled for refund.'];
                }

                $reservasi->update([
                    'status' => $statusTarget,
                    'transaction_id' => $transactionId,
                    'metode_pembayaran' => 'midtrans',
                    'tanggal_konfirmasi' => now(),
                ]);

                event(new \App\Events\ReservasiDibayar($reservasi));
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $reservasi->update([
                    'status' => 'batal',
                ]);
            }

            return ['status' => 200, 'message' => 'Callback processed successfully'];
        });

        return response()->json(['message' => $result['message']], $result['status']);
    }
}
