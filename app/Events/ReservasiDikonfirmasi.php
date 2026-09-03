<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Penyewa;
use App\Models\Reservasi;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika reservasi kamar dikonfirmasi oleh admin.
 */
class ReservasiDikonfirmasi
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instansi event baru.
     *
     * @param Reservasi $reservasi Instansi reservasi yang dikonfirmasi.
     * @param Penyewa|null $penyewa Entitas penyewa terkait (dapat dilewatkan secara manual jika relasi belum dimuat/eager loaded).
     */
    public function __construct(
        public readonly Reservasi $reservasi,
        public readonly ?Penyewa $penyewa = null
    ) {
    }

    /**
     * Mendapatkan instansi Penyewa terkait (menggunakan fallback ke relasi reservasi jika null).
     */
    public function getPenyewa(): ?Penyewa
    {
        return $this->penyewa ?? $this->reservasi->penyewa;
    }
}
