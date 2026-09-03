<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthRedirectService;
use App\Traits\HandlesReservationSession;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    use HandlesReservationSession;

    public function __construct(
        protected AuthRedirectService $redirectService
    ) {}

    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $previousUrl = $this->storeReservationSessionState($request);

        if ($request->has('from')) {
            session(['socialite_login_from' => $request->query('from')]);
        } else {
            $from = ($previousUrl && Str::contains($previousUrl, '/penyewa')) ? 'penyewa' : 'reservasi';
            session(['socialite_login_from' => $from]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            $rawEmail = $googleUser->getEmail();
            if (empty($rawEmail)) {
                return $this->redirectWithError('Gagal mendapatkan data email dari akun Google Anda. Pastikan izin berbagi email telah diaktifkan.');
            }
            
            $email = trim(strtolower($rawEmail));
            $from = session('socialite_login_from', 'reservasi');

            // Guardrail 1: Non-penyewa MUST NOT be allowed to log in via Google OAuth
            $existingUser = User::where('email', $email)->first();
            if ($existingUser && $existingUser->role !== 'penyewa') {
                $errorMessage = $existingUser->role === 'admin'
                    ? 'Akses ditolak. Administrator tidak diperbolehkan login menggunakan Google OAuth.'
                    : 'Akses ditolak. Akun Anda tidak terdaftar sebagai penyewa.';
                return $this->redirectWithError($errorMessage);
            }

            // Guardrail 2: Tenant portal requires an active tenant profile
            if ($from === 'penyewa') {
                $isActiveTenant = $existingUser && $existingUser->penyewa && ($existingUser->penyewa->status === 'aktif');
                if (!$existingUser || $existingUser->role !== 'penyewa' || !$isActiveTenant) {
                    return redirect()->route('penyewa.login')->withErrors([
                        'email' => 'Akses ditolak. Portal ini khusus untuk Penyewa Aktif yang terdaftar.',
                    ]);
                }
            }

            // Auto-registration for new guests or retrieve existing user
            if (!$existingUser) {
                $user = $this->registerGoogleUserAtomically($googleUser, $email);
            } else {
                $user = $existingUser;
            }

            Auth::login($user, true);
            request()->session()->regenerate();

            return $this->redirectService->resolvePostAuthRedirect($user);

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return $this->redirectWithError('Sesi masuk dengan Google telah kedaluwarsa atau tidak valid. Silakan coba kembali.');
        } catch (\InvalidArgumentException $e) {
            return $this->redirectWithError('Permintaan otentikasi tidak valid. Silakan coba kembali.');
        } catch (\Throwable $e) {
            Log::error('Google Socialite callback exception: ' . $e->getMessage(), ['exception' => $e]);
            return $this->redirectWithError('Gagal melakukan login dengan akun Google. Silakan coba beberapa saat lagi.');
        }
    }

    /**
     * Registrasi pengguna baru dari Google OAuth secara atomik dengan penanganan concurrency.
     */
    private function registerGoogleUserAtomically($googleUser, string $email): User
    {
        try {
            return DB::transaction(function () use ($googleUser, $email) {
                $userInside = User::where('email', $email)->lockForUpdate()->first();
                if ($userInside) {
                    return $userInside;
                }

                $googleName = trim($googleUser->getName() ?? '');
                if ($googleName === '') {
                    $emailParts = explode('@', $email);
                    $googleName = $emailParts[0] ?? 'Penyewa Baru';
                }
                $formattedName = ucwords(str_replace(['.', '-', '_'], ' ', $googleName));
                $tempPhone = 'temp_' . Str::random(15);

                return User::create([
                    'nama' => $formattedName,
                    'email' => $email,
                    'password' => bcrypt(Str::random(16)),
                    'no_hp' => $tempPhone,
                    'role' => 'penyewa',
                    'is_active' => 1,
                ]);
            });
        } catch (UniqueConstraintViolationException $e) {
            return User::where('email', $email)->firstOrFail();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), '1062 Duplicate entry')) {
                return User::where('email', $email)->firstOrFail();
            }
            throw $e;
        }
    }

    /**
     * Helper redirect dengan pesan kesalahan berdasarkan konteks sesi portal.
     */
    private function redirectWithError(string $errorMessage): RedirectResponse
    {
        $from = session('socialite_login_from', 'reservasi');
        $redirectRoute = ($from === 'penyewa') ? 'penyewa.login' : 'reservasi.login';

        return redirect()->route($redirectRoute)->withErrors([
            'email' => $errorMessage,
        ]);
    }
}
