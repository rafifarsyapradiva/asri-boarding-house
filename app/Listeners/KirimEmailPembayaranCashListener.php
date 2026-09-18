<?php

namespace App\Listeners;

use App\Events\PembayaranCashDikonfirmasi;
use App\Jobs\KirimNotifikasiPembayaranJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class KirimEmailPembayaranCashListener implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;

    /**
     * Handle the event: kirim WhatsApp + Email ke penyewa ketika
     * admin mengonfirmasi pembayaran cash secara manual.
     */
    public function handle(PembayaranCashDikonfirmasi $event): void
    {
        if (! $event->pembayaran) {
            return;
        }

        KirimNotifikasiPembayaranJob::dispatch($event->pembayaran);
    }
}
