<?php

namespace App\Jobs;

use App\Models\Penyewa;
use App\Services\NotifikasiService;
use App\Jobs\Traits\QueueConfiguration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class KirimNotifikasiKustomJob implements ShouldQueue
{
    use QueueConfiguration;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Penyewa $penyewa,
        public string $channel,
        public string $pesan,
        public string $subject = 'Pengumuman Asri Boarding House'
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(NotifikasiService $notifikasiService): void
    {
        $notifikasiService->kirimNotifikasiKustomDirect($this->penyewa, $this->channel, $this->pesan, $this->subject);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('KirimNotifikasiKustomJob exhausted all attempts.', [
            'penyewa_id' => $this->penyewa?->getKey(),
            'channel' => $this->channel,
            'error' => $exception->getMessage(),
        ]);
    }
}
