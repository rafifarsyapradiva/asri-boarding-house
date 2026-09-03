<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Keluhan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika keluhan baru berhasil dibuat oleh penyewa.
 */
class KeluhanDibuat
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instansi event baru.
     *
     * @param Keluhan $keluhan Instansi keluhan yang dibuat.
     */
    public function __construct(public readonly Keluhan $keluhan)
    {
    }
}
