<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Abstract Base Notification for Reset Password using the Template Method Pattern.
 * Supports asynchronous queued delivery out-of-the-box.
 */
abstract class BaseResetPasswordNotification extends ResetPasswordNotification implements ShouldQueue
{
    use Queueable;

    public const VIEW_TEMPLATE = 'emails.reset-password';

    /**
     * Get the role title/label for the notification.
     */
    abstract protected function getRoleName(): string;

    /**
     * Resolve the route name for password reset.
     *
     * @param  mixed  $notifiable
     */
    abstract protected function resolveRouteName(mixed $notifiable): string;

    /**
     * Get the auth password broker name (default: 'users').
     */
    protected function getBrokerName(): string
    {
        return 'users';
    }

    /**
     * Get the reset URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable): string
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        return route($this->resolveRouteName($notifiable), [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], true);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        $roleName = $this->getRoleName();
        $nama = $notifiable->nama ?? $roleName;
        $appName = config('app.name', 'Asri Boarding House');
        $broker = $this->getBrokerName();
        $expireMinutes = (int) config("auth.passwords.{$broker}.expire", 60);

        return (new MailMessage)
            ->subject("Reset Password {$roleName} - {$appName}")
            ->view(self::VIEW_TEMPLATE, [
                'urlReset' => $url,
                'namaPenyewa' => $nama, // Key 'namaPenyewa' dipertahankan demi kompatibilitas Blade view
                'roleName' => $roleName,
                'expireMinutes' => $expireMinutes,
            ]);
    }
}

