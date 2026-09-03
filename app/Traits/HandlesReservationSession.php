<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait HandlesReservationSession
{
    /**
     * Dapatkan pemetaan kunci input request ke kunci session untuk data reservasi.
     * Mengikuti Open/Closed Principle agar dapat di-override oleh kelas pengguna.
     *
     * @return array<string, string>
     */
    protected function getReservationSessionKeys(): array
    {
        return [
            'tipe_sewa' => 'reservasi_tipe_sewa',
            'durasi' => 'reservasi_durasi',
        ];
    }

    /**
     * Dapatkan daftar path URL yang dikecualikan dari perekaman URL tujuan (url.intended).
     * Mengambil dari konfigurasi sistem atau fallback ke array default.
     *
     * @return array<int, string>
     */
    protected function getExcludedIntendedPaths(): array
    {
        return config('auth.excluded_intended_paths', [
            '/login',
            '/register',
            '/forgot-password',
            '/reset-password',
            '/auth/',
            '/logout',
        ]);
    }

    /**
     * Simpan parameter reservasi ke session dan catat URL asal jika valid.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function storeReservationSessionState(Request $request): string
    {
        // 1. Simpan parameter reservasi yang terisi (non-empty) ke session
        foreach ($this->getReservationSessionKeys() as $requestKey => $sessionKey) {
            if ($request->filled($requestKey)) {
                session([$sessionKey => $request->input($requestKey)]);
            }
        }

        // 2. Simpan URL asal untuk redirect pasca login jika memenuhi kriteria
        $previousUrl = url()->previous() ?: '';
        if ($this->shouldSaveIntendedUrl($previousUrl, $request)) {
            session(['url.intended' => $previousUrl]);
        }

        return $previousUrl;
    }

    /**
     * Tentukan apakah URL asal layak disimpan sebagai url.intended.
     * Mencegah open redirect, menghindari self-referencing, dan menyederhanakan guard clause.
     *
     * @param  string  $previousUrl
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldSaveIntendedUrl(string $previousUrl, Request $request): bool
    {
        if (empty($previousUrl)) {
            return false;
        }

        // Jangan timpa url.intended yang sudah ada
        if (session()->has('url.intended')) {
            return false;
        }

        // Mencegah self-referencing redirect loop (URL asal sama dengan URL request saat ini)
        if ($previousUrl === $request->fullUrl()) {
            return false;
        }

        // KEAMANAN: Pastikan URL bersifat lokal dengan validasi Host dan Port secara ketat (Anti-Open Redirect)
        $parsedPrevHost = parse_url($previousUrl, PHP_URL_HOST);
        $parsedPrevPort = parse_url($previousUrl, PHP_URL_PORT);
        $requestHost    = $request->getHost();
        $requestPort    = $request->getPort();

        if (strtolower((string) $parsedPrevHost) !== strtolower($requestHost)) {
            return false;
        }

        if ($parsedPrevPort !== null && $parsedPrevPort !== $requestPort) {
            return false;
        }

        // Ambil path saja untuk dicocokkan dengan blacklist
        $path = parse_url($previousUrl, PHP_URL_PATH) ?: '/';

        // Gunakan helper Laravel Str::contains yang menerima array pattern untuk efisiensi
        return !Str::contains($path, $this->getExcludedIntendedPaths());
    }
}

