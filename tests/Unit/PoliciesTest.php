<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Keluhan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Reservasi;
use App\Models\Penyewa;
use App\Policies\KeluhanPolicy;
use App\Policies\PembayaranPolicy;
use App\Policies\TagihanPolicy;
use App\Policies\ReservasiPolicy;
use Tests\TestCase;

class PoliciesTest extends TestCase
{
    /**
     * Test KeluhanPolicy
     */
    public function test_keluhan_policy(): void
    {
        $policy = new KeluhanPolicy();

        // 1. Admin can view complaints
        $admin = new User(['role' => User::ROLE_ADMIN]);
        $keluhan = new Keluhan();
        
        $this->assertTrue($policy->view($admin, $keluhan));

        // 2. Owner tenant can view their complaints
        $tenantUser = new User([
            'role' => User::ROLE_PENYEWA,
            'id' => 42
        ]);
        $tenantUser->id = 42; // Force ID since mass assignment might guard it

        $penyewa = new Penyewa(['user_id' => 42]);
        $ownerKeluhan = new Keluhan();
        $ownerKeluhan->setRelation('penyewa', $penyewa);

        $this->assertTrue($policy->view($tenantUser, $ownerKeluhan));

        // 3. Other tenant cannot view complaint
        $otherTenantUser = new User([
            'role' => User::ROLE_PENYEWA,
            'id' => 99
        ]);
        $otherTenantUser->id = 99;

        $this->assertFalse($policy->view($otherTenantUser, $ownerKeluhan));

        // 4. Keluhan without penyewa cannot be viewed by tenant
        $noTenantKeluhan = new Keluhan();
        $noTenantKeluhan->setRelation('penyewa', null);

        $this->assertFalse($policy->view($tenantUser, $noTenantKeluhan));
    }

    /**
     * Test PembayaranPolicy
     */
    public function test_pembayaran_policy(): void
    {
        $policy = new PembayaranPolicy();

        // 1. Admin can download invoice
        $admin = new User(['role' => User::ROLE_ADMIN]);
        $pembayaran = new Pembayaran();
        
        $this->assertTrue($policy->downloadNota($admin, $pembayaran));

        // 2. Owner tenant can download invoice
        $tenantUser = new User(['role' => User::ROLE_PENYEWA]);
        $penyewa = new Penyewa();
        $penyewa->id = 10;
        $tenantUser->setRelation('penyewa', $penyewa);

        $tagihan = new Tagihan(['penyewa_id' => 10]);
        $ownerPembayaran = new Pembayaran();
        $ownerPembayaran->setRelation('tagihan', $tagihan);

        $this->assertTrue($policy->downloadNota($tenantUser, $ownerPembayaran));

        // 3. Other tenant cannot download invoice
        $otherTenantUser = new User(['role' => User::ROLE_PENYEWA]);
        $otherPenyewa = new Penyewa();
        $otherPenyewa->id = 99;
        $otherTenantUser->setRelation('penyewa', $otherPenyewa);

        $this->assertFalse($policy->downloadNota($otherTenantUser, $ownerPembayaran));

        // 4. User without penyewa record cannot download invoice
        $nonTenantUser = new User(['role' => User::ROLE_PENYEWA]);
        $nonTenantUser->setRelation('penyewa', null);

        $this->assertFalse($policy->downloadNota($nonTenantUser, $ownerPembayaran));
    }

    /**
     * Test TagihanPolicy
     */
    public function test_tagihan_policy(): void
    {
        $policy = new TagihanPolicy();

        $admin = new User(['role' => User::ROLE_ADMIN]);
        
        $tenantUser = new User([
            'role' => User::ROLE_PENYEWA,
            'id' => 5
        ]);
        $tenantUser->id = 5;

        $penyewa = new Penyewa(['user_id' => 5]);
        $tagihan = new Tagihan();
        $tagihan->setRelation('penyewa', $penyewa);

        // View ability
        $this->assertTrue($policy->view($admin, $tagihan));
        $this->assertTrue($policy->view($tenantUser, $tagihan));

        $otherUser = new User([
            'role' => User::ROLE_PENYEWA,
            'id' => 99
        ]);
        $otherUser->id = 99;
        $this->assertFalse($policy->view($otherUser, $tagihan));

        // Pay ability
        $this->assertTrue($policy->pay($tenantUser, $tagihan));
        $this->assertFalse($policy->pay($otherUser, $tagihan));
        $this->assertFalse($policy->pay($admin, $tagihan)); // Admin cannot pay tenant's bill
    }

    /**
     * Test ReservasiPolicy
     */
    public function test_reservasi_policy(): void
    {
        $policy = new ReservasiPolicy();

        $admin = new User(['role' => User::ROLE_ADMIN]);
        
        $owner = new User([
            'role' => User::ROLE_PENYEWA,
            'id' => 12
        ]);
        $owner->id = 12;

        $other = new User([
            'role' => User::ROLE_PENYEWA,
            'id' => 34
        ]);
        $other->id = 34;

        $reservasi = new Reservasi(['user_id' => 12]);

        // 1. view
        $this->assertTrue($policy->view($admin, $reservasi));
        $this->assertTrue($policy->view($owner, $reservasi));
        $this->assertFalse($policy->view($other, $reservasi));

        // 2. chat
        $this->assertTrue($policy->chat($admin, $reservasi));
        $this->assertTrue($policy->chat($owner, $reservasi));
        $this->assertFalse($policy->chat($other, $reservasi));

        // 3. pay
        $this->assertTrue($policy->pay($owner, $reservasi));
        $this->assertFalse($policy->pay($admin, $reservasi));
        $this->assertFalse($policy->pay($other, $reservasi));

        // 4. update (cancel)
        $this->assertTrue($policy->update($owner, $reservasi));
        $this->assertFalse($policy->update($admin, $reservasi));
        $this->assertFalse($policy->update($other, $reservasi));
    }
}
