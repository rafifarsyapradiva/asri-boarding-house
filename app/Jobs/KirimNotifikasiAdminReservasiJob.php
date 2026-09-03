<?php

namespace App\Jobs;

use App\Models\Reservasi;
use App\Services\NotifikasiService;
use App\Jobs\Traits\QueueConfiguration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class KirimNotifikasiAdminReservasiJob implements ShouldQueue
{
    use QueueConfiguration;

    /**
     * Create a new job instance.
     */
    public function __construct(public Reservasi $reservasi)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(NotifikasiService $notifikasiService): void
    {
        $notifikasiService->kirimNotifikasiReservasiBaru($this->reservasi);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('KirimNotifikasiAdminReservasiJob exhausted all attempts.', [
            'reservasi_id' => $this->reservasi?->getKey(),
            'error' => $exception->getMessage(),
        ]);
    }
}
