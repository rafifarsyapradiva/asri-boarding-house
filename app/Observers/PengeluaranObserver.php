<?php

namespace App\Observers;

use App\Models\Pengeluaran;
use App\Models\NotifikasiKhusus;
use Illuminate\Support\Number;

class PengeluaranObserver
{
    public const LOG_SOURCE = 'admin';
    public const EVENT_CREATED = 'pengeluaran_dicatat';
    public const EVENT_DELETED = 'pengeluaran_dihapus';

    public function created(Pengeluaran $pengeluaran): void
    {
        // Clean Code: Gunakan Number helper bawaan Laravel untuk format mata uang Rupiah
        $nominalFormat = Number::currency($pengeluaran->nominal, in: 'IDR', locale: 'id');
        $deskripsi = sprintf(
            "Pengeluaran baru dicatat: %s (Kategori: %s, %s)",
            $pengeluaran->nama_pengeluaran,
            strtoupper((string) $pengeluaran->kategori),
            $nominalFormat
        );

        NotifikasiKhusus::log(self::LOG_SOURCE, self::EVENT_CREATED, $deskripsi, $pengeluaran->toArray());
    }

    public function deleted(Pengeluaran $pengeluaran): void
    {
        $deskripsi = sprintf("Catatan pengeluaran ID #%s (%s) dihapus", $pengeluaran->id, $pengeluaran->nama_pengeluaran);
        NotifikasiKhusus::log(self::LOG_SOURCE, self::EVENT_DELETED, $deskripsi, $pengeluaran->toArray());
    }
}
