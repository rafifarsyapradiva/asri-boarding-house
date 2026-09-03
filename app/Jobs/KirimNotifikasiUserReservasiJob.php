<?php

namespace App\Jobs;

use App\Models\Reservasi;
use App\Services\NotifikasiService;
use App\Jobs\Traits\QueueConfiguration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class KirimNotifikasiUserReservasiJob implements ShouldQueue
{
    use QueueConfiguration;

    /**
     * Create a new job instance.
     */
    public function __construct(public Reservasi $reservasi, public string $type = 'dibayar')
    {
    }

    /**
     * Execute the job.
     */
    public function handle(NotifikasiService $notifikasiService): void
    {
        $notifikasiService->kirimNotifikasiUserReservasi($this->reservasi, $this->type);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('KirimNotifikasiUserReservasiJob exhausted all attempts.', [
            'reservasi_id' => $this->reservasi?->getKey(),
            'type' => $this->type,
            'error' => $exception->getMessage(),
        ]);
    }
}
