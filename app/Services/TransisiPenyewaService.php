<?php

namespace App\Services;

use App\Models\Reservasi;
use App\Models\Penyewa;
use App\Events\ReservasiDikonfirmasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class TransisiPenyewaService
{
    /**
     * Create a new service instance.
     */
    public function __construct(protected BillingService $billingService)
    {
    }

    /**
     * Orkestrasikan pemindahan data calon penyewa menjadi penyewa aktif kost.
     */
    public function transisi(Reservasi $reservasi, int $adminId, array $data = []): Penyewa
    {
        // Pengecekan validitas metode pembayaran dan status
        if ($reservasi->status === 'batal') {
            throw new \Exception('Reservasi telah dibatalkan. Tidak dapat dikonfirmasi.');
        }

        if ($reservasi->metode_pembayaran === 'midtrans' && $reservasi->status === 'pending') {
            throw new \Exception('Reservasi belum dibayar. Tidak dapat dikonfirmasi.');
        }

        // IDEMPOTENCY CHECK
        if ($reservasi->status === 'dikonfirmasi' && $reservasi->penyewa) {
            return $reservasi->penyewa;
        }

        $penyewaBaru = DB::transaction(function () use ($reservasi, $adminId, $data) {
            // Cari record penyewa lama milik user yang sama (termasuk yang soft-deleted)
            // Ini terjadi saat penyewa yang sudah checkout ingin reservasi ulang.
            $penyewa = Penyewa::withTrashed()
                ->where('user_id', $reservasi->user_id)
                ->latest()
                ->first();

            $hargaSewa = ($reservasi->tipe_sewa === 'bulanan' && $reservasi->durasi > 0)
                ? ($reservasi->total_harga / $reservasi->durasi)
                : ($reservasi->kamar ? $reservasi->kamar->harga_bulan : 0);

            $dataUpdate = [
                'kamar_id'        => $reservasi->kamar_id,
                'harga_sewa'      => $hargaSewa,
                'nik'             => $data['nik'] ?? '',
                'tanggal_masuk'   => $reservasi->tanggal_mulai ? $reservasi->tanggal_mulai->toDateString() : date('Y-m-d'),
                'tanggal_keluar'  => null,
                'nama_wali'       => $data['nama_wali'] ?? '',
                'no_wali'         => $data['no_wali'] ?? '',
                'deposit'         => $data['deposit'] ?? 0,
                'status'          => 'aktif',
                'tanggal_billing' => 1,
                'tipe_sewa'       => $reservasi->tipe_sewa,
                'durasi'          => $reservasi->durasi,
            ];

            if ($penyewa) {
                // Langkah 1A: Penyewa lama ditemukan — restore dan update datanya
                // PenyewaObserver::updated() otomatis mengubah status kamar ke 'terisi'
                $penyewa->restore(); // pulihkan jika soft-deleted
                // Bersihkan NIK lama yang mungkin punya suffix '_deleted_...'
                if (str_contains($penyewa->nik ?? '', '_deleted_')) {
                    $dataUpdate['nik'] = $data['nik'] ?? '';
                }
                $penyewa->update($dataUpdate);
            } else {
                // Langkah 1B: Penyewa baru pertama kali — buat record baru
                // PenyewaObserver::created() otomatis mengubah status kamar ke 'terisi'
                $penyewa = Penyewa::create(array_merge($dataUpdate, ['user_id' => $reservasi->user_id]));
            }


            // Langkah 2 (Routing Billing)
            if ($reservasi->is_dp && $reservasi->nominal_sisa > 0) {
                $this->billingService->injectSisaDp($penyewa, $reservasi->nominal_sisa);
            } elseif ($reservasi->status === 'lunas') {
                $this->billingService->injectLunasPenuh($penyewa, $reservasi, $adminId);
            }

            // Langkah 3 (Update Induk Reservasi)
            $reservasi->update([
                'status' => 'dikonfirmasi',
                'penyewa_id' => $penyewa->id,
                'dikonfirmasi_oleh' => $adminId,
                'tanggal_konfirmasi' => Carbon::now(),
            ]);

            return $penyewa;
        });

        Log::info('Proses konversi data reservasi menjadi penyewa aktif berhasil diselesaikan.', [
            'reservasi_id' => $reservasi->id,
            'penyewa_id' => $penyewaBaru->id,
            'admin_id' => $adminId
        ]);

        // Langkah 4 (Pemicu Event Bus)
        event(new ReservasiDikonfirmasi($reservasi, $penyewaBaru));

        return $penyewaBaru;
    }
}
