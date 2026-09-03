<?php

namespace App\Services;

use App\Models\User;
use App\Models\Reservasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class AuthRedirectService
{
    /**
     * Resolves the target route or URL destination after successful authentication.
     *
     * @param User $user
     * @param string|null $intended
     * @return array{type: 'route'|'url', target: string}
     */
    public function resolvePostAuthDestination(User $user, ?string $intended = null): array
    {
        // 1. Validasi Kelengkapan Profil (Menghindari kegagalan WhatsApp/Fonnte delivery)
        if (empty($user->no_hp) || Str::startsWith($user->no_hp, 'temp_')) {
            return ['type' => 'route', 'target' => 'profil.complete'];
        }

        $isActiveTenant = $user->penyewa && ($user->penyewa->status === 'aktif');

        // 2. Tangani Intended URL jika ada di sesi
        if ($intended) {
            $isAdminRoute = Str::contains($intended, '/admin');
            $isActiveTenantRoute = Str::contains($intended, '/penyewa') && !Str::contains($intended, '/penyewa/reservasi') && !Str::contains($intended, '/penyewa/profile');

            if (!$isAdminRoute) {
                if ($isActiveTenantRoute) {
                    if ($isActiveTenant) {
                        return ['type' => 'url', 'target' => $intended];
                    }
                } else {
                    if (session()->has('reservasi_tipe_sewa') && session()->has('reservasi_durasi')) {
                        $tipeSewa = session()->pull('reservasi_tipe_sewa');
                        $durasi = session()->pull('reservasi_durasi');
                        $symbol = str_contains($intended, '?') ? '&' : '?';
                        $intended .= $symbol . 'tipe_sewa=' . urlencode($tipeSewa) . '&durasi=' . urlencode($durasi);
                    }
                    return ['type' => 'url', 'target' => $intended];
                }
            }
        }

        // 3. Fallback: Cek apakah pengguna adalah Penyewa Aktif
        if ($isActiveTenant) {
            return ['type' => 'route', 'target' => 'penyewa.dashboard'];
        }

        // 4. Fallback: Cek apakah pengguna memiliki reservasi yang sedang berjalan/pending
        $pendingReservasi = Reservasi::where('user_id', $user->id)
            ->where('status', '!=', 'batal')
            ->latest()
            ->first();

        if ($pendingReservasi) {
            return ['type' => 'route', 'target' => 'penyewa.reservasi.dashboard'];
        }

        // 5. Fallback utama ke Halaman Utama (Landing)
        return ['type' => 'route', 'target' => 'landing.index'];
    }

    /**
     * Menentukan rute pengalihan yang sesuai setelah otentikasi berhasil (HTTP Redirect wrapper).
     */
    public function resolvePostAuthRedirect(User $user): RedirectResponse
    {
        $intended = session()->pull('url.intended');
        $destination = $this->resolvePostAuthDestination($user, $intended);

        if ($destination['type'] === 'url') {
            return redirect()->to($destination['target']);
        }

        return redirect()->route($destination['target']);
    }
}
