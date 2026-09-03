<?php

namespace App\Listeners;

use App\Events\TagihanDibuat;
use App\Jobs\KirimNotifikasiTagihanJob;
use App\Listeners\Traits\StaggersJobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleTagihanDibuat implements ShouldQueue
{
    use InteractsWithQueue, StaggersJobs;

    public bool $afterCommit = true;

    /**
     * Handle the event.
     */
    public function handle(TagihanDibuat $event): void
    {
        if (! $event->tagihan) {
            return;
        }

        KirimNotifikasiTagihanJob::dispatch($event->tagihan)
            ->delay(now()->addSeconds($this->getStaggerDelay()));
    }
}
