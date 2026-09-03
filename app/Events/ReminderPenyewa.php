<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Tagihan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu saat reminder/pengingat pembayaran tagihan perlu dikirim ke penyewa.
 */
class ReminderPenyewa
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instansi event baru.
     *
     * @param Tagihan $tagihan Instansi tagihan yang diingatkan pembayarannya.
     */
    public function __construct(public readonly Tagihan $tagihan)
    {
    }
}
