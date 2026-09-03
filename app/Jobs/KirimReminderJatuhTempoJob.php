<?php

namespace App\Jobs;

use App\Models\Tagihan;
use App\Services\NotifikasiService;
use App\Jobs\Traits\QueueConfiguration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class KirimReminderJatuhTempoJob implements ShouldQueue
{
    use QueueConfiguration;

    /**
     * Create a new job instance.
     */
    public function __construct(public Tagihan $tagihan)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(NotifikasiService $notifikasiService): void
    {
        $notifikasiService->kirimReminderJatuhTempo($this->tagihan, true);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('KirimReminderJatuhTempoJob exhausted all attempts.', [
            'tagihan_id' => $this->tagihan?->getKey(),
            'error' => $exception->getMessage(),
        ]);
    }
}
