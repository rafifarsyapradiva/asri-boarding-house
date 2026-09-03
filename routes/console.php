<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Console Task Scheduling
|--------------------------------------------------------------------------
*/

// Billing & Payment Automation
Schedule::command('tagihan:generate-bulanan')
    ->monthlyOn(1, '00:05')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(fn() => Log::info("Tagihan bulanan berhasil dibuat otomatis."));

Schedule::command('tagihan:proses-keterlambatan')
    ->dailyAt('01:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(fn() => Log::info("Proses keterlambatan berhasil dijalankan."))
    ->onFailure(fn() => Log::error("Proses keterlambatan gagal dijalankan."));

// Contract & Reservation Lifecycle
Schedule::command('kontrak:reminder-habis')
    ->dailyAt('08:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('reservasi:cancel-expired')
    ->hourly()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground();

// Maintenance & System Cleanup Tasks
Schedule::command('log-notifikasi:clear')
    ->dailyAt('02:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('chat-guest:prune')
    ->dailyAt('03:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('session:cleanup')
    ->dailyAt('01:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('session-cleanup');

Schedule::command('log:truncate')
    ->weekly()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('abh:purge-trash')
    ->monthly()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground();

