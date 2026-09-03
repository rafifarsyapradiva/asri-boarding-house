<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Tagihan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika denda keterlambatan berhasil dikenakan pada tagihan.
 */
class DendaDikenakan
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instansi event baru.
     *
     * @param Tagihan $tagihan Instansi tagihan yang dikenakan denda.
     */
    public function __construct(public readonly Tagihan $tagihan)
    {
    }
}
