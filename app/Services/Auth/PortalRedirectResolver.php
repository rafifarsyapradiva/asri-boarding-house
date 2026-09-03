<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;

class PortalRedirectResolver
{
    /**
     * Inject AuthFactory to enforce Dependency Inversion Principle (D in SOLID).
     */
    public function __construct(
        private readonly AuthFactory $auth
    ) {}

    /**
     * Resolves guest redirection target route.
     */
    public function resolveGuestRedirect(Request $request): string
    {
        if ($request->is(['admin', 'admin/*'])) {
            return route('admin.login');
        }

        if ($request->is(['penyewa', 'penyewa/*'])) {
            return route('penyewa.login');
        }

        return route('reservasi.login');
    }

    /**
     * Resolves authenticated user redirection target route or URL.
     */
    public function resolveAuthenticatedRedirect(Request $request): string
    {
        $user = $request->user();

        if (!$user) {
            return route('landing.index');
        }

        // 1. If accessing Admin login portal, check if user is admin
        if ($request->is(['admin', 'admin/login'])) {
            return $user->isAdmin()
                ? route('admin.dashboard')
                : $this->logoutAndGetRedirectUrl($request);
        }

        // 2. If accessing Tenant login portal, check if user is active tenant
        if ($request->is(['penyewa', 'penyewa/login'])) {
            return $this->isUserActiveTenant($user)
                ? route('penyewa.dashboard')
                : $this->logoutAndGetRedirectUrl($request);
        }

        // 3. If accessing Reservation login/register/forgot-password/google
        if ($this->isReservationRoute($request)) {
            if ($user->isAdmin()) {
                return $this->logoutAndGetRedirectUrl($request);
            }
            return route('landing.index');
        }

        // Fallback default redirects
        return $this->resolveFallbackRoute($user);
    }

    private function isUserActiveTenant(User $user): bool
    {
        return $user->role === User::ROLE_PENYEWA && $user->isActiveTenant();
    }

    private function isReservationRoute(Request $request): bool
    {
        return $request->is([
            'reservasi/login',
            'reservasi/register',
            'forgot-password',
            'reset-password/*',
            'auth/google',
        ]);
    }

    private function resolveFallbackRoute(User $user): string
    {
        if ($user->isAdmin()) {
            return route('admin.dashboard');
        }

        if ($this->isUserActiveTenant($user)) {
            return route('penyewa.dashboard');
        }

        return route('landing.index');
    }

    private function logoutAndGetRedirectUrl(Request $request): string
    {
        $this->auth->guard()->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $request->fullUrl();
    }
}
