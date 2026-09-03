<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Rute yang dikecualikan dari pemeriksaan kelengkapan profil.
     */
    protected const EXCLUDED_ROUTES = [
        'profil.complete',
        'profil.complete.store',
        'profile.edit',
        'profile.update',
        'penyewa.profile.edit',
        'penyewa.profile.update',
        'logout',
        'penyewa.logout',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Guard Clause: Hanya periksa user terautentikasi dengan role penyewa
        if (!$user || $user->role !== 'penyewa') {
            return $next($request);
        }

        // Guard Clause: Lanjutkan jika profil lengkap atau mengakses rute pengecualian
        if ($user->isProfileComplete() || $request->routeIs(self::EXCLUDED_ROUTES)) {
            return $next($request);
        }

        return redirect()->route('profil.complete');
    }
}
