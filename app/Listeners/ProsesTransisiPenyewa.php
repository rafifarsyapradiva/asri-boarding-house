<?php

namespace App\Listeners;

use App\Events\ReservasiDikonfirmasi;
use App\Services\NotifikasiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProsesTransisiPenyewa implements ShouldQueue
{
    use InteractsWithQueue;

    public ?bool $afterCommit = true;

    /**
     * Create the event listener.
     */
    public function __construct(protected NotifikasiService $notifikasiService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(ReservasiDikonfirmasi $event): void
    {
        $penyewa = $event->penyewa ?? $event->reservasi?->penyewa;

        if ($penyewa) {
            $nomorKamar = $event->reservasi?->kamar?->nomor_kamar ?? '-';
            $this->notifikasiService->kirimNotifikasiTransisiPenyewa($penyewa, $nomorKamar);
        }
    }
}

