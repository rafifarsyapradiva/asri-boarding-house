<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Pembayaran;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika pembayaran tagihan berhasil divalidasi/diselesaikan.
 */
class PembayaranBerhasil
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instansi event baru.
     *
     * @param Pembayaran $pembayaran Instansi pembayaran yang berhasil.
     */
    public function __construct(public readonly Pembayaran $pembayaran)
    {
    }
}
