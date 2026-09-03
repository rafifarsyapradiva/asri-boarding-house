<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DatabaseSessionCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pembersihan session database kedaluwarsa jika menggunakan driver database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            if (config('session.driver') === 'database') {
                Log::info('Memulai pembersihan session database...');
                
                $lifetime = config('session.lifetime') * 60;
                $deleted = DB::table(config('session.table', 'sessions'))
                    ->where('last_activity', '<', time() - $lifetime)
                    ->delete();
                
                Log::info("Pembersihan session database selesai. Jumlah terhapus: {$deleted}");
                $this->info("Pembersihan session database selesai. Jumlah terhapus: {$deleted}");
            } else {
                $this->info("Session driver bukan database (" . config('session.driver') . "). Tidak ada tindakan.");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Gagal membersihkan session database: ' . $e->getMessage());
            $this->error('Gagal membersihkan session database: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
