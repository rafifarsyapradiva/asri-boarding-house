<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PenyewaAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenyewaLoginController extends Controller
{
    /**
     * Display the login view for Penyewa.
     */
    public function showLogin()
    {
        return view('auth.penyewa-login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request, PenyewaAuthService $authService)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginValue = $request->input('login');
        $user = null;

        // RESOLUSI USER DI AWAL: Memastikan validasi role dan status penyewa aktif dilakukan sebelum attempt
        if (filter_var($loginValue, FILTER_VALIDATE_EMAIL)) {
            $user = \App\Models\User::where('email', trim(strtolower($loginValue)))
                ->where('role', 'penyewa')
                ->first();
        } else {
            $numberPart = $this->normalizePhoneNumber($loginValue);
            if ($numberPart !== null) {
                $user = \App\Models\User::where('role', 'penyewa')
                    ->where(function($query) use ($numberPart) {
                        $query->where('no_hp', '0' . $numberPart)
                              ->orWhere('no_hp', '62' . $numberPart)
                              ->orWhere('no_hp', '+62' . $numberPart);
                    })->first();
            }
        }

        // VALIDASI STATUS: Tolak di awal tanpa membuat session atau trigger login hooks jika status tidak aktif
        if (!$user || !$user->penyewa || $user->penyewa->status !== 'aktif') {
            return back()->withErrors([
                'login' => __('auth.failed'),
            ])->onlyInput('login');
        }

        // Lakukan otentikasi berdasarkan kredensial user yang sudah valid secara peran & status
        $credentials = [
            'email' => $user->email,
            'password' => $request->password,
        ];

        $authenticated = Auth::attempt($credentials, $request->boolean('remember'));

        // Fallback untuk pencocokan password default (08... <-> 62...) jika user masih menggunakan password awal
        if (!$authenticated && $authService->attemptInitialPasswordFallback($user, $request->password)) {
            Auth::login($user, $request->boolean('remember'));
            $authenticated = true;
        }

        if ($authenticated) {
            $request->session()->regenerate();

            $intended = session()->pull('url.intended', route('penyewa.dashboard'));
            if (\Illuminate\Support\Str::contains($intended, '/penyewa')) {
                return redirect()->to($intended);
            }

            return redirect()->route('penyewa.dashboard');
        }

        return back()->withErrors([
            'login' => __('auth.failed'),
        ])->onlyInput('login');
    }

    /**
     * Helper normalisasi input no HP untuk mereduksi kompleksitas kognitif.
     */
    private function normalizePhoneNumber(string $phone): ?string
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);
        $cleanNoHp = ltrim($cleaned, '+');
        
        if ($cleanNoHp === '') {
            return null;
        } 
        
        if (str_starts_with($cleanNoHp, '62')) {
            return substr($cleanNoHp, 2);
        } 
        
        if (str_starts_with($cleanNoHp, '0')) {
            return substr($cleanNoHp, 1);
        }
        
        return $cleanNoHp;
    }

    /**
     * Log the tenant out of the session.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('penyewa.login');
    }
}
