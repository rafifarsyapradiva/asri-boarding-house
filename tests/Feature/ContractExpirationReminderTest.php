<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\User;
use App\Models\LogNotifikasi;
use App\Services\NotifikasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ContractExpirationReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_kontrak_reminder_habis_command_executes_without_errors(): void
    {
        $this->artisan('kontrak:reminder-habis')
            ->assertExitCode(0);
    }

    public function test_proses_reminder_habis_kontrak_sends_notification_for_h14_and_h7(): void
    {
        $user14 = User::factory()->create([
            'nama' => 'Penyewa H14',
            'email' => 'h14@example.com',
            'no_hp' => '081234567891',
            'role' => 'penyewa',
        ]);
        $kamar1 = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 1000000,
            'status' => 'terisi',
        ]);
        $penyewa14 = Penyewa::create([
            'user_id' => $user14->id,
            'kamar_id' => $kamar1->id,
            'nik' => '3201010101010001',
            'harga_sewa' => 1000000,
            'nama_wali' => 'Wali H14',
            'no_wali' => '081299998881',
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 12,
            'tanggal_masuk' => Carbon::today()->subMonths(11)->toDateString(),
            'tanggal_keluar_seharusnya' => Carbon::today()->addDays(14)->toDateString(),
        ]);

        $user7 = User::factory()->create([
            'nama' => 'Penyewa H7',
            'email' => 'h7@example.com',
            'no_hp' => '081234567892',
            'role' => 'penyewa',
        ]);
        $kamar2 = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 1000000,
            'status' => 'terisi',
        ]);
        $penyewa7 = Penyewa::create([
            'user_id' => $user7->id,
            'kamar_id' => $kamar2->id,
            'nik' => '3201010101010002',
            'harga_sewa' => 1000000,
            'nama_wali' => 'Wali H7',
            'no_wali' => '081299998882',
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 12,
            'tanggal_masuk' => Carbon::today()->subMonths(11)->toDateString(),
            'tanggal_keluar_seharusnya' => Carbon::today()->addDays(7)->toDateString(),
        ]);

        $userOther = User::factory()->create([
            'nama' => 'Penyewa H10',
            'email' => 'h10@example.com',
            'no_hp' => '081234567893',
            'role' => 'penyewa',
        ]);
        $kamar3 = Kamar::create([
            'nomor_kamar' => '103',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 1000000,
            'status' => 'terisi',
        ]);
        $penyewaOther = Penyewa::create([
            'user_id' => $userOther->id,
            'kamar_id' => $kamar3->id,
            'nik' => '3201010101010003',
            'harga_sewa' => 1000000,
            'nama_wali' => 'Wali H10',
            'no_wali' => '081299998883',
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 12,
            'tanggal_masuk' => Carbon::today()->subMonths(11)->toDateString(),
            'tanggal_keluar_seharusnya' => Carbon::today()->addDays(10)->toDateString(),
        ]);

        /** @var NotifikasiService $service */
        $service = app(NotifikasiService::class);
        $service->prosesReminderHabisKontrak();

        $this->assertDatabaseHas('log_notifikasi', [
            'penyewa_id' => $penyewa14->id,
            'event' => 'reminder_kontrak_14',
        ]);

        $this->assertDatabaseHas('log_notifikasi', [
            'penyewa_id' => $penyewa7->id,
            'event' => 'reminder_kontrak_7',
        ]);

        $this->assertDatabaseMissing('log_notifikasi', [
            'penyewa_id' => $penyewaOther->id,
        ]);
    }
}
