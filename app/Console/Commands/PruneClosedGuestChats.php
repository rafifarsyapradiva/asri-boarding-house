<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\GuestChatThread;

class PruneClosedGuestChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat-guest:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pembersihan otomatis data obrolan dari thread guest yang sudah ditutup dan melewati retensi 90 hari';

    private const RETENTION_DAYS = 90;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $threshold = now()->subDays(self::RETENTION_DAYS);

            Log::info('Proses pembersihan thread guest chat dimulai.');
            $this->info('Memulai pembersihan thread guest chat yang ditutup dan lebih tua dari 90 hari...');

            $deletedCount = GuestChatThread::where('status', 'closed')
                ->where('updated_at', '<', $threshold)
                ->delete();

            Log::info("Pembersihan thread guest chat selesai. Jumlah thread terhapus: {$deletedCount}");
            $this->info("Pembersihan selesai! {$deletedCount} thread guest chat berhasil dihapus.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Gagal membersihkan thread guest chat: ' . $e->getMessage());
            $this->error('Gagal membersihkan thread guest chat: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
