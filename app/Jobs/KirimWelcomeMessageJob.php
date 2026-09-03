<?php

namespace App\Jobs;

use App\Models\Penyewa;
use App\Services\NotifikasiService;
use App\Jobs\Traits\QueueConfiguration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class KirimWelcomeMessageJob implements ShouldQueue
{
    use QueueConfiguration;

    /**
     * Create a new job instance.
     */
    public function __construct(public Penyewa $penyewa)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(NotifikasiService $notifikasiService): void
    {
        $notifikasiService->kirimNotifikasiWelcomePenyewa($this->penyewa, true);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('KirimWelcomeMessageJob exhausted all attempts.', [
            'penyewa_id' => $this->penyewa?->getKey(),
            'error' => $exception->getMessage(),
        ]);
    }
}
