<?php

namespace App\Jobs\Traits;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Trait QueueConfiguration
 *
 * Menggabungkan trait dasar Laravel Queue serta menyediakan konfigurasi terpusat
 * untuk jumlah retry, timeout, backoff strategy, dan penanganan model terhapus.
 */
trait QueueConfiguration
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Hapus job secara otomatis jika model Eloquent sudah tidak ditemukan.
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Mendapatkan jumlah percobaan maksimal untuk job ini.
     * Mengambil nilai dari config `queue.jobs.tries` dengan fallback default = 3.
     */
    public function tries(): int
    {
        return (int) (config('queue.jobs.tries') ?? 3);
    }

    /**
     * Mendapatkan batas waktu eksekusi job dalam detik.
     * Mengambil nilai dari config `queue.jobs.timeout` dengan fallback default = 60 detik.
     */
    public function timeout(): int
    {
        return (int) (config('queue.jobs.timeout') ?? 60);
    }

    /**
     * Mendapatkan jeda waktu sebelum mencoba kembali job yang gagal.
     * Mengembalikan 0 saat unit test berjalan agar test tidak tertahan (sleep),
     * atau array Exponential Backoff [30s, 60s, 120s] pada environment production/staging.
     *
     * @return array<int>|int
     */
    public function backoff(): array|int
    {
        if (app()->runningUnitTests() || app()->environment('testing')) {
            return 0;
        }

        return config('queue.jobs.backoff') ?? [30, 60, 120];
    }
}

