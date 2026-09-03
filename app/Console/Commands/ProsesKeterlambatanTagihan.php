<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BillingService;
use Illuminate\Support\Facades\Log;

class ProsesKeterlambatanTagihan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:proses-keterlambatan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Proses harian untuk mengecek keterlambatan tagihan dan mengkalkulasi denda bertahap';

    /**
     * Execute the console command.
     */
    public function handle(BillingService $billingService): int
    {
        try {
            Log::info('Proses pengecekan keterlambatan tagihan harian dimulai.');
            $this->info('Memulai pengecekan keterlambatan tagihan...');

            $billingService->prosesKeterlambatan();

            Log::info('Pengecekan keterlambatan tagihan harian berhasil diselesaikan.');
            $this->info('Pengecekan keterlambatan tagihan berhasil diselesaikan.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Gagal mengecek keterlambatan tagihan harian: ' . $e->getMessage());
            $this->error('Gagal mengecek keterlambatan tagihan harian: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
