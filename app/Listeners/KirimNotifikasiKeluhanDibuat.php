<?php

namespace App\Listeners;

use App\Events\KeluhanDibuat;
use App\Services\NotifikasiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class KirimNotifikasiKeluhanDibuat implements ShouldQueue
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
    public function handle(KeluhanDibuat $event): void
    {
        $this->notifikasiService->kirimNotifikasiKeluhanDibuat($event->keluhan);
    }
}
