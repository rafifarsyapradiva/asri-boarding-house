<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Reservasi;
use App\Models\Tagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AbhPurgeTrashTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_purges_trash_older_than_90_days(): void
    {
        // 1. Setup Rooms (Kamar)
        $kamarOld = Kamar::create([
            'nomor_kamar' => '101A',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);
        $kamarOld->delete(); // Soft delete now
        // Force the deleted_at timestamp to 95 days ago
        DB::table('kamar')->where('id', $kamarOld->id)->update(['deleted_at' => Carbon::now()->subDays(95)]);

        $kamarNew = Kamar::create([
            'nomor_kamar' => '102A',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);
        $kamarNew->delete(); // Soft delete now
        DB::table('kamar')->where('id', $kamarNew->id)->update(['deleted_at' => Carbon::now()->subDays(5)]);

        // 2. Setup Users
        $userOld = User::create([
            'nama' => 'User Old',
            'email' => 'user.old@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567800',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
        $userOld->delete();
        DB::table('users')->where('id', $userOld->id)->update(['deleted_at' => Carbon::now()->subDays(95)]);

        // 3. Setup Reservasi
        $kamarRes = Kamar::create([
            'nomor_kamar' => '103A',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);
        $userRes = User::create([
            'nama' => 'User Reservasi',
            'email' => 'user.res@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567801',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
        $reservasiOld = Reservasi::create([
            'user_id' => $userRes->id,
            'kamar_id' => $kamarRes->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => Carbon::now()->toDateString(),
            'tanggal_selesai' => Carbon::now()->addMonth()->toDateString(),
            'durasi' => 1,
            'total_harga' => 1000000,
            'status' => 'batal',
        ]);
        $reservasiOld->delete();
        DB::table('reservasi')->where('id', $reservasiOld->id)->update(['deleted_at' => Carbon::now()->subDays(95)]);

        // Verify initial counts in trash
        $this->assertEquals(1, Kamar::onlyTrashed()->where('deleted_at', '<', Carbon::now()->subDays(90))->count());
        $this->assertEquals(1, User::onlyTrashed()->where('deleted_at', '<', Carbon::now()->subDays(90))->count());
        $this->assertEquals(1, Reservasi::onlyTrashed()->where('deleted_at', '<', Carbon::now()->subDays(90))->count());

        // 4. Run Artisan Command
        $this->artisan('abh:purge-trash')
            ->expectsOutput('Memulai pembersihan data sampah yang berusia lebih dari 90 hari...')
            ->assertExitCode(0);

        // 5. Verify old trash is permanently gone, but new trash is preserved
        $this->assertDatabaseMissing('kamar', ['id' => $kamarOld->id]);
        $this->assertDatabaseHas('kamar', ['id' => $kamarNew->id]);
        $this->assertDatabaseMissing('users', ['id' => $userOld->id]);
        $this->assertDatabaseMissing('reservasi', ['id' => $reservasiOld->id]);
    }

    public function test_it_skips_tenants_with_billing_records(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '104A',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);
        $user = User::create([
            'nama' => 'User Tenant',
            'email' => 'user.tenant@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567802',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
        $penyewa = Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123457',
            'tanggal_masuk' => Carbon::now()->toDateString(),
            'nama_wali' => 'Wali',
            'no_wali' => '081122334455',
            'deposit' => 200000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
        ]);

        // Create billing record
        Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'BILL-TEST-001',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => Carbon::now()->toDateString(),
            'tanggal_jatuh_tempo' => Carbon::now()->addDays(7)->toDateString(),
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        $penyewa->delete();
        DB::table('penyewa')->where('id', $penyewa->id)->update(['deleted_at' => Carbon::now()->subDays(95)]);

        // Run Purge
        $this->artisan('abh:purge-trash')->assertExitCode(0);

        // Renter must still exist (soft-deleted but not force-deleted) because they have billing records
        $this->assertDatabaseHas('penyewa', ['id' => $penyewa->id]);
    }

    public function test_abh_purge_trash_scheduler_is_registered(): void
    {
        $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
        $events = collect($schedule->events());

        $purgeTrashEvent = $events->first(fn($event) => str_contains($event->command, 'abh:purge-trash'));
        $this->assertNotNull($purgeTrashEvent, 'Scheduler abh:purge-trash belum terdaftar.');
        
        // monthly() expression in cron is '0 0 1 * *'
        $this->assertEquals('0 0 1 * *', $purgeTrashEvent->expression);
        $this->assertTrue($purgeTrashEvent->runInBackground);
        $this->assertTrue($purgeTrashEvent->withoutOverlapping);
    }
}
