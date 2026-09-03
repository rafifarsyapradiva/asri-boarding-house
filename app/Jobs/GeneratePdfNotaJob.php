<?php

namespace App\Jobs;

use App\Models\Pembayaran;
use App\Services\PdfNotaService;
use App\Jobs\Traits\QueueConfiguration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class GeneratePdfNotaJob implements ShouldQueue
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
    public function handle(PdfNotaService $pdfNotaService): void
    {
        $pdfNotaService->generate($this->pembayaran);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GeneratePdfNotaJob exhausted all attempts.', [
            'pembayaran_id' => $this->pembayaran?->getKey(),
            'error' => $exception->getMessage(),
        ]);
    }
}
