<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi Admin');
    }

    public function test_admin_forgot_password_link_can_be_requested(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/forgot-password', ['email' => $admin->email]);

        $response->assertSessionHasNoErrors();
        Notification::assertSentTo($admin, AdminResetPasswordNotification::class);
    }

    public function test_non_admin_cannot_request_admin_password_reset(): void
    {
        Notification::fake();

        // Create a regular user (role is penyewa)
        $user = User::factory()->create([
            'role' => 'penyewa',
        ]);

        $response = $this->post('/admin/forgot-password', ['email' => $user->email]);

        $response->assertSessionHasErrors(['email']);
        Notification::assertNotSentTo($user, AdminResetPasswordNotification::class);
    }

    public function test_admin_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->post('/admin/forgot-password', ['email' => $admin->email]);

        Notification::assertSentTo($admin, AdminResetPasswordNotification::class, function ($notification) use ($admin) {
            $response = $this->get('/admin/reset-password/'.$notification->token.'?email='.$admin->email);

            $response->assertStatus(200);
            $response->assertSee('Atur Ulang Kata Sandi Admin');

            return true;
        });
    }

    public function test_admin_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'require_password_change' => true,
        ]);

        $this->post('/admin/forgot-password', ['email' => $admin->email]);

        Notification::assertSentTo($admin, AdminResetPasswordNotification::class, function ($notification) use ($admin) {
            $response = $this->post('/admin/reset-password', [
                'token' => $notification->token,
                'email' => $admin->email,
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('admin.login'));

            // Verify the require_password_change flag is reset to false
            $admin->refresh();
            $this->assertFalse($admin->require_password_change);

            return true;
        });
    }
}
