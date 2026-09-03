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
                return $this->midtransService->createSnapToken($lockedTagihan);
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

                return $this->midtransService->createSnapTokenReservasi($lockedReservasi);
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
