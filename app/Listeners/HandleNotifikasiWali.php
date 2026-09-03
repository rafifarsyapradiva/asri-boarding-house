<?php

namespace App\Listeners;

use App\Events\NotifikasiWali;
use App\Jobs\KirimNotifikasiWaliJob;
use App\Listeners\Traits\StaggersJobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleNotifikasiWali implements ShouldQueue
{
    use InteractsWithQueue, StaggersJobs;

    public bool $afterCommit = true;

    /**
     * Handle the event.
     */
    public function handle(NotifikasiWali $event): void
    {
        if (! $event->tagihan) {
            return;
        }

        KirimNotifikasiWaliJob::dispatch($event->tagihan)
            ->delay(now()->addSeconds($this->getStaggerDelay()));
    }
}
