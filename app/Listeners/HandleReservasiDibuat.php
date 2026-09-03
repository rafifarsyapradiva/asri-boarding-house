<?php

namespace App\Listeners;

use App\Events\ReservasiDibuat;
use App\Jobs\KirimNotifikasiAdminReservasiJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleReservasiDibuat implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;

    /**
     * Handle the event.
     */
    public function handle(ReservasiDibuat $event): void
    {
        if (! $event->reservasi) {
            return;
        }

        KirimNotifikasiAdminReservasiJob::dispatch($event->reservasi);
    }
}

