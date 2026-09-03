<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Pemetaan rute pengecualian berdasarkan role user (Open/Closed Principle).
     */
    protected const EXCLUDED_ROUTES = [
        'admin' => [
            'admin.force-change-password',
            'admin.force-change-password.update',
            'admin.logout',
            'logout',
        ],
        'penyewa' => [
            'profile.edit',
            'profile.update',
            'password.update',
            'penyewa.logout',
            'logout',
        ],
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Guard Clause: Lewati jika user tidak terautentikasi atau tidak wajib ganti password
        if (!$user || !$user->require_password_change) {
            return $next($request);
        }

        $role = $user->role;
        $excludedRoutes = self::EXCLUDED_ROUTES[$role] ?? [];

        // Guard Clause: Lewati jika rute saat ini ada dalam daftar pengecualian
        if ($request->routeIs($excludedRoutes)) {
            return $next($request);
        }

        // Pengalihan berdasarkan role
        if ($role === 'admin') {
            return redirect()->route('admin.force-change-password');
        }

        if ($role === 'penyewa') {
            return redirect()->route('profile.edit', ['tab' => 'password'])
                ->with('error', __('Anda wajib mengganti password default Anda terlebih dahulu sebelum dapat mengakses fitur portal.'));
        }

        return $next($request);
    }
}
