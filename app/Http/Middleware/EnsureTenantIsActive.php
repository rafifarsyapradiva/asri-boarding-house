<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    /**
     * Rute read-only historis yang diizinkan untuk penyewa non-aktif.
     */
    protected const ALLOWED_READONLY_ROUTES = [
        'penyewa.tagihan.index',
        'penyewa.tagihan.show',
        'penyewa.nota.download',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Guard Clause: Hanya jalankan pengecekan untuk role penyewa
        if (!$user || $user->role !== 'penyewa') {
            return $next($request);
        }

        // Guard Clause: Izinkan mantan penyewa mengakses rute read-only historis
        if ($request->routeIs(self::ALLOWED_READONLY_ROUTES)) {
            return $next($request);
        }

        // Guard Clause: Lanjutkan jika penyewa berstatus aktif
        if ($user->isActiveTenant()) {
            return $next($request);
        }

        // Alihkan ke dashboard reservasi jika memiliki proses yang sedang berlangsung (pending/DP)
        if ($user->hasActiveReservations()) {
            return redirect()->route('penyewa.reservasi.dashboard');
        }

        return redirect()->route('landing.index')
            ->with('error', __('Akses ditolak. Halaman ini khusus untuk penyewa aktif.'));
    }
}
