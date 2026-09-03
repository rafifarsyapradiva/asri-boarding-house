<?php

namespace App\Listeners;

use App\Events\PembayaranBerhasil;
use App\Events\PembayaranCashDikonfirmasi;
use App\Services\PdfNotaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeneratePdfNotaListener implements ShouldQueue
{
    use InteractsWithQueue;

    public ?bool $afterCommit = true;

    /**
     * Jumlah percobaan ulang jika job gagal.
     */
    public int $tries = 3;

    /**
     * Create the event listener.
     */
    public function __construct(protected PdfNotaService $pdfNotaService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(PembayaranBerhasil|PembayaranCashDikonfirmasi $event): void
    {
        $pembayaran = $event->pembayaran ?? null;
        $pembayaranId = $pembayaran?->id ?? 'UNKNOWN';

        if (! $pembayaran) {
            Log::warning("GeneratePdfNotaListener: Payload pembayaran kosong untuk event.", [
                'event_type' => get_class($event),
            ]);
            return;
        }

        try {
            $this->pdfNotaService->generate($pembayaran);
        } catch (Throwable $e) {
            Log::error("Gagal membuat PDF Nota untuk Pembayaran ID #{$pembayaranId}: " . $e->getMessage(), [
                'exception' => $e,
                'pembayaran_id' => $pembayaranId,
            ]);

            throw $e;
        }
    }
}

