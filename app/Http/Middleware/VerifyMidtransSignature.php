<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyMidtransSignature
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $serverKey = config('midtrans.server_key');

        // Validasi prasyarat: Pastikan konfigurasi server key sudah terisi
        if (empty($serverKey)) {
            Log::error('VerifyMidtransSignature: Midtrans server key is not configured.');
            return response()->json(['message' => 'Internal Server Error'], 500);
        }

        // Validasi prasyarat payload
        if (!$request->has(['order_id', 'status_code', 'gross_amount', 'signature_key'])) {
            Log::warning('VerifyMidtransSignature: Missing required signature payload fields.', [
                'ip' => $request->ip(),
            ]);
            return response()->json(['message' => 'Bad Request'], 400);
        }

        $signatureKey = (string) $request->signature_key;

        // Verifikasi keabsahan signature
        if (!$this->isValidSignature($request, $serverKey, $signatureKey)) {
            Log::warning('VerifyMidtransSignature: Signature mismatch detected', [
                'order_id' => $request->order_id,
                'ip'       => $request->ip(),
            ]);
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }

    /**
     * Memeriksa keabsahan signature Midtrans (mencegah timing attack via hash_equals).
     */
    protected function isValidSignature(Request $request, string $serverKey, string $signatureKey): bool
    {
        $orderId     = $request->order_id;
        $statusCode  = $request->status_code;
        $grossAmount = $request->gross_amount;

        // Format 1: Format desimal 2 angka di belakang koma (standar Midtrans notification)
        $formattedAmount = is_numeric($grossAmount)
            ? number_format((float) $grossAmount, 2, '.', '')
            : $grossAmount;
        $expected1 = hash('sha512', $orderId . $statusCode . $formattedAmount . $serverKey);

        // Format 2: Raw amount (fallback format tanpa pemformatan desimal)
        $expected2 = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected1, $signatureKey) || hash_equals($expected2, $signatureKey);
    }
}
