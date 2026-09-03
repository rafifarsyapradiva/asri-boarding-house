<?php

namespace Tests\Unit\Notifications;

use App\Models\User;
use App\Notifications\AdminResetPasswordNotification;
use App\Notifications\PenyewaResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Tests\TestCase;

class ResetPasswordNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_base_notification_implements_should_queue()
    {
        $notification = new AdminResetPasswordNotification('token');
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $notification);
    }

    public function test_admin_reset_password_notification_generates_correct_mail()
    {
        $admin = User::factory()->make([
            'nama' => 'Budi Admin',
            'email' => 'admin@asri.com',
            'role' => User::ROLE_ADMIN,
        ]);

        $notification = new AdminResetPasswordNotification('dummy-token-123');
        /** @var MailMessage $mail */
        $mail = $notification->toMail($admin);

        $this->assertEquals('Reset Password Admin - Asri Boarding House', $mail->subject);
        $this->assertEquals('emails.reset-password', $mail->view);
        $this->assertStringContainsString('admin/reset-password/dummy-token-123', $mail->viewData['urlReset']);
        $this->assertEquals('Budi Admin', $mail->viewData['namaPenyewa']);
        $this->assertEquals('Admin', $mail->viewData['roleName']);
        $this->assertEquals(config('auth.passwords.users.expire', 60), $mail->viewData['expireMinutes']);
    }

    public function test_penyewa_aktif_uses_penyewa_reset_route()
    {
        $user = User::factory()->make([
            'nama' => 'Andi Penyewa',
            'email' => 'penyewa@asri.com',
            'role' => User::ROLE_PENYEWA,
        ]);

        $notification = new PenyewaResetPasswordNotification('tenant-token-456');
        /** @var MailMessage $mail */
        $mail = $notification->toMail($user);

        $this->assertEquals('Reset Password Penyewa - Asri Boarding House', $mail->subject);
        $this->assertStringContainsString('/reset-password/tenant-token-456', $mail->viewData['urlReset']);
        $this->assertEquals('Andi Penyewa', $mail->viewData['namaPenyewa']);
        $this->assertEquals('Penyewa', $mail->viewData['roleName']);
    }

    public function test_penyewa_inaktif_uses_default_reset_route()
    {
        $user = User::factory()->make([
            'nama' => null,
            'email' => 'inaktif@asri.com',
            'role' => User::ROLE_PENYEWA,
        ]);

        $notification = new PenyewaResetPasswordNotification('tenant-token-789');
        /** @var MailMessage $mail */
        $mail = $notification->toMail($user);

        $this->assertStringContainsString('/reset-password/tenant-token-789', $mail->viewData['urlReset']);
        $this->assertEquals('Penyewa', $mail->viewData['namaPenyewa']); // Fallback to role name
    }

    public function test_custom_create_url_callback_is_respected()
    {
        $user = User::factory()->make([
            'nama' => 'Callback Test User',
            'email' => 'callback@asri.com',
        ]);

        AdminResetPasswordNotification::createUrlUsing(function ($notifiable, $token) {
            return 'https://custom-domain.com/reset/' . $token . '?email=' . $notifiable->getEmailForPasswordReset();
        });

        $notification = new AdminResetPasswordNotification('custom-token');
        /** @var MailMessage $mail */
        $mail = $notification->toMail($user);

        $this->assertEquals('https://custom-domain.com/reset/custom-token?email=callback@asri.com', $mail->viewData['urlReset']);

        // Reset callback to avoid side effects
        AdminResetPasswordNotification::createUrlUsing(null);
    }
}
