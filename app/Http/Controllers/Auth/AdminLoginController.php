<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    /**
     * Display the login view for Administrator.
     */
    public function showLogin()
    {
        return view('auth.admin-login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials['email'] = trim(strtolower($credentials['email']));
        
        // Membatasi query otentikasi hanya untuk pengguna dengan peran admin
        $credentials['role'] = 'admin';

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Session Fixation Protection
            $request->session()->regenerate();

            $intended = session()->pull('url.intended', route('admin.dashboard'));
            if (\Illuminate\Support\Str::contains($intended, '/admin')) {
                return redirect()->to($intended);
            }

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    /**
     * Log the admin out of the session.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
