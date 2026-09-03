<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LogNotifikasi;
use Illuminate\Support\Facades\Log;

class ClearOldNotificationLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log-notifikasi:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus data log_notifikasi yang berusia lebih dari 90 hari';

    private const RETENTION_DAYS = 90;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $threshold = now()->subDays(self::RETENTION_DAYS);

            Log::info('Proses pembersihan log_notifikasi dimulai.');
            $this->info('Memulai pembersihan log_notifikasi yang lebih tua dari 90 hari...');

            $deletedCount = LogNotifikasi::where('created_at', '<', $threshold)->delete();

            Log::info("Pembersihan log_notifikasi selesai. Jumlah baris terhapus: {$deletedCount}");
            $this->info("Pembersihan selesai! {$deletedCount} log notifikasi berhasil dihapus.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Gagal membersihkan log_notifikasi: ' . $e->getMessage());
            $this->error('Gagal membersihkan log_notifikasi: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
