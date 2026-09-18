<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SnapTokenController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Generate Snap Token untuk pembayaran tagihan penyewa.
     */
    public function generate(Tagihan $tagihan): JsonResponse
    {
        // Otorisasi IDOR menggunakan Eloquent Policy
        Gate::authorize('pay', $tagihan);

        try {
            // Lock dan reload data tagihan terbaru secara atomik untuk menjamin konsistensi payload finansial
            $result = DB::transaction(function () use ($tagihan) {
                $lockedTagihan = Tagihan::where('id', $tagihan->id)->lockForUpdate()->first();
                if (!$lockedTagihan) {
                    throw new \Exception('Tagihan tidak ditemukan.');
                }
                
                // Gunakan cache untuk menyimpan snap_token agar timer 24 jam tidak terus di-reset.
                // Kunci cache mencakup nominal_total agar token otomatis hangus jika ada perubahan denda/nominal.
                $cacheKey = 'snap_token_tagihan_' . $lockedTagihan->id . '_' . $lockedTagihan->nominal_total;
                $cachedToken = \Illuminate\Support\Facades\Cache::get($cacheKey);
                
                if ($cachedToken) {
                    return $cachedToken;
                }
                
                $tokenResult = $this->midtransService->createSnapToken($lockedTagihan);
                
                // Hitung durasi kedaluwarsa token (mengikuti logika di MidtransService)
                $now = \Illuminate\Support\Carbon::now();
                $hoursUntilEndOfMonth = $now->diffInHours($now->copy()->endOfMonth());
                $duration = $hoursUntilEndOfMonth < 24 ? max(1, $hoursUntilEndOfMonth) : 24;
                
                // Simpan di cache sedikit lebih pendek dari durasi asli (kurangi 5 menit) untuk safety margin
                \Illuminate\Support\Facades\Cache::put($cacheKey, $tokenResult, now()->addHours($duration)->subMinutes(5));
                
                return $tokenResult;
            });

            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error('Gagal membuat snap token tagihan: ' . $e->getMessage(), [
                'tagihan_id' => $tagihan->id,
                'exception' => $e
            ]);

            return response()->json([
                'message' => 'Gagal menghubungi gateway pembayaran (Midtrans). Silakan coba lagi beberapa saat lagi.'
            ], 502);
        }
    }

    /**
     * Generate Snap Token untuk pembayaran reservasi penyewa secara dinamis.
     */
    public function generateReservasi(\App\Models\Reservasi $reservasi): JsonResponse
    {
        // Otorisasi IDOR menggunakan Eloquent Policy
        Gate::authorize('pay', $reservasi);

        // Pastikan hanya reservasi berstatus pending yang bisa di-generate tokennya
        if ($reservasi->status !== 'pending') {
            return response()->json(['message' => 'Reservasi ini tidak dalam status pending.'], 400);
        }

        try {
            // Lock dan reload data reservasi terbaru secara atomik
            $result = DB::transaction(function () use ($reservasi) {
                // Lock Kamar terlebih dahulu untuk menjaga lock order (Kamar -> Reservasi) guna mencegah deadlock
                $kamar = \App\Models\Kamar::where('id', $reservasi->kamar_id)->lockForUpdate()->first();
                if (!$kamar) {
                    throw new \Exception('Kamar tidak ditemukan.');
                }

                $lockedReservasi = \App\Models\Reservasi::where('id', $reservasi->id)->lockForUpdate()->first();
                if (!$lockedReservasi) {
                    throw new \Exception('Reservasi tidak ditemukan.');
                }
                if ($lockedReservasi->status !== 'pending') {
                    throw new \Exception('Reservasi ini tidak dalam status pending.');
                }

                // Proteksi Tambahan: Re-evaluasi ketersediaan kamar sebelum memicu Midtrans Snap Token
                $hasOverlap = \App\Models\Reservasi::isKamarTerbooking(
                    $kamar->id,
                    $lockedReservasi->tanggal_mulai->toDateString(),
                    $lockedReservasi->tanggal_selesai->toDateString(),
                    $lockedReservasi->id
                );

                if ($hasOverlap) {
                    throw new \Exception('Kamar sudah ter-booking pada rentang tanggal tersebut.');
                }

                $grossAmount = $lockedReservasi->is_dp ? $lockedReservasi->nominal_dp : $lockedReservasi->total_harga;
                $cacheKey = 'snap_token_reservasi_' . $lockedReservasi->id . '_' . $grossAmount;
                $cachedToken = \Illuminate\Support\Facades\Cache::get($cacheKey);
                
                if ($cachedToken) {
                    return $cachedToken;
                }

                $tokenResult = $this->midtransService->createSnapTokenReservasi($lockedReservasi);
                
                // Simpan di cache sedikit lebih pendek dari durasi asli (kurangi 5 menit) untuk safety margin
                \Illuminate\Support\Facades\Cache::put($cacheKey, $tokenResult, now()->addHours(24)->subMinutes(5));
                
                return $tokenResult;
            });

            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error('Gagal membuat snap token reservasi: ' . $e->getMessage(), [
                'reservasi_id' => $reservasi->id,
                'exception' => $e
            ]);

            return response()->json([
                'message' => 'Gagal menghubungi gateway pembayaran (Midtrans). Silakan coba lagi beberapa saat lagi.'
            ], 502);
        }
    }
}
