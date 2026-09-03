<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SocialiteControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin user cannot login via Google OAuth.
     */
    public function test_admin_user_cannot_login_via_google_oauth(): void
    {
        User::factory()->create([
            'email' => 'admin@asrikost.com',
            'role' => 'admin',
        ]);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getEmail')->andReturn('admin@asrikost.com');

        Socialite::shouldReceive('driver->stateless->user')->andReturn($abstractUser);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('reservasi.login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test new user registering via Google OAuth is redirected to profile completion when phone is temp.
     */
    public function test_new_google_user_redirected_to_profile_completion(): void
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getEmail')->andReturn('newpenyewa@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn('Budi Santoso');

        Socialite::shouldReceive('driver->stateless->user')->andReturn($abstractUser);

        $response = $this->get(route('auth.google.callback'));

        $this->assertDatabaseHas('users', [
            'email' => 'newpenyewa@gmail.com',
            'role' => 'penyewa',
        ]);

        $response->assertRedirect(route('profil.complete'));
        $this->assertAuthenticated();
    }
}
