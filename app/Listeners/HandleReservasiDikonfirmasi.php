<?php

namespace App\Listeners;

use App\Events\ReservasiDikonfirmasi;
use App\Jobs\KirimNotifikasiUserReservasiJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleReservasiDikonfirmasi implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;

    /**
     * Handle the event.
     */
    public function handle(ReservasiDikonfirmasi $event): void
    {
        if (! $event->reservasi) {
            return;
        }

        KirimNotifikasiUserReservasiJob::dispatch($event->reservasi, 'dikonfirmasi');
    }
}

