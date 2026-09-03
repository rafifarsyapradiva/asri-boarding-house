<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\User;
use App\Models\Reservasi;
use App\Models\LogNotifikasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AbhPurgeTrash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abh:purge-trash {--days=90 : Jumlah hari retensi data sampah}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently purge soft-deleted records older than specified retention days for Kamar, Penyewa, User, and Reservasi.';

    // Configuration for chunk size
    private const CHUNK_SIZE = 100;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $days = (int) ($this->option('days') ?? 90);
            $threshold = now()->subDays($days);

            Log::info("Proses pembersihan data sampah (abh:purge-trash) dimulai untuk data > {$days} hari.");
            $this->info("Memulai pembersihan data sampah yang berusia lebih dari {$days} hari...");

            $purgedReservasi = 0;
            $purgedPenyewa = 0;
            $purgedUser = 0;
            $purgedKamar = 0;

            // 1. Purge Reservasi (Chunking to prevent memory bloat)
            Reservasi::onlyTrashed()
                ->where('deleted_at', '<', $threshold)
                ->chunkById(self::CHUNK_SIZE, function ($reservasiList) use (&$purgedReservasi) {
                    DB::transaction(function () use ($reservasiList, &$purgedReservasi) {
                        foreach ($reservasiList as $reservasi) {
                            // Cascade delete related chat messages
                            $reservasi->chatMessages()->delete();
                            $reservasi->forceDelete();
                            $purgedReservasi++;
                        }
                    });
                });

            // 2. Purge Penyewa (Filter billing existence directly in database level)
            Penyewa::onlyTrashed()
                ->where('deleted_at', '<', $threshold)
                ->whereDoesntHave('tagihan')
                ->chunkById(self::CHUNK_SIZE, function ($penyewaList) use (&$purgedPenyewa) {
                    DB::transaction(function () use ($penyewaList, &$purgedPenyewa) {
                        foreach ($penyewaList as $penyewa) {
                            // Cascade delete related log notifications
                            LogNotifikasi::where('penyewa_id', $penyewa->id)->delete();
                            $penyewa->forceDelete();
                            $purgedPenyewa++;
                        }
                    });
                });

            // 3. Purge User (Filter relational constraints in database level)
            User::onlyTrashed()
                ->where('deleted_at', '<', $threshold)
                ->whereDoesntHave('penyewa')
                ->whereDoesntHave('reservasi')
                ->chunkById(self::CHUNK_SIZE, function ($userList) use (&$purgedUser) {
                    DB::transaction(function () use ($userList, &$purgedUser) {
                        foreach ($userList as $user) {
                            $user->forceDelete();
                            $purgedUser++;
                        }
                    });
                });

            // 4. Purge Kamar (Filter relational constraints in database level)
            Kamar::onlyTrashed()
                ->where('deleted_at', '<', $threshold)
                ->whereDoesntHave('penyewa')
                ->whereDoesntHave('reservasi')
                ->chunkById(self::CHUNK_SIZE, function ($kamarList) use (&$purgedKamar) {
                    DB::transaction(function () use ($kamarList, &$purgedKamar) {
                        foreach ($kamarList as $kamar) {
                            $kamar->fasilitas()->detach();
                            $kamar->forceDelete();
                            $purgedKamar++;
                        }
                    });
                });

            $summary = "Pembersihan selesai! Reservasi: {$purgedReservasi}, Penyewa: {$purgedPenyewa}, User: {$purgedUser}, Kamar: {$purgedKamar} berhasil dihapus permanen.";
            Log::info($summary);
            $this->info($summary);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Gagal menjalankan pembersihan data sampah: ' . $e->getMessage());
            $this->error('Gagal menjalankan pembersihan data sampah: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
