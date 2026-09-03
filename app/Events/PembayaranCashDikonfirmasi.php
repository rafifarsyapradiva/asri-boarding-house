<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Pembayaran;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika pembayaran tunai/cash dikonfirmasi manual oleh admin.
 */
class PembayaranCashDikonfirmasi
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instansi event baru.
     *
     * @param Pembayaran $pembayaran Instansi pembayaran cash yang dikonfirmasi.
     */
    public function __construct(public readonly Pembayaran $pembayaran)
    {
    }
}
