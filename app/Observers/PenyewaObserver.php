<?php

namespace App\Observers;

use App\Models\Penyewa;
use App\Models\Setting;
use App\Models\NotifikasiKhusus;
use Illuminate\Support\Carbon;

class PenyewaObserver
{
    // Clean Code: Ekstrak magic number ke dalam konstanta konfigurasi sewa default
    public const DEFAULT_LEASE_DURATION_MONTHS = 12;

    public const LOG_SOURCE = 'admin';
    public const EVENT_CHECKOUT = 'penyewa_checkout';

    /**
     * Handle the Penyewa "creating" event.
     */
    public function creating(Penyewa $penyewa): void
    {
        if (empty($penyewa->tanggal_keluar_seharusnya) && $penyewa->tanggal_masuk) {
            $durasiDefault = (int) Setting::get('durasi_sewa_default', self::DEFAULT_LEASE_DURATION_MONTHS);
            $penyewa->tanggal_keluar_seharusnya = Carbon::parse($penyewa->tanggal_masuk)
                ->addMonths($durasiDefault)
                ->toDateString();
        }
    }

    /**
     * Handle the Penyewa "created" event.
     */
    public function created(Penyewa $penyewa): void
    {
        // Clean Logic: Gunakan loadMissing untuk mencegah N+1 query
        $penyewa->loadMissing('kamar');
        $penyewa->kamar?->update([
            'status' => 'terisi',
        ]);
    }

    /**
     * Handle the Penyewa "updating" event.
     */
    public function updating(Penyewa $penyewa): void
    {
        if ($penyewa->isDirty('status') && $penyewa->status === 'nonaktif') {
            $penyewa->tanggal_keluar = Carbon::today()->toDateString();
        }
    }

    /**
     * Handle the Penyewa "updated" event.
     */
    public function updated(Penyewa $penyewa): void
    {
        // Fix Critical Bug: Gunakan wasChanged() pada event updated() bukan isDirty()
        if ($penyewa->wasChanged('status') && $penyewa->status === 'nonaktif') {
            // Clean Logic: Eager load relasi yang dibutuhkan untuk log deskripsi
            $penyewa->loadMissing(['kamar', 'user']);

            // Clean Logic: Gunakan Nullsafe operator untuk menghindari Null Pointer Exception
            $nomorKamar = $penyewa->kamar?->nomor_kamar ?? '-';
            $namaUser = $penyewa->user?->nama ?? '-';

            $deskripsi = sprintf(
                "Penyewa Kamar %s (%s) telah di-checkout dari sistem oleh Admin",
                $nomorKamar,
                $namaUser
            );

            NotifikasiKhusus::log(
                self::LOG_SOURCE,
                self::EVENT_CHECKOUT,
                $deskripsi,
                [
                    'penyewa_id' => $penyewa->id,
                    'user_id' => $penyewa->user_id,
                    'kamar_id' => $penyewa->kamar_id,
                ]
            );
        }
    }
}
