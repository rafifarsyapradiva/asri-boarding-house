<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\Reservasi;

class CancelExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservasi:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan reservasi pending yang melewati batas waktu kadaluarsa';

    private const CHUNK_SIZE = 100;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $expireHours = (int) config('reservasi.expire_hours', 24);
            $threshold = Carbon::now()->subHours($expireHours);

            Log::info('Proses pembatalan reservasi kadaluarsa dimulai.');

            $cancelledCount = 0;

            // CLEAN LOGIC: Ambil data via chunking Eloquent lalu update agar booted event
            // di model Reservasi terpanggil untuk menghapus cache status tanpa menghabiskan memori.
            Reservasi::where('status', 'pending')
                ->where('created_at', '<', $threshold)
                ->chunkById(self::CHUNK_SIZE, function ($expiredReservations) use (&$cancelledCount) {
                    foreach ($expiredReservations as $reservasi) {
                        // Memperbarui status memicu event "updated" dan membersihkan cache
                        $reservasi->update(['status' => 'batal']);
                        $cancelledCount++;
                    }
                });

            if ($cancelledCount > 0) {
                Log::info("Berhasil membatalkan {$cancelledCount} reservasi yang kadaluarsa.");
                $this->info("Berhasil membatalkan {$cancelledCount} reservasi yang kadaluarsa.");
            } else {
                $this->info("Tidak ada reservasi pending yang kadaluarsa.");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Gagal membatalkan reservasi kadaluarsa: ' . $e->getMessage());
            $this->error('Gagal membatalkan reservasi kadaluarsa: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
