<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Reservasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TenantProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $tenantUser;
    private User $otherUser;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        // Prevent external WhatsApp notifications
        Http::fake([
            'api.fonnte.com/*' => Http::response([
                'status' => true,
                'message' => 'WhatsApp Mock Success',
            ], 200),
        ]);

        $this->kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        // Create a tenant user (starts with no active penyewa record, just role=penyewa)
        $this->tenantUser = User::create([
            'nama' => 'Penyewa Asli',
            'email' => 'penyewa.asli@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $this->otherUser = User::create([
            'nama' => 'Penyewa Lain',
            'email' => 'penyewa.lain@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567899',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
    }

    /**
     * Create tenant details (making the user an active tenant).
     */
    private function activateTenant(User $user): Penyewa
    {
        return Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => Carbon::now()->toDateString(),
            'nama_wali' => 'Wali Asli',
            'no_wali' => '081122334455',
            'deposit' => 200000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
        ]);
    }

    public function test_active_tenant_can_access_profile_edit_page(): void
    {
        $this->activateTenant($this->tenantUser);

        $response = $this->actingAs($this->tenantUser)
            ->get(route('penyewa.profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Informasi Profil');
        $response->assertSee($this->tenantUser->email);
        $response->assertSee($this->tenantUser->no_hp);
    }

    public function test_inactive_or_pending_tenant_can_access_profile_edit_page(): void
    {
        // No activateTenant call - the user is pending/inactive
        $response = $this->actingAs($this->tenantUser)
            ->get(route('penyewa.profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Informasi Profil');
        $response->assertSee($this->tenantUser->email);
        $response->assertSee($this->tenantUser->no_hp);
    }

    public function test_tenant_profile_information_can_be_updated(): void
    {
        $this->activateTenant($this->tenantUser);

        $response = $this->actingAs($this->tenantUser)
            ->patch(route('penyewa.profile.update'), [
                'name' => 'Nama Baru',
                'email' => 'baru@gmail.com',
                'no_hp' => '089876543210',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('penyewa.profile.edit'));

        $this->tenantUser->refresh();
        $this->assertEquals('Nama Baru', $this->tenantUser->nama);
        $this->assertEquals('baru@gmail.com', $this->tenantUser->email);
        $this->assertEquals('089876543210', $this->tenantUser->no_hp);
    }

    public function test_tenant_profile_update_validation_fails_on_duplicate_email_or_no_hp(): void
    {
        $this->activateTenant($this->tenantUser);

        // Try using otherUser's email
        $response = $this->actingAs($this->tenantUser)
            ->from(route('penyewa.profile.edit'))
            ->patch(route('penyewa.profile.update'), [
                'name' => 'Nama Baru',
                'email' => $this->otherUser->email,
                'no_hp' => '089876543210',
            ]);

        $response->assertRedirect(route('penyewa.profile.edit'));
        $response->assertSessionHasErrors('email');

        // Try using otherUser's no_hp
        $response = $this->actingAs($this->tenantUser)
            ->from(route('penyewa.profile.edit'))
            ->patch(route('penyewa.profile.update'), [
                'name' => 'Nama Baru',
                'email' => 'baru@gmail.com',
                'no_hp' => $this->otherUser->no_hp,
            ]);

        $response->assertRedirect(route('penyewa.profile.edit'));
        $response->assertSessionHasErrors('no_hp');
    }

    public function test_tenant_profile_update_validation_fails_on_invalid_no_hp_format(): void
    {
        $this->activateTenant($this->tenantUser);

        $response = $this->actingAs($this->tenantUser)
            ->from(route('penyewa.profile.edit'))
            ->patch(route('penyewa.profile.update'), [
                'name' => 'Nama Baru',
                'email' => 'baru@gmail.com',
                'no_hp' => '12345678', // Invalid Indonesian WhatsApp regex format
            ]);

        $response->assertRedirect(route('penyewa.profile.edit'));
        $response->assertSessionHasErrors('no_hp');
    }

    public function test_tenant_cannot_delete_account_if_has_active_tenant_record(): void
    {
        $this->activateTenant($this->tenantUser);

        $response = $this->actingAs($this->tenantUser)
            ->from(route('penyewa.profile.edit'))
            ->delete(route('penyewa.profile.destroy'), [
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('penyewa.profile.edit'));
        $response->assertSessionHasErrorsIn('userDeletion', 'password');

        $this->assertNotNull($this->tenantUser->fresh());
    }

    public function test_tenant_cannot_delete_account_if_has_reservations(): void
    {
        // User has no active tenancy but has a reservation record
        Reservasi::create([
            'user_id' => $this->tenantUser->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => Carbon::now()->toDateString(),
            'tanggal_selesai' => Carbon::now()->addMonth()->toDateString(),
            'durasi' => 1,
            'total_harga' => 1000000,
            'status' => 'pending',
            'is_dp' => false,
        ]);

        $response = $this->actingAs($this->tenantUser)
            ->from(route('profile.edit'))
            ->delete(route('profile.destroy'), [
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasErrorsIn('userDeletion', 'password');

        $this->assertNotNull($this->tenantUser->fresh());
    }

    public function test_registered_user_without_rentals_or_reservations_can_delete_account(): void
    {
        // Log in without activating tenant details or reservations
        $response = $this->actingAs($this->tenantUser)
            ->from(route('profile.edit'))
            ->delete(route('profile.destroy'), [
                'password' => 'password123',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull(User::find($this->tenantUser->id));
    }
}
