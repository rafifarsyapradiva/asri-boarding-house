<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AdminPenyewaService;
use App\Services\BillingService;
use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Jobs\KirimWelcomeMessageJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Exception;

class AdminPenyewaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AdminPenyewaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $billingService = $this->createMock(BillingService::class);
        $this->service = new AdminPenyewaService($billingService);
    }

    public function test_get_penyewa_query_filters_by_search_and_status(): void
    {
        $user1 = User::create(['nama' => 'Budi Santoso', 'email' => 'budi@example.com', 'no_hp' => '0811111111', 'password' => bcrypt('pass'), 'role' => 'penyewa']);
        $user2 = User::create(['nama' => 'Siti Aminah', 'email' => 'siti@example.com', 'no_hp' => '0822222222', 'password' => bcrypt('pass'), 'role' => 'penyewa']);

        $kamar1 = Kamar::create(['nomor_kamar' => '101', 'lantai' => 1, 'tipe' => 'standar', 'luas_m2' => 12.0, 'harga_bulan' => 1000000, 'status' => 'terisi']);
        $kamar2 = Kamar::create(['nomor_kamar' => '102', 'lantai' => 1, 'tipe' => 'standar', 'luas_m2' => 12.0, 'harga_bulan' => 1000000, 'status' => 'tersedia']);

        Penyewa::create(['user_id' => $user1->id, 'kamar_id' => $kamar1->id, 'harga_sewa' => 1000000, 'status' => 'aktif', 'nik' => '11111', 'tanggal_masuk' => now()->toDateString(), 'tanggal_billing' => 1, 'tipe_sewa' => 'bulanan', 'durasi' => 6, 'nama_wali' => 'Wali 1', 'no_wali' => '0811111111']);
        Penyewa::create(['user_id' => $user2->id, 'kamar_id' => $kamar2->id, 'harga_sewa' => 1000000, 'status' => 'nonaktif', 'nik' => '22222', 'tanggal_masuk' => now()->toDateString(), 'tanggal_billing' => 1, 'tipe_sewa' => 'bulanan', 'durasi' => 6, 'nama_wali' => 'Wali 2', 'no_wali' => '0822222222']);

        $resultSearch = $this->service->getPenyewaQuery(['search' => 'Budi'])->get();
        $this->assertCount(1, $resultSearch);
        $this->assertEquals('Budi Santoso', $resultSearch->first()->user->nama);

        $resultStatus = $this->service->getPenyewaQuery(['status' => 'aktif'])->get();
        $this->assertCount(1, $resultStatus);
        $this->assertEquals('aktif', $resultStatus->first()->status);
    }

    public function test_register_penyewa_success(): void
    {
        $admin = User::create(['nama' => 'Admin', 'email' => 'admin@example.com', 'no_hp' => '0800000000', 'password' => bcrypt('pass'), 'role' => 'admin']);
        $kamar = Kamar::create(['nomor_kamar' => '103', 'lantai' => 1, 'tipe' => 'standar', 'luas_m2' => 12.0, 'harga_bulan' => 1200000, 'status' => 'tersedia']);

        $data = [
            'nama'          => 'Penyewa Baru',
            'email'         => 'penyewabaru@example.com',
            'no_hp'         => '081234567890',
            'kamar_id'      => $kamar->id,
            'nik'           => '3512345678900001',
            'tanggal_masuk' => now()->toDateString(),
            'tipe_sewa'     => 'bulanan',
            'durasi'        => 6,
            'nama_wali'     => 'Wali Penyewa',
            'no_wali'       => '081299998888',
        ];

        $penyewa = $this->service->registerPenyewa($data, $admin->id);

        $this->assertInstanceOf(Penyewa::class, $penyewa);
        $this->assertDatabaseHas('users', ['email' => 'penyewabaru@example.com']);
        $this->assertDatabaseHas('penyewa', ['nik' => '3512345678900001']);

        Queue::assertPushed(KirimWelcomeMessageJob::class);
    }

    public function test_register_penyewa_fails_when_room_not_available(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Kamar ini sudah terisi oleh penyewa lain.');

        $admin = User::create(['nama' => 'Admin', 'email' => 'admin2@example.com', 'no_hp' => '0800000001', 'password' => bcrypt('pass'), 'role' => 'admin']);
        $kamar = Kamar::create(['nomor_kamar' => '104', 'lantai' => 1, 'tipe' => 'standar', 'luas_m2' => 12.0, 'harga_bulan' => 1200000, 'status' => 'terisi']);

        $data = [
            'nama'          => 'Penyewa Gagal',
            'email'         => 'gagal@example.com',
            'no_hp'         => '081234567891',
            'kamar_id'      => $kamar->id,
            'nik'           => '3512345678900002',
            'tanggal_masuk' => now()->toDateString(),
            'tipe_sewa'     => 'bulanan',
            'durasi'        => 1,
            'nama_wali'     => 'Wali Penyewa',
            'no_wali'       => '081299998888',
        ];

        $this->service->registerPenyewa($data, $admin->id);
    }
}
