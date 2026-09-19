<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
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

        // Guard Clause: Lanjutkan jika penyewa berstatus aktif
        if ($user->isActiveTenant()) {
            return $next($request);
        }

        // Alihkan ke dashboard reservasi jika memiliki proses yang sedang berlangsung (pending/DP)
        if ($user->hasActiveReservations()) {
            return redirect()->route('penyewa.reservasi.dashboard');
        }

        return redirect()->route('penyewa.reservasi.dashboard')
            ->with('error', __('Akses ditolak. Halaman ini khusus untuk penyewa aktif.'));
    }
}
