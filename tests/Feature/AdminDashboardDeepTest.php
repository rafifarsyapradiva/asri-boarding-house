<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Reservasi;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Pengeluaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AdminDashboardDeepTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tenantUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Admin
        $this->admin = User::create([
            'nama' => 'Admin Asep',
            'email' => 'asri-asep@gmail.com',
            'password' => bcrypt('asriasep48'),
            'no_hp' => '6281234567890',
            'role' => 'admin',
            'require_password_change' => false,
            'is_active' => 1,
        ]);

        // 2. Create Tenant User
        $this->tenantUser = User::create([
            'nama' => 'Tenant Budi',
            'email' => 'tenant-budi@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '6281234567891',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
    }

    /**
     * Test admin dashboard statistics calculations.
     */
    public function test_admin_dashboard_statistics_calculation(): void
    {
        $now = Carbon::now();
        $currentDateStr = $now->toDateString();

        // 1. Create Room mix (terisi, tersedia, maintenance)
        Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'terisi'
        ]);

        Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        Kamar::create([
            'nomor_kamar' => '103',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'maintenance'
        ]);

        // 2. Create Active Tenant (needed for Tagihan)
        $penyewa = Penyewa::create([
            'user_id' => $this->tenantUser->id,
            'kamar_id' => Kamar::where('nomor_kamar', '101')->first()->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => $currentDateStr,
            'status' => 'aktif',
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Budi',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // 3. Create normal Pembayaran (counted in $pembayaranPokok)
        $tagihanPay = Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-PAY-TEST',
            'periode_bulan' => $now->month,
            'periode_tahun' => $now->year,
            'tanggal_tagihan' => $now->copy()->startOfMonth()->toDateString(),
            'tanggal_jatuh_tempo' => $now->copy()->startOfMonth()->addDays(9)->toDateString(),
            'nominal_pokok' => 700000,
            'nominal_total' => 700000,
            'status' => 'lunas',
        ]);

        Pembayaran::create([
            'tagihan_id' => $tagihanPay->id,
            'transaction_id' => 'TRX-PAY-TEST',
            'nominal' => 700000,
            'status_midtrans' => 'settlement',
            'tanggal_bayar' => $now,
        ]);

        // 4. Create DP Reservation (is_dp=true, status=dp) -> expected nominal_dp = 300000
        Reservasi::create([
            'user_id' => $this->tenantUser->id,
            'kamar_id' => Kamar::where('nomor_kamar', '102')->first()->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => $currentDateStr,
            'tanggal_selesai' => $now->copy()->addMonth()->toDateString(),
            'durasi' => 1,
            'total_harga' => 1000000,
            'status' => 'dp',
            'is_dp' => true,
            'nominal_dp' => 300000,
            'nominal_sisa' => 700000,
            'tanggal_konfirmasi' => $now,
        ]);

        // 5. Create DP Reservation (is_dp=true, status=dikonfirmasi) -> expected nominal_dp = 300000
        Reservasi::create([
            'user_id' => $this->tenantUser->id,
            'kamar_id' => Kamar::where('nomor_kamar', '102')->first()->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => $currentDateStr,
            'tanggal_selesai' => $now->copy()->addMonth()->toDateString(),
            'durasi' => 1,
            'total_harga' => 1000000,
            'status' => 'dikonfirmasi',
            'is_dp' => true,
            'nominal_dp' => 300000,
            'nominal_sisa' => 700000,
            'tanggal_konfirmasi' => $now,
        ]);

        // 6. Create Full Reservation (is_dp=false, status=lunas) -> expected total_harga = 1000000
        Reservasi::create([
            'user_id' => $this->tenantUser->id,
            'kamar_id' => Kamar::where('nomor_kamar', '102')->first()->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => $currentDateStr,
            'tanggal_selesai' => $now->copy()->addMonth()->toDateString(),
            'durasi' => 1,
            'total_harga' => 1000000,
            'status' => 'lunas',
            'is_dp' => false,
            'nominal_dp' => 0,
            'nominal_sisa' => 0,
            'tanggal_konfirmasi' => $now,
        ]);

        // 7. Create Pengeluaran -> expected nominal = 200000
        Pengeluaran::create([
            'nama_pengeluaran' => 'Beli Alat Kebersihan',
            'kategori' => 'operasional',
            'nominal' => 200000,
            'keterangan' => 'Beli sapu dan pel',
            'tanggal_pengeluaran' => $currentDateStr,
        ]);

        // Helper to create unique tenants for late bill breakdown
        $createTenantWithTagihan = function($index, $dueDaysOffset, $lateMonths) use ($now, $currentDateStr) {
            $user = User::create([
                'nama' => "Tenant Budi $index",
                'email' => "tenant-budi-$index@gmail.com",
                'password' => bcrypt('password123'),
                'no_hp' => '628123456780' . $index,
                'role' => 'penyewa',
                'is_active' => 1,
            ]);

            $kamar = Kamar::create([
                'nomor_kamar' => 'KMR-' . $index,
                'lantai' => 1,
                'tipe' => 'standar',
                'luas_m2' => 12.0,
                'harga_bulan' => 1000000,
                'status' => 'terisi'
            ]);

            $penyewa = Penyewa::create([
                'user_id' => $user->id,
                'kamar_id' => $kamar->id,
                'nik' => '123456789012345' . $index,
                'tanggal_masuk' => $currentDateStr,
                'status' => 'aktif',
                'no_wali' => '08123456780' . $index,
                'nama_wali' => 'Wali Budi',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
            ]);

            return Tagihan::create([
                'penyewa_id' => $penyewa->id,
                'order_id' => 'TGH-BREAK-' . $index,
                'periode_bulan' => $now->month,
                'periode_tahun' => $now->year,
                'tanggal_tagihan' => $currentDateStr,
                'tanggal_jatuh_tempo' => Carbon::now()->addDays($dueDaysOffset)->toDateString(),
                'nominal_pokok' => 1000000,
                'nominal_total' => 1000000,
                'status' => 'pending',
                'bulan_keterlambatan' => $lateMonths,
            ]);
        };

        // 8. Create Tagihan mix for breakdown
        // a. Pending & Future due date (falls in 'pending')
        $createTenantWithTagihan(1, 1, 0);

        // b. Pending & Past due date with 0 bulan_keterlambatan (falls in 'pending')
        $createTenantWithTagihan(2, -1, 0);

        // c. Pending & Past due date with 1 bulan_keterlambatan (falls in '1_bulan')
        $createTenantWithTagihan(3, -1, 1);

        // d. Pending & Past due date with 2 bulan_keterlambatan (falls in '2_bulan')
        $createTenantWithTagihan(4, -1, 2);

        // e. Pending & Past due date with 3 bulan_keterlambatan (falls in '3_bulan_plus')
        $createTenantWithTagihan(5, -1, 3);

        // Access Dashboard
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Asserting Stats:
        // Room mix: Kamar 101, 102, 103 (total = 3).
        // Plus 5 chambers from helper: KMR-1, KMR-2, KMR-3, KMR-4, KMR-5 (each has status 'terisi').
        // Total Rooms = 3 + 5 = 8 Rooms.
        // Terisi Rooms = 1 (KMR 101) + 5 (KMR-1 to KMR-5) = 6 Terisi.
        // Occupancy Rate: 6 / 8 = 75.0%
        $response->assertSee('8 Unit');
        $response->assertSee('6 Terisi');
        $response->assertSee('75% Hunian');
        $response->assertSee('1 Unit'); // Kamar 102 (tersedia)
        $response->assertSee('1 Maintenance'); // Kamar 103 (maintenance)

        // Total Pemasukan:
        // $pembayaranPokok = 700,000
        // $reservasiDp = 300,000 (status dp) + 300,000 (status dikonfirmasi) = 600,000
        // $reservasiFull = 1,000,000 (status lunas)
        // Total = 700,000 + 600,000 + 1,000,000 = 2,300,000
        $response->assertSee('Rp 2.300.000');

        // Total Pengeluaran: 200,000
        $response->assertSee('Rp 200.000');

        // Keuntungan Bersih: 2,300,000 - 200,000 = 2,100,000
        $response->assertSee('Rp 2.100.000');

        // Tagihan Belum Lunas:
        // 5 bills created (TGH-BREAK-1 to TGH-BREAK-5)
        $response->assertSee('5 Tagihan');

        // Breakdown check:
        // Pending: 2
        // L1: 1
        // L2: 1
        // L3+: 1
        $response->assertSee('Pending:');
        $response->assertSee('L1 (1B):');
        $response->assertSee('L2 (2B):');
        $response->assertSee('L3+ (3B+):');

        // Reservasi Pending Konfirmasi:
        // status dp = 1, status lunas = 1 -> Total = 2 Pending
        $response->assertSee('2 Pending');
        // status dikonfirmasi = 1 -> 1 Dikonfirmasi
        $response->assertSee('1 Dikonfirmasi');
    }
}
