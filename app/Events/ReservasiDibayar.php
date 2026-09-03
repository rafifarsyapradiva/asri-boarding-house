<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Reservasi;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika DP/pembayaran reservasi kamar boarding house berhasil dilakukan.
 */
class ReservasiDibayar
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instansi event baru.
     *
     * @param Reservasi $reservasi Instansi reservasi yang telah dibayar.
     */
    public function __construct(public readonly Reservasi $reservasi)
    {
    }
}
