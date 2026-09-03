<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Tagihan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika notifikasi tagihan perlu dikirimkan kepada wali/orang tua penyewa.
 */
class NotifikasiWali
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instansi event baru.
     *
     * @param Tagihan $tagihan Instansi tagihan yang dinotifikasikan ke wali.
     */
    public function __construct(public readonly Tagihan $tagihan)
    {
    }
}
