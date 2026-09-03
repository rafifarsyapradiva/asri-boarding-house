<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Reservasi;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika draf/pengajuan reservasi kamar baru berhasil dibuat.
 */
class ReservasiDibuat
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instansi event baru.
     *
     * @param Reservasi $reservasi Instansi reservasi yang baru saja dibuat.
     */
    public function __construct(public readonly Reservasi $reservasi)
    {
    }
}
