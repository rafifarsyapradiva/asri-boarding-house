<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AdminPasswordResetController extends Controller
{
    /**
     * Display the password reset link request view for Administrator.
     */
    public function showForgotPassword(): View
    {
        return view('auth.admin-forgot-password');
    }

    /**
     * Handle an incoming password reset link request for Administrator.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        if ($request->has('email')) {
            $request->merge([
                'email' => trim(strtolower($request->input('email'))),
            ]);
        }

        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        // Validate that user exists and has role 'admin'
        $user = User::where('email', $request->email)->first();

        if (!$user || $user->role !== 'admin') {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akses ditolak. Email tidak terdaftar sebagai administrator.']);
        }

        // Send reset link using default broker
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            \Illuminate\Support\Facades\Log::error('SMTP Error during admin password reset: ' . $e->getMessage(), [
                'email' => $request->input('email')
            ]);
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Gagal menghubungi server email (SMTP). Silakan coba lagi nanti atau hubungi pengembang sistem.']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('System Error during admin password reset: ' . $e->getMessage(), [
                'email' => $request->input('email')
            ]);
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Terjadi kesalahan sistem saat memproses permintaan reset password.']);
        }

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', $this->getTranslation($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => $this->getTranslation($status)]);
    }

    /**
     * Display the password reset view for Administrator.
     */
    public function showResetPassword(Request $request, $token = null): View
    {
        return view('auth.admin-reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle an incoming new password request for Administrator.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        if ($request->has('email')) {
            $request->merge([
                'email' => trim(strtolower($request->input('email'))),
            ]);
        }

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'token.required' => 'Token reset wajib dilampirkan.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal harus :min karakter.',
        ]);

        // Validate that user exists and has role 'admin'
        $user = User::where('email', $request->email)->first();

        if (!$user || $user->role !== 'admin') {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akses ditolak. Email tidak terdaftar sebagai administrator.']);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->password = Hash::make($request->password);
                $user->require_password_change = false; // reset flag if exists
                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('status', $this->getTranslation($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => $this->getTranslation($status)]);
    }

    /**
     * Get the translated message for the password reset status.
     */
    private function getTranslation(string $status): string
    {
        $messages = [
            Password::RESET_LINK_SENT => 'Tautan atur ulang kata sandi telah dikirim ke email Anda.',
            Password::PASSWORD_RESET => 'Kata sandi Anda telah berhasil diatur ulang.',
            Password::INVALID_TOKEN => 'Token atur ulang kata sandi ini tidak valid.',
            Password::INVALID_USER => 'Kami tidak dapat menemukan administrator dengan alamat email tersebut.',
            Password::RESET_THROTTLED => 'Harap tunggu beberapa saat sebelum mencoba kembali.',
        ];

        return $messages[$status] ?? __($status);
    }
}
