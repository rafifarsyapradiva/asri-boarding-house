<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Reservasi;
use App\Models\User;
use App\Models\Kamar;
use PHPUnit\Framework\Attributes\Test;

class ConsoleCommandsAuditTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_prevents_path_traversal_when_truncating_logs(): void
    {
        $invalidFilename = '../../.env';

        $this->artisan('log:truncate', ['filename' => $invalidFilename])
            ->expectsOutputToContain('tidak valid atau tidak ditemukan')
            ->assertExitCode(1);
    }

    #[Test]
    public function it_truncates_valid_log_file_successfully(): void
    {
        $testLogFile = storage_path('logs/test_dummy.log');
        File::put($testLogFile, 'Sample log content to be cleared.');

        $this->assertTrue(File::exists($testLogFile));

        $this->artisan('log:truncate', ['filename' => 'test_dummy.log'])
            ->expectsOutputToContain('berhasil dikosongkan')
            ->assertExitCode(0);

        $this->assertEquals('', File::get($testLogFile));

        if (File::exists($testLogFile)) {
            File::delete($testLogFile);
        }
    }

    #[Test]
    public function it_cancels_expired_reservations_in_chunks(): void
    {
        $user = User::create([
            'nama' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '201B',
            'lantai' => 2,
            'tipe' => 'vip',
            'luas_m2' => 16,
            'harga_bulan' => 1500000,
            'status' => 'tersedia',
        ]);

        $expiredReservasi = Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'kode_reservasi' => 'RES-EXP-001',
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(30),
            'durasi_sewa' => 1,
            'total_harga' => 1500000,
            'status' => 'pending',
        ]);
        // Force created_at to 30 hours ago via DB update
        DB::table('reservasi')->where('id', $expiredReservasi->id)->update(['created_at' => now()->subHours(30)]);

        $activeReservasi = Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'kode_reservasi' => 'RES-ACT-002',
            'tanggal_mulai' => now()->addDays(2),
            'tanggal_selesai' => now()->addDays(31),
            'durasi_sewa' => 1,
            'total_harga' => 1500000,
            'status' => 'pending',
        ]);

        $this->artisan('reservasi:cancel-expired')
            ->assertExitCode(0);

        $this->assertDatabaseHas('reservasi', [
            'id' => $expiredReservasi->id,
            'status' => 'batal',
        ]);

        $this->assertDatabaseHas('reservasi', [
            'id' => $activeReservasi->id,
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function it_purges_trash_with_custom_days_option(): void
    {
        $this->artisan('abh:purge-trash', ['--days' => 30])
            ->expectsOutputToContain('Memulai pembersihan data sampah yang berusia lebih dari 30 hari')
            ->assertExitCode(0);
    }

    #[Test]
    public function it_runs_check_status_command_successfully(): void
    {
        $this->artisan('abh:check-status')
            ->assertExitCode(0);
    }

    #[Test]
    public function it_fails_generate_monthly_billing_if_not_first_day_without_force(): void
    {
        $this->travelTo(now()->setDay(15));

        $this->artisan('tagihan:generate-bulanan')
            ->expectsOutputToContain('hanya dapat berjalan pada tanggal 1')
            ->assertExitCode(1);
    }
}
