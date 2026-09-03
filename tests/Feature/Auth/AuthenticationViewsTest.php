<?php

namespace Tests\Feature\Auth;


use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationViewsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_route_redirects_to_reservasi_login()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(302);
        $response->assertRedirect(route('reservasi.login'));
    }

    #[Test]
    public function register_route_redirects_to_reservasi_register()
    {
        $response = $this->get(route('register'));
        $response->assertStatus(302);
        $response->assertRedirect(route('reservasi.register'));
    }

    #[Test]
    public function admin_login_screen_can_be_rendered()
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
        $response->assertSee('Admin Login');
    }

    #[Test]
    public function penyewa_login_screen_can_be_rendered()
    {
        $response = $this->get(route('penyewa.login'));
        $response->assertStatus(200);
        $response->assertSee('Portal Penyewa');
    }

    #[Test]
    public function reservasi_login_screen_can_be_rendered()
    {
        $response = $this->get(route('reservasi.login'));
        $response->assertStatus(200);
        $response->assertSee('Sistem Reservasi');
    }

    #[Test]
    public function reservasi_register_screen_can_be_rendered()
    {
        $response = $this->get(route('reservasi.register'));
        $response->assertStatus(200);
        $response->assertSee('Registrasi Akun');
    }

    #[Test]
    public function complete_profile_sanitizes_temporary_phone_prefix()
    {
        $user = User::factory()->create([
            'no_hp' => 'temp_08123456789',
            'nik' => null,
        ]);

        $this->assertEquals('', $user->display_no_hp);

        $response = $this->actingAs($user)->get(route('profil.complete'));
        $response->assertStatus(200);
        $response->assertDontSee('temp_08123456789');
    }
}
