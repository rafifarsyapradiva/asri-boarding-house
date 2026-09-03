<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Penyewa;
use App\Models\Kamar;
use App\Models\Reservasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class SocialiteLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_google_redirect_returns_redirect(): void
    {
        $response = $this->get(route('auth.google'));
        
        // Ensure it attempts to redirect to Google
        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_active_tenant_can_login_via_google_and_redirects_to_dashboard(): void
    {
        // 1. Create a user who is an active tenant
        $kamar = Kamar::create([
            'nomor_kamar' => '104',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'terisi',
        ]);

        $user = User::factory()->create([
            'email' => 'active.tenant@gmail.com',
            'role' => 'penyewa',
            'no_hp' => '081234567892',
        ]);

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Rian',
            'no_wali' => '081111111111',
            'status' => 'aktif',
        ]);

        // 2. Mock Socialite response
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getEmail')->andReturn('active.tenant@gmail.com');
        $googleUser->shouldReceive('getName')->andReturn('Active Tenant');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // 3. Call callback route
        $response = $this->get(route('auth.google.callback'));

        // 4. Verify assertion
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('penyewa.dashboard'));
    }

    public function test_pending_reservasi_user_can_login_via_google_and_redirects_to_reservation_dashboard(): void
    {
        // 1. Create a user who has a pending reservation
        $kamar = Kamar::create([
            'nomor_kamar' => '105',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'tersedia',
        ]);

        $user = User::factory()->create([
            'email' => 'pending.user@gmail.com',
            'role' => 'penyewa',
            'no_hp' => '081234567892',
        ]);

        Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'durasi' => 1,
            'total_harga' => 750000,
            'status' => 'pending',
        ]);

        // 2. Mock Socialite response
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getEmail')->andReturn('pending.user@gmail.com');
        $googleUser->shouldReceive('getName')->andReturn('Pending User');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // 3. Call callback route
        $response = $this->get(route('auth.google.callback'));

        // 4. Verify assertion
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('penyewa.reservasi.dashboard'));
    }

    public function test_admin_cannot_login_via_google(): void
    {
        // 1. Create an admin user
        $admin = User::factory()->create([
            'email' => 'admin.house@gmail.com',
            'role' => 'admin',
        ]);

        // 2. Mock Socialite response
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getEmail')->andReturn('admin.house@gmail.com');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // 3. Call callback route
        $response = $this->get(route('auth.google.callback'));

        // 4. Verify assertion
        $this->assertGuest();
        $response->assertRedirect(route('reservasi.login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_admin_cannot_login_via_google_and_redirects_to_penyewa_login_if_origin_is_penyewa(): void
    {
        // 1. Create an admin user
        $admin = User::factory()->create([
            'email' => 'admin.house2@gmail.com',
            'role' => 'admin',
        ]);

        // Set session origin
        $this->withSession(['socialite_login_from' => 'penyewa']);

        // 2. Mock Socialite response
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getEmail')->andReturn('admin.house2@gmail.com');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // 3. Call callback route
        $response = $this->get(route('auth.google.callback'));

        // 4. Verify redirection is to penyewa.login
        $this->assertGuest();
        $response->assertRedirect(route('penyewa.login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_non_active_tenant_cannot_login_via_google_at_penyewa_portal(): void
    {
        // 1. Create a user but NOT as an active tenant
        $user = User::factory()->create([
            'email' => 'inactive.tenant@gmail.com',
            'role' => 'penyewa',
        ]);

        // Set session origin
        $this->withSession(['socialite_login_from' => 'penyewa']);

        // 2. Mock Socialite response
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getEmail')->andReturn('inactive.tenant@gmail.com');
        $googleUser->shouldReceive('getName')->andReturn('Inactive Tenant');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // 3. Call callback route
        $response = $this->get(route('auth.google.callback'));

        // 4. Verify redirection is to penyewa.login with error
        $this->assertGuest();
        $response->assertRedirect(route('penyewa.login'));
        $response->assertSessionHasErrors('email');
    }


    public function test_active_tenant_can_login_via_google_and_respects_intended_url(): void
    {
        // 1. Create active tenant
        $kamar = Kamar::create([
            'nomor_kamar' => '106',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'terisi',
        ]);

        $user = User::factory()->create([
            'email' => 'active.intended@gmail.com',
            'role' => 'penyewa',
            'no_hp' => '081234567892',
        ]);

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123457',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Rian',
            'no_wali' => '081111111111',
            'status' => 'aktif',
        ]);

        // Set intended url
        $targetUrl = route('penyewa.tagihan.index');
        $this->withSession(['url.intended' => $targetUrl]);

        // 2. Mock Socialite response
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getEmail')->andReturn('active.intended@gmail.com');
        $googleUser->shouldReceive('getName')->andReturn('Active Intended');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // 3. Call callback route
        $response = $this->get(route('auth.google.callback'));

        // 4. Verify redirect goes to the intended tenant route
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect($targetUrl);
    }

    public function test_active_tenant_profile_complete_redirects_to_tenant_dashboard(): void
    {
        // 1. Create user who is active tenant but has temporary/missing phone number
        $kamar = Kamar::create([
            'nomor_kamar' => '107',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'terisi',
        ]);

        $user = User::factory()->create([
            'email' => 'temp.profile@gmail.com',
            'role' => 'penyewa',
            'no_hp' => 'temp_123',
        ]);

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123459',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Rian',
            'no_wali' => '081111111111',
            'status' => 'aktif',
        ]);

        // 2. Submit form to complete profile
        $response = $this->actingAs($user)->post(route('profil.complete.store'), [
            'no_hp' => '089999999999',
        ]);

        // 3. Verify it redirects to penyewa.dashboard
        $response->assertRedirect(route('penyewa.dashboard'));
        $this->assertEquals('089999999999', $user->fresh()->no_hp);
    }



    public function test_non_whitelisted_google_email_is_blocked_at_penyewa_portal_and_redirects_with_error(): void
    {
        // Set session origin
        $this->withSession(['socialite_login_from' => 'penyewa']);

        // Mock Socialite response with an email not in the DB
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getEmail')->andReturn('unregistered.stranger@gmail.com');
        $googleUser->shouldReceive('getName')->andReturn('Stranger');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // Call callback route
        $response = $this->get(route('auth.google.callback'));

        // Verify redirection and check that guest remains unauthenticated (whitelist block)
        $this->assertGuest();
        $response->assertRedirect(route('penyewa.login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_non_registered_google_email_is_automatically_registered_and_redirects_to_profile_complete(): void
    {
        // Set session origin to guest/reservasi
        $this->withSession(['socialite_login_from' => 'reservasi']);

        // Mock Socialite response with an email not in the DB
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getEmail')->andReturn('unregistered.newbie@gmail.com');
        $googleUser->shouldReceive('getName')->andReturn('Newbie Guest');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // Call callback route
        $response = $this->get(route('auth.google.callback'));

        // Verify that a new user was created
        $user = User::where('email', 'unregistered.newbie@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('penyewa', $user->role);
        $this->assertTrue(str_starts_with($user->no_hp, 'temp_'));

        // Verify redirection and authentication
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('profil.complete'));
    }
}
