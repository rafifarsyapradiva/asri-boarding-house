<?php

namespace App\Listeners;

use App\Events\DendaDikenakan;
use App\Jobs\KirimReminderJatuhTempoJob;
use App\Listeners\Traits\StaggersJobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleDendaDikenakan implements ShouldQueue
{
    use InteractsWithQueue, StaggersJobs;

    public bool $afterCommit = true;

    /**
     * Handle the event.
     */
    public function handle(DendaDikenakan $event): void
    {
        if (! $event->tagihan) {
            return;
        }

        KirimReminderJatuhTempoJob::dispatch($event->tagihan)
            ->delay(now()->addSeconds($this->getStaggerDelay()));
    }
}
