<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed', 'different:current_password'],
        ], [
            'password.different' => 'Password baru tidak boleh sama dengan password saat ini.',
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
            'require_password_change' => false,
        ]);

        return back()->with('status', 'password-updated');
    }
}
