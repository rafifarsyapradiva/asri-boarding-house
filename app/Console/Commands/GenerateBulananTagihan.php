<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BillingService;
use Illuminate\Support\Facades\Log;

class GenerateBulananTagihan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:generate-bulanan {--force : Jalankan pembuatan tagihan meskipun bukan tanggal 1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate otomatis tagihan bulanan penyewa aktif setiap tanggal 1';

    /**
     * Execute the console command.
     */
    public function handle(BillingService $billingService): int
    {
        try {
            $isFirstDayOfMonth = now()->day === 1;

            if (!$isFirstDayOfMonth && !$this->option('force')) {
                $msg = 'Batal generate tagihan: Pembuatan tagihan bulanan hanya dapat berjalan pada tanggal 1. Gunakan opsi --force untuk memaksa jalan.';
                Log::warning($msg);
                $this->warn($msg);
                return Command::FAILURE;
            }

            Log::info('Proses pembuatan tagihan bulanan dimulai.');
            $this->info('Memulai pembuatan tagihan bulanan...');

            $billingService->generateTagihanBulanan();

            Log::info('Pembuatan tagihan bulanan selesai.');
            $this->info('Pembuatan tagihan bulanan berhasil diselesaikan.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Gagal generate otomatis tagihan bulanan: ' . $e->getMessage());
            $this->error('Gagal generate otomatis tagihan bulanan: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
