<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Reservasi;
use App\Models\Kamar;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private function createKamar()
    {
        return Kamar::create([
            'nomor_kamar' => 'K-' . rand(100, 999),
            'lantai' => 1,
            'tipe' => 'deluxe',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);
    }

    public function test_isActiveTenant_returns_true_when_penyewa_is_aktif()
    {
        $user = User::create([
            'nama' => 'Tenant Active',
            'email' => 'active@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PENYEWA,
        ]);

        $kamar = $this->createKamar();

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'status' => 'aktif',
            'no_kamar' => '101',
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'no_wali' => '08123456789',
            'nama_wali' => 'Wali Active',
        ]);

        $this->assertTrue($user->isActiveTenant());
    }

    public function test_isActiveTenant_returns_false_when_penyewa_is_not_aktif_or_null()
    {
        $userWithPendingTenant = User::create([
            'nama' => 'Tenant Pending',
            'email' => 'pending@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PENYEWA,
        ]);

        $kamar = $this->createKamar();

        Penyewa::create([
            'user_id' => $userWithPendingTenant->id,
            'kamar_id' => $kamar->id,
            'status' => 'nonaktif',
            'no_kamar' => '102',
            'nik' => '1234567890123457',
            'tanggal_masuk' => now()->toDateString(),
            'no_wali' => '08123456789',
            'nama_wali' => 'Wali Pending',
        ]);

        $userWithNoTenant = User::create([
            'nama' => 'User No Tenant',
            'email' => 'notenant@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PENYEWA,
        ]);

        $this->assertFalse($userWithPendingTenant->isActiveTenant());
        $this->assertFalse($userWithNoTenant->isActiveTenant());
    }

    public function test_initials_accessor_extracts_correct_initials()
    {
        $userSingle = User::create([
            'nama' => 'Gajahmada',
            'email' => 'gajah@example.com',
            'password' => bcrypt('password'),
        ]);

        $userMultiple = User::create([
            'nama' => 'Muhammad Hatta',
            'email' => 'hatta@example.com',
            'password' => bcrypt('password'),
        ]);

        $userTrimmed = User::create([
            'nama' => '   Ki Hajar  ',
            'email' => 'hajar@example.com',
            'password' => bcrypt('password'),
        ]);

        $userEmpty = User::create([
            'nama' => '',
            'email' => 'empty@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertEquals('G', $userSingle->initials);
        $this->assertEquals('MH', $userMultiple->initials);
        $this->assertEquals('KH', $userTrimmed->initials);
        $this->assertEquals('U', $userEmpty->initials);
    }

    public function test_hasBookingInProgress_detects_pending_reservations()
    {
        $user = User::create([
            'nama' => 'Penyewa Pending',
            'email' => 'pending-res@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PENYEWA,
        ]);

        $kamar = $this->createKamar();

        $this->assertFalse($user->hasBookingInProgress());

        Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'status' => 'pending',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'durasi' => 1,
            'total_harga' => 500000,
        ]);

        $this->assertTrue($user->hasBookingInProgress());
    }

    public function test_dashboard_route_accessor_returns_correct_dashboard()
    {
        $admin = User::create([
            'nama' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        $kamar = $this->createKamar();

        $activeTenantUser = User::create([
            'nama' => 'Active Tenant',
            'email' => 'active-tenant@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PENYEWA,
        ]);
        Penyewa::create([
            'user_id' => $activeTenantUser->id,
            'kamar_id' => $kamar->id,
            'status' => 'aktif',
            'no_kamar' => '106',
            'nik' => '1234567890123458',
            'tanggal_masuk' => now()->toDateString(),
            'no_wali' => '08123456789',
            'nama_wali' => 'Wali Active',
        ]);

        $pendingTenantUser = User::create([
            'nama' => 'Pending Tenant',
            'email' => 'pending-tenant@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PENYEWA,
        ]);
        Penyewa::create([
            'user_id' => $pendingTenantUser->id,
            'kamar_id' => $kamar->id,
            'status' => 'nonaktif', // status isn't 'aktif'
            'no_kamar' => '107',
            'nik' => '1234567890123459',
            'tanggal_masuk' => now()->toDateString(),
            'no_wali' => '08123456789',
            'nama_wali' => 'Wali Pending',
        ]);

        $this->assertEquals('admin.dashboard', $admin->getDashboardRouteName());
        $this->assertEquals('penyewa.dashboard', $activeTenantUser->getDashboardRouteName());
        $this->assertEquals('penyewa.reservasi.dashboard', $pendingTenantUser->getDashboardRouteName());
    }

    public function test_name_attribute_accessor_and_mutator_alias_nama()
    {
        $user = new User();
        $user->name = 'Budi Santoso';

        $this->assertEquals('Budi Santoso', $user->nama);
        $this->assertEquals('Budi Santoso', $user->name);
    }
}
