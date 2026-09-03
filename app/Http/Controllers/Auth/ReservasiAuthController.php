<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthRedirectService;
use App\Traits\HandlesReservationSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ReservasiAuthController extends Controller
{
    use HandlesReservationSession;

    public function __construct(
        protected AuthRedirectService $redirectService
    ) {}

    /**
     * Display the login view for reservation users.
     */
    public function showLogin(Request $request): View
    {
        $this->storeReservationSessionState($request);

        return view('auth.reservasi-login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials['email'] = trim(strtolower($credentials['email']));
        
        // Membatasi query otentikasi hanya untuk pengguna dengan peran penyewa
        // Tindakan ini mencegah password oracle vulnerability
        $credentials['role'] = 'penyewa';

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Session Fixation Protection
            $request->session()->regenerate();

            return $this->redirectService->resolvePostAuthRedirect(Auth::user());
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    /**
     * Display the registration view.
     */
    public function showRegister(Request $request): View
    {
        $this->storeReservationSessionState($request);

        return view('auth.reservasi-register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        if ($request->has('email')) {
            $request->merge([
                'email' => trim(strtolower((string) $request->input('email'))),
            ]);
        }

        if ($request->has('no_hp') && is_string($request->input('no_hp'))) {
            $request->merge([
                'no_hp' => preg_replace('/[^0-9+]/', '', $request->input('no_hp')),
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'no_hp' => ['required', 'string', 'regex:/^(08|628|\+628)[0-9]{7,11}$/', 'unique:users,no_hp'],
        ], [
            'no_hp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_hp.regex' => 'Format nomor WhatsApp tidak valid (gunakan format Indonesia seperti 0812xxx atau 62812xxx).',
            'no_hp.unique' => 'Nomor WhatsApp sudah terdaftar pada akun lain.',
        ]);

        $noHp = $request->no_hp ?: 'temp_' . time() . '_' . rand(1000, 9999);

        $user = User::create([
            'nama' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_hp' => $noHp,
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        Auth::login($user);

        // Session Fixation Protection
        $request->session()->regenerate();

        return $this->redirectService->resolvePostAuthRedirect($user);
    }

    /**
     * Log the user out of the session and redirect appropriately.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $redirectRoute = 'landing.index';

        if ($user) {
            $redirectRoute = match ($user->role) {
                'admin' => 'admin.login',
                default => ($user->penyewa && $user->penyewa->status === 'aktif') ? 'penyewa.login' : 'reservasi.login',
            };
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($redirectRoute);
    }
}
