<?php

namespace App\Listeners;

use App\Events\ReservasiDibayar;
use App\Jobs\KirimNotifikasiUserReservasiJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleReservasiDibayar implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;

    /**
     * Handle the event.
     */
    public function handle(ReservasiDibayar $event): void
    {
        if (! $event->reservasi) {
            return;
        }

        KirimNotifikasiUserReservasiJob::dispatch($event->reservasi);
    }
}

