<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotifikasiService;
use Illuminate\Support\Facades\Log;

class ReminderHabisKontrakCommand extends Command
{
    /**
     * Nama dan signature dari Artisan Command.
     *
     * @var string
     */
    protected $signature = 'kontrak:reminder-habis';

    /**
     * Deskripsi fungsional dari Artisan Command.
     *
     * @var string
     */
    protected $description = 'Kirim otomatis WhatsApp & Email pengingat masa kontrak sewa berakhir (H-14 dan H-7)';

    /**
     * Eksekusi Command.
     */
    public function handle(NotifikasiService $notifikasiService): int
    {
        try {
            Log::info('Proses pengingat masa kontrak sewa berakhir dimulai.');
            $this->info('Memulai pengecekan masa kontrak...');

            $notifikasiService->prosesReminderHabisKontrak();

            Log::info('Proses pengingat masa kontrak sewa berakhir selesai.');
            $this->info('Pengecekan masa kontrak berhasil diselesaikan.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Gagal mengecek dan mengirim reminder kontrak berakhir: ' . $e->getMessage());
            $this->error('Gagal menjalankan pengingat kontrak berakhir: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
