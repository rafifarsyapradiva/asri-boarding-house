<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\Reservasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservasiService
{
    /**
     * Calculate dynamic pricing based on rental type and duration.
     */
    public function hitungHarga(Kamar $kamar, string $tipeSewa, int $durasi): array
    {
        $hargaDasar = $kamar->harga_bulan;
        $dpPercentage = config('reservasi.dp_percentage', 0.30);

        $totalHarga = match ($tipeSewa) {
            'harian'   => ($kamar->harga_harian ?? ($hargaDasar / 30)) * $durasi,
            'mingguan' => ($kamar->harga_mingguan ?? ($hargaDasar / 4)) * $durasi,
            'bulanan'  => $hargaDasar * $durasi,
            default    => $hargaDasar * $durasi,
        };

        $discount = \App\Models\Setting::getDiscountForDuration($tipeSewa, $durasi);
        if ($discount > 0) {
            $totalHarga = $totalHarga * (1 - ($discount / 100));
        }

        $nominalDp = $totalHarga * $dpPercentage;
        $nominalSisa = $totalHarga - $nominalDp;

        return [
            'total_harga' => round($totalHarga, 2),
            'nominal_dp' => round($nominalDp, 2),
            'nominal_sisa' => round($nominalSisa, 2),
        ];
    }

    /**
     * Check if a room is already booked on the specified date range.
     */
    public function cekDoubleBooking(Kamar $kamar, string $tanggalMulai, string $tanggalSelesai): bool
    {
        $query = Reservasi::query()
            ->overlapDengan($kamar->id, $tanggalMulai, $tanggalSelesai)
            ->aktif();

        if (DB::transactionLevel() > 0) {
            $query->lockForUpdate();
        }

        $hasOverlapReservasi = $query->exists();

        $hasOverlapPenyewa = \App\Models\Penyewa::where('kamar_id', $kamar->id)
            ->where('status', 'aktif')
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->where('tanggal_masuk', '<=', $tanggalSelesai)
                  ->where(function ($sub) use ($tanggalMulai) {
                      $sub->where('tanggal_keluar', '>=', $tanggalMulai)
                          ->orWhereNull('tanggal_keluar');
                  });
            })
            ->exists();

        return $hasOverlapReservasi || $hasOverlapPenyewa;
    }

    /**
     * Create a new reservation atomically under a database transaction.
     */
    public function buatReservasi(array $data): Reservasi
    {
        return DB::transaction(function () use ($data) {
            $kamar = Kamar::lockForUpdate()->findOrFail($data['kamar_id']);

            if (!isset($data['tanggal_selesai'])) {
                $tanggalMulai = \Illuminate\Support\Carbon::parse($data['tanggal_mulai']);
                $tanggalSelesai = match ($data['tipe_sewa']) {
                    'harian'   => $tanggalMulai->copy()->addDays((int)$data['durasi']),
                    'mingguan' => $tanggalMulai->copy()->addWeeks((int)$data['durasi']),
                    'bulanan'  => $tanggalMulai->copy()->addMonths((int)$data['durasi']),
                    default    => $tanggalMulai->copy()->addMonths((int)$data['durasi']),
                };
                $data['tanggal_selesai'] = $tanggalSelesai->toDateString();
            }

            if ($this->cekDoubleBooking($kamar, $data['tanggal_mulai'], $data['tanggal_selesai'])) {
                throw ValidationException::withMessages([
                    'kamar_id' => 'Kamar sudah ter-booking pada rentang tanggal tersebut.'
                ]);
            }

            $userId = $data['user_id'];
            $timestamp = time() . '-' . rand(1000, 9999);
            $orderId = "RSV-{$userId}-{$timestamp}";

            $rincianHarga = $this->hitungHarga($kamar, $data['tipe_sewa'], $data['durasi']);

            $reservasi = Reservasi::create(array_merge($data, [
                'order_id' => $orderId,
                'total_harga' => $rincianHarga['total_harga'],
                'nominal_dp' => $rincianHarga['nominal_dp'],
                'nominal_sisa' => $rincianHarga['nominal_sisa'],
                'status' => 'pending',
            ]));

            event(new \App\Events\ReservasiDibuat($reservasi));

            return $reservasi;
        });
    }

    /**
     * Cancel a pending reservation.
     */
    public function batalkanReservasi(Reservasi $reservasi): void
    {
        DB::transaction(function () use ($reservasi) {
            $reservasi->update(['status' => 'batal']);
        });
    }
}
