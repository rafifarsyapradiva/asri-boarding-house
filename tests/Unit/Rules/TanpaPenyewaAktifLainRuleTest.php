<?php

namespace Tests\Unit\Rules;

use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\User;
use App\Rules\TanpaPenyewaAktifLainRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TanpaPenyewaAktifLainRuleTest extends TestCase
{
    use RefreshDatabase;

    private function createKamar(): Kamar
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

    private function createUser(): User
    {
        return User::create([
            'nama' => 'Test User',
            'email' => 'user' . rand(1000, 9999) . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
        ]);
    }

    /**
     * Test pass when status is not 'aktif'.
     */
    public function test_passes_when_status_is_not_aktif(): void
    {
        $rule = new TanpaPenyewaAktifLainRule(1);
        $calledFail = false;

        $rule->validate('status', 'nonaktif', function () use (&$calledFail) {
            $calledFail = true;
        });

        $this->assertFalse($calledFail, 'Non-aktif status should pass without checking.');
    }

    /**
     * Test pass when kamarId is null or empty.
     */
    public function test_passes_when_kamar_id_is_empty(): void
    {
        $rule = new TanpaPenyewaAktifLainRule(null);
        $calledFail = false;

        $rule->validate('status', TanpaPenyewaAktifLainRule::STATUS_AKTIF, function () use (&$calledFail) {
            $calledFail = true;
        });

        $this->assertFalse($calledFail, 'Empty kamarId should pass validation early.');
    }

    /**
     * Test pass when status is 'aktif' and no other active tenant exists in room.
     */
    public function test_passes_when_no_other_active_tenant_exists(): void
    {
        $kamar = $this->createKamar();

        $rule = new TanpaPenyewaAktifLainRule($kamar->id);
        $calledFail = false;

        $rule->validate('status', TanpaPenyewaAktifLainRule::STATUS_AKTIF, function () use (&$calledFail) {
            $calledFail = true;
        });

        $this->assertFalse($calledFail, 'Validation should pass if room has no active tenants.');
    }

    /**
     * Test fail when another active tenant exists in room.
     */
    public function test_fails_when_another_active_tenant_exists(): void
    {
        $kamar = $this->createKamar();
        $user = $this->createUser();

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'harga_sewa' => 1000000,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'status' => TanpaPenyewaAktifLainRule::STATUS_AKTIF,
            'no_wali' => '08123456789',
            'nama_wali' => 'Wali Test',
        ]);

        $rule = new TanpaPenyewaAktifLainRule($kamar->id);
        $errorMessage = null;

        $rule->validate('status', TanpaPenyewaAktifLainRule::STATUS_AKTIF, function ($message) use (&$errorMessage) {
            $errorMessage = $message;
        });

        $this->assertEquals('Kamar yang dipilih sudah diisi oleh penyewa aktif lain.', $errorMessage);
    }

    /**
     * Test pass when updating current tenant (excluding current tenant ID).
     */
    public function test_passes_when_updating_same_active_tenant(): void
    {
        $kamar = $this->createKamar();
        $user = $this->createUser();

        $penyewa = Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'harga_sewa' => 1000000,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'status' => TanpaPenyewaAktifLainRule::STATUS_AKTIF,
            'no_wali' => '08123456789',
            'nama_wali' => 'Wali Test',
        ]);

        $rule = new TanpaPenyewaAktifLainRule($kamar->id, $penyewa->id);
        $calledFail = false;

        $rule->validate('status', TanpaPenyewaAktifLainRule::STATUS_AKTIF, function () use (&$calledFail) {
            $calledFail = true;
        });

        $this->assertFalse($calledFail, 'Validation should pass when updating current active tenant.');
    }
}
