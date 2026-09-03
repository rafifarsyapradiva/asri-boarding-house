<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Display the first-time password change view.
     */
    public function showForceChangePassword(Request $request): View|RedirectResponse
    {
        if (!$request->user()->require_password_change) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin-force-change-password');
    }

    /**
     * Update the password for first-time login.
     */
    public function updateForcePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->require_password_change) {
            return redirect()->route('admin.dashboard');
        }

        $request->validate([
            'password' => [
                'required',
                'string',
                Password::min(8)->letters()->numbers(),
                'confirmed'
            ],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru harus terdiri dari minimal 8 karakter.',
            'password.letters' => 'Password baru harus mengandung setidaknya satu huruf.',
            'password.numbers' => 'Password baru harus mengandung setidaknya satu angka.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Password baru tidak boleh sama dengan password default/lama Anda.',
            ])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
            'require_password_change' => false,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Password Anda berhasil diperbarui. Selamat datang di Dashboard!');
    }

    /**
     * Show the admin settings form.
     */
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the admin's email and password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // SANITASI NOMOR HP SEBELUM VALIDASI
        if ($request->has('no_hp') && is_string($request->input('no_hp'))) {
            $cleaned = preg_replace('/[^0-9]/', '', $request->input('no_hp'));
            if (str_starts_with($cleaned, '08')) {
                $cleaned = '628' . substr($cleaned, 2);
            }
            $request->merge(['no_hp' => $cleaned]);
        }

        $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
            ],
            'no_hp' => [
                'required',
                'string',
                'regex:/^628[0-9]{8,12}$/',
                'unique:users,no_hp,' . $user->id,
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                'ends_with:@gmail.com',
                'unique:users,email,' . $user->id
            ],
            'password' => [
                'nullable',
                'string',
                Password::min(8)->letters()->numbers(),
                'confirmed'
            ],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'no_hp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_hp.regex' => 'Format nomor WhatsApp tidak valid (harus diawali 628 tanpa spasi/karakter khusus).',
            'no_hp.unique' => 'Nomor WhatsApp sudah terdaftar pada akun lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.ends_with' => 'Email harus menggunakan domain Gmail yang valid (@gmail.com).',
            'email.unique' => 'Email ini sudah terdaftar pada akun lain.',
            'password.min' => 'Password baru harus terdiri dari minimal 8 karakter.',
            'password.letters' => 'Password baru harus mengandung setidaknya satu huruf.',
            'password.numbers' => 'Password baru harus mengandung setidaknya satu angka.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $updates = [
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
        ];

        if ($request->filled('password')) {
            $updates['password'] = Hash::make($request->password);
        }

        $user->update($updates);

        return redirect()->route('admin.profile.edit')->with('success', 'Profil admin berhasil diperbarui!');
    }
}
