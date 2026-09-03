<?php

namespace App\Jobs;

use App\Models\Pembayaran;
use App\Services\NotifikasiService;
use App\Jobs\Traits\QueueConfiguration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class KirimNotifikasiPembayaranJob implements ShouldQueue
{
    use QueueConfiguration;

    /**
     * Create a new job instance.
     */
    public function __construct(public Pembayaran $pembayaran)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(NotifikasiService $notifikasiService): void
    {
        $notifikasiService->kirimNotifikasiPembayaran($this->pembayaran, true);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('KirimNotifikasiPembayaranJob exhausted all attempts.', [
            'pembayaran_id' => $this->pembayaran?->getKey(),
            'error' => $exception->getMessage(),
        ]);
    }
}
