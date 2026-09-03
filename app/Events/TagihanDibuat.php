<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Tagihan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika tagihan bulanan baru berhasil diterbitkan untuk penyewa.
 */
class TagihanDibuat
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instansi event baru.
     *
     * @param Tagihan $tagihan Instansi tagihan yang baru dibuat.
     */
    public function __construct(public readonly Tagihan $tagihan)
    {
    }
}
