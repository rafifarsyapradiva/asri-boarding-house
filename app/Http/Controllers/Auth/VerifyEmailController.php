<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $targetRoute = ($user && $user->role === 'admin') ? 'admin.dashboard' : 'penyewa.dashboard';

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route($targetRoute, absolute: false).'?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(route($targetRoute, absolute: false).'?verified=1');
    }
}
