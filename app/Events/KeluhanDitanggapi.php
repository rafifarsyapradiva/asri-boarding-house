<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Keluhan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika keluhan ditanggapi atau diperbarui oleh admin.
 */
class KeluhanDitanggapi
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instansi event baru.
     *
     * @param Keluhan $keluhan Instansi keluhan yang ditanggapi.
     */
    public function __construct(public readonly Keluhan $keluhan)
    {
    }
}
