<?php

namespace Tests\Unit\Rules;

use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\User;
use App\Rules\KamarTersediaRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KamarTersediaRuleTest extends TestCase
{
    use RefreshDatabase;

    private function createKamar(string $status = KamarTersediaRule::STATUS_TERSEDIA): Kamar
    {
        return Kamar::create([
            'nomor_kamar' => 'K-' . rand(100, 999),
            'lantai' => 1,
            'tipe' => 'deluxe',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => $status,
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
     * Test pass when room status is 'tersedia'.
     */
    public function test_passes_when_kamar_is_available(): void
    {
        $kamar = $this->createKamar(KamarTersediaRule::STATUS_TERSEDIA);

        $rule = new KamarTersediaRule();
        $calledFail = false;

        $rule->validate('kamar_id', $kamar->id, function () use (&$calledFail) {
            $calledFail = true;
        });

        $this->assertFalse($calledFail, 'Available room should pass validation.');
    }

    /**
     * Test fail when room is not found.
     */
    public function test_fails_when_kamar_not_found(): void
    {
        $rule = new KamarTersediaRule();
        $errorMessage = null;

        $rule->validate('kamar_id', 99999, function ($message) use (&$errorMessage) {
            $errorMessage = $message;
        });

        $this->assertEquals('Kamar tidak ditemukan.', $errorMessage);
    }

    /**
     * Test fail when input value is not numeric.
     */
    public function test_fails_when_value_is_not_numeric(): void
    {
        $rule = new KamarTersediaRule();
        $errorMessage = null;

        $rule->validate('kamar_id', 'invalid-id', function ($message) use (&$errorMessage) {
            $errorMessage = $message;
        });

        $this->assertEquals('ID Kamar harus berupa angka.', $errorMessage);
    }

    /**
     * Test fail when creating new tenant and room is unavailable.
     */
    public function test_fails_for_new_tenant_when_kamar_is_terisi(): void
    {
        $kamar = $this->createKamar('terisi');

        $rule = new KamarTersediaRule();
        $errorMessage = null;

        $rule->validate('kamar_id', $kamar->id, function ($message) use (&$errorMessage) {
            $errorMessage = $message;
        });

        $this->assertEquals('Kamar yang dipilih tidak tersedia.', $errorMessage);
    }

    /**
     * Test reactivating tenant on same room when room is not available.
     */
    public function test_fails_when_reactivating_tenant_in_unavailable_room(): void
    {
        $kamar = $this->createKamar('perbaikan');
        $user = $this->createUser();

        $penyewa = Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'harga_sewa' => 1000000,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'status' => KamarTersediaRule::STATUS_NONAKTIF,
            'no_wali' => '08123456789',
            'nama_wali' => 'Wali Test',
        ]);

        $rule = new KamarTersediaRule($penyewa, KamarTersediaRule::STATUS_AKTIF);
        $errorMessage = null;

        $rule->validate('kamar_id', $kamar->id, function ($message) use (&$errorMessage) {
            $errorMessage = $message;
        });

        $this->assertStringContainsString('Kamar tidak tersedia (status kamar:', $errorMessage);
    }
}
