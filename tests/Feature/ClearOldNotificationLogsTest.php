<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClearOldNotificationLogsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_clears_notification_logs_older_than_90_days(): void
    {
        \Illuminate\Support\Facades\Queue::fake();
        // 1. Setup Kamar, User, & Penyewa (karena log_notifikasi memiliki FK penyewa_id)
        $kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $user = User::create([
            'nama' => 'Penyewa Asli',
            'email' => 'penyewa.asli@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => Carbon::now()->toDateString(),
            'nama_wali' => 'Wali Asli',
            'no_wali' => '081122334455',
            'deposit' => 200000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
        ]);

        // 2. Buat data log lama (> 90 hari) dan log baru (< 90 hari)
        DB::table('log_notifikasi')->insert([
            [
                'penyewa_id' => $penyewa->id,
                'channel' => 'whatsapp',
                'event' => 'tagihan_bulanan',
                'status' => 'sukses',
                'pesan' => 'Log lama (95 Hari Lalu)',
                'created_at' => Carbon::now()->subDays(95),
            ],
            [
                'penyewa_id' => $penyewa->id,
                'channel' => 'whatsapp',
                'event' => 'tagihan_bulanan',
                'status' => 'sukses',
                'pesan' => 'Log baru (5 Hari Lalu)',
                'created_at' => Carbon::now()->subDays(5),
            ]
        ]);

        // Pastikan jumlah awal data adalah 2
        $this->assertEquals(2, DB::table('log_notifikasi')->count());

        // 3. Jalankan Artisan Command
        $this->artisan('log-notifikasi:clear')
            ->expectsOutput('Memulai pembersihan log_notifikasi yang lebih tua dari 90 hari...')
            ->expectsOutput('Pembersihan selesai! 1 log notifikasi berhasil dihapus.')
            ->assertExitCode(0);

        // 4. Verifikasi sisa data di database
        // Log lama (95 hari lalu) harus terhapus
        $this->assertDatabaseMissing('log_notifikasi', [
            'pesan' => 'Log lama (95 Hari Lalu)'
        ]);

        // Log baru (5 hari lalu) harus tetap ada
        $this->assertDatabaseHas('log_notifikasi', [
            'pesan' => 'Log baru (5 Hari Lalu)'
        ]);

        // Sisa data harus tinggal 1
        $this->assertEquals(1, DB::table('log_notifikasi')->count());
    }

    public function test_scheduler_commands_are_registered(): void
    {
        $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
        $events = collect($schedule->events());

        // 1. tagihan:generate-bulanan (Bulanan setiap tanggal 1 jam 00:05)
        $generateBulanan = $events->first(fn($event) => str_contains($event->command, 'tagihan:generate-bulanan'));
        $this->assertNotNull($generateBulanan, 'Scheduler tagihan:generate-bulanan belum terdaftar.');
        $this->assertEquals('5 0 1 * *', $generateBulanan->expression);
        $this->assertTrue($generateBulanan->runInBackground);
        $this->assertTrue($generateBulanan->withoutOverlapping);

        // 2. tagihan:proses-keterlambatan (Harian jam 01:00)
        $prosesKeterlambatan = $events->first(fn($event) => str_contains($event->command, 'tagihan:proses-keterlambatan'));
        $this->assertNotNull($prosesKeterlambatan, 'Scheduler tagihan:proses-keterlambatan belum terdaftar.');
        $this->assertEquals('0 1 * * *', $prosesKeterlambatan->expression);
        $this->assertTrue($prosesKeterlambatan->runInBackground);
        $this->assertTrue($prosesKeterlambatan->withoutOverlapping);

        // 3. reservasi:cancel-expired (Setiap jam)
        $cancelExpired = $events->first(fn($event) => str_contains($event->command, 'reservasi:cancel-expired'));
        $this->assertNotNull($cancelExpired, 'Scheduler reservasi:cancel-expired belum terdaftar.');
        $this->assertEquals('0 * * * *', $cancelExpired->expression);
        $this->assertTrue($cancelExpired->runInBackground);
        $this->assertTrue($cancelExpired->withoutOverlapping);

        // 4. log-notifikasi:clear (Harian jam 02:00)
        $logClear = $events->first(fn($event) => str_contains($event->command, 'log-notifikasi:clear'));
        $this->assertNotNull($logClear, 'Scheduler log-notifikasi:clear belum terdaftar.');
        $this->assertEquals('0 2 * * *', $logClear->expression);
        $this->assertTrue($logClear->runInBackground);
        $this->assertTrue($logClear->withoutOverlapping);

        // 5. session-cleanup callback (Harian jam 01:30)
        $sessionCleanup = $events->first(fn($event) => $event->description === 'session-cleanup');
        $this->assertNotNull($sessionCleanup, 'Scheduler session-cleanup belum terdaftar.');
        $this->assertEquals('30 1 * * *', $sessionCleanup->expression);
        $this->assertTrue($sessionCleanup->withoutOverlapping);
    }
}
