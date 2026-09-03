<?php

namespace App\Notifications;

class AdminResetPasswordNotification extends BaseResetPasswordNotification
{
    public const ROUTE_NAME = 'admin.password.reset';

    /**
     * {@inheritdoc}
     */
    protected function getRoleName(): string
    {
        return 'Admin';
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveRouteName(mixed $notifiable): string
    {
        return self::ROUTE_NAME;
    }
}

