<?php

namespace App\Observers;

use App\Models\Kamar;
use App\Models\NotifikasiKhusus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class KamarObserver
{
    // Clean Code: Gunakan konstanta untuk menghindari magic strings
    public const CACHE_KEY_KAMAR_LANDING = 'kamar_aktif_landing';
    public const CACHE_KEY_FASILITAS_ALL = 'fasilitas_all';

    public const LOG_SOURCE = 'admin';

    public const EVENT_CREATED = 'kamar_dibuat';
    public const EVENT_STATUS_UPDATED = 'kamar_status_diperbarui';
    public const EVENT_UPDATED = 'kamar_diperbarui';
    public const EVENT_DELETED = 'kamar_dihapus';
    public const EVENT_RESTORED = 'kamar_dipulihkan';
    public const EVENT_FORCE_DELETED = 'kamar_dihapus_permanen';

    /**
     * Hapus cache yang berkaitan dengan data kamar.
     */
    private function invalidateCache(): void
    {
        Cache::forget(self::CACHE_KEY_KAMAR_LANDING);
        Cache::forget(self::CACHE_KEY_FASILITAS_ALL);
    }

    public function created(Kamar $kamar): void
    {
        $this->invalidateCache();

        // Clean Code: Gunakan Number helper bawaan Laravel untuk format mata uang Rupiah
        $hargaFormat = Number::currency($kamar->harga_bulan, in: 'IDR', locale: 'id');
        $deskripsi = sprintf(
            "Kamar baru ditambahkan: Kamar %s (Lantai %s, Tipe %s, %s/bulan)",
            $kamar->nomor_kamar,
            $kamar->lantai,
            strtoupper((string) $kamar->tipe),
            $hargaFormat
        );

        NotifikasiKhusus::log(self::LOG_SOURCE, self::EVENT_CREATED, $deskripsi, $kamar->toArray());
    }

    public function updated(Kamar $kamar): void
    {
        $this->invalidateCache();

        // Fix Critical Bug: Gunakan wasChanged() pada event updated() bukan isDirty()
        if ($kamar->wasChanged('status')) {
            $deskripsi = sprintf(
                "Status Kamar %s diubah menjadi %s",
                $kamar->nomor_kamar,
                strtoupper((string) $kamar->status)
            );

            NotifikasiKhusus::log(self::LOG_SOURCE, self::EVENT_STATUS_UPDATED, $deskripsi, [
                'nomor_kamar' => $kamar->nomor_kamar,
                'status_sebelum' => $kamar->getOriginal('status'),
                'status_baru' => $kamar->status,
            ]);
        } else {
            $deskripsi = sprintf("Detail Kamar %s diperbarui oleh Admin", $kamar->nomor_kamar);
            
            NotifikasiKhusus::log(self::LOG_SOURCE, self::EVENT_UPDATED, $deskripsi, $kamar->getChanges());
        }
    }

    public function deleted(Kamar $kamar): void
    {
        $this->invalidateCache();

        $deskripsi = sprintf("Kamar %s telah dihapus dari sistem", $kamar->nomor_kamar);
        NotifikasiKhusus::log(self::LOG_SOURCE, self::EVENT_DELETED, $deskripsi, $kamar->toArray());
    }

    public function restored(Kamar $kamar): void
    {
        $this->invalidateCache();

        $deskripsi = sprintf("Kamar %s telah dipulihkan ke sistem", $kamar->nomor_kamar);
        NotifikasiKhusus::log(self::LOG_SOURCE, self::EVENT_RESTORED, $deskripsi, $kamar->toArray());
    }

    public function forceDeleted(Kamar $kamar): void
    {
        $this->invalidateCache();

        $deskripsi = sprintf("Kamar %s telah dihapus secara permanen dari sistem", $kamar->nomor_kamar);
        NotifikasiKhusus::log(self::LOG_SOURCE, self::EVENT_FORCE_DELETED, $deskripsi, $kamar->toArray());
    }
}
