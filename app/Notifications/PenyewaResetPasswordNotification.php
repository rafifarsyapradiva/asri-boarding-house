<?php

namespace App\Notifications;

class PenyewaResetPasswordNotification extends BaseResetPasswordNotification
{
    public const ROUTE_ACTIVE = 'penyewa.password.reset';
    public const ROUTE_INACTIVE = 'password.reset';

    /**
     * {@inheritdoc}
     */
    protected function getRoleName(): string
    {
        return 'Penyewa';
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveRouteName(mixed $notifiable): string
    {
        $isActive = method_exists($notifiable, 'isActiveTenant')
            ? (bool) $notifiable->isActiveTenant()
            : false;

        return $isActive ? self::ROUTE_ACTIVE : self::ROUTE_INACTIVE;
    }
}

