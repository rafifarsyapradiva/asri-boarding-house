<?php

namespace App\Listeners;

use App\Events\KeluhanDitanggapi;
use App\Services\NotifikasiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class KirimNotifikasiKeluhanDitanggapi implements ShouldQueue
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
    public function handle(KeluhanDitanggapi $event): void
    {
        $this->notifikasiService->kirimNotifikasiKeluhanDitanggapi($event->keluhan);
    }
}
