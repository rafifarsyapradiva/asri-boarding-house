<?php

namespace App\Listeners;

use App\Events\ReminderPenyewa;
use App\Jobs\KirimReminderJatuhTempoJob;
use App\Listeners\Traits\StaggersJobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleReminderPenyewa implements ShouldQueue
{
    use InteractsWithQueue, StaggersJobs;

    public bool $afterCommit = true;

    /**
     * Handle the event.
     */
    public function handle(ReminderPenyewa $event): void
    {
        if (! $event->tagihan) {
            return;
        }

        KirimReminderJatuhTempoJob::dispatch($event->tagihan)
            ->delay(now()->addSeconds($this->getStaggerDelay()));
    }
}
