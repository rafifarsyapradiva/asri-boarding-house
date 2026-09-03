<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class TruncateLogFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:truncate {filename=laravel.log : Nama file log di dalam storage/logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengosongkan isi berkas log tanpa menghapus file fisiknya';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filename = basename($this->argument('filename'));
        $logsDir = storage_path('logs');
        $logPath = $logsDir . DIRECTORY_SEPARATOR . $filename;

        try {
            if (!File::exists($logPath) || strpos(realpath($logPath) ?: '', realpath($logsDir) ?: '') !== 0) {
                $this->warn("Berkas log '{$filename}' tidak valid atau tidak ditemukan di direktori storage/logs.");
                return Command::FAILURE;
            }

            File::put($logPath, '');
            $msg = "Berkas log '{$filename}' berhasil dikosongkan.";
            Log::info($msg);
            $this->info($msg);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $errorMsg = "Gagal mengosongkan berkas log '{$filename}': " . $e->getMessage();
            Log::error($errorMsg);
            $this->error($errorMsg);

            return Command::FAILURE;
        }
    }
}
