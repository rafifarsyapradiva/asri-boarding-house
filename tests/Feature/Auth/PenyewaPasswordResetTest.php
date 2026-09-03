<?php

namespace Tests\Feature\Auth;

use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\User;
use App\Notifications\PenyewaResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PenyewaPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private User $activeTenantUser;
    private User $inactiveTenantUser;
    private User $adminUser;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        // 1. Active Tenant
        $this->activeTenantUser = User::create([
            'nama' => 'Penyewa Aktif',
            'email' => 'aktif@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        Penyewa::create([
            'user_id' => $this->activeTenantUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => Carbon::now()->toDateString(),
            'nama_wali' => 'Wali Aktif',
            'no_wali' => '081122334455',
            'deposit' => 200000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
        ]);

        // 2. Inactive Tenant
        $this->inactiveTenantUser = User::create([
            'nama' => 'Penyewa Nonaktif',
            'email' => 'nonaktif@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567891',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        Penyewa::create([
            'user_id' => $this->inactiveTenantUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1234567890123457',
            'tanggal_masuk' => Carbon::now()->toDateString(),
            'nama_wali' => 'Wali Nonaktif',
            'no_wali' => '081122334456',
            'deposit' => 200000,
            'status' => 'nonaktif',
            'tanggal_billing' => 1,
        ]);

        // 3. Admin User
        $this->adminUser = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567892',
            'role' => 'admin',
            'is_active' => 1,
        ]);
    }

    public function test_penyewa_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/penyewa/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi');
        $response->assertSee('Portal Penyewa');
    }

    public function test_active_penyewa_can_request_reset_password_link(): void
    {
        Notification::fake();

        $response = $this->post('/penyewa/forgot-password', [
            'email' => $this->activeTenantUser->email
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302); // Redirect back

        Notification::assertSentTo($this->activeTenantUser, PenyewaResetPasswordNotification::class);
    }

    public function test_inactive_penyewa_can_request_reset_password_link(): void
    {
        Notification::fake();

        $response = $this->post('/penyewa/forgot-password', [
            'email' => $this->inactiveTenantUser->email
        ]);

        $response->assertSessionHasNoErrors();
        Notification::assertSentTo($this->inactiveTenantUser, PenyewaResetPasswordNotification::class);
    }

    public function test_non_penyewa_role_cannot_request_reset_password_link(): void
    {
        Notification::fake();

        $response = $this->post('/penyewa/forgot-password', [
            'email' => $this->adminUser->email
        ]);

        $response->assertSessionHasErrors('email');
        Notification::assertNotSentTo($this->adminUser, PenyewaResetPasswordNotification::class);
    }

    public function test_penyewa_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $this->post('/penyewa/forgot-password', ['email' => $this->activeTenantUser->email]);

        Notification::assertSentTo($this->activeTenantUser, PenyewaResetPasswordNotification::class, function ($notification) {
            $response = $this->get('/penyewa/reset-password/'.$notification->token.'?email='.$this->activeTenantUser->email);

            $response->assertStatus(200);
            $response->assertSee('Atur Ulang Kata Sandi');

            return true;
        });
    }

    public function test_penyewa_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $this->post('/penyewa/forgot-password', ['email' => $this->activeTenantUser->email]);

        Notification::assertSentTo($this->activeTenantUser, PenyewaResetPasswordNotification::class, function ($notification) {
            $response = $this->post('/penyewa/reset-password', [
                'token' => $notification->token,
                'email' => $this->activeTenantUser->email,
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ]);

            $response->assertSessionHasNoErrors();
            $response->assertRedirect(route('penyewa.login'));

            return true;
        });
    }
}
