<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    /**
     * Redirect the user based on their role and active tenant status.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // CLEAN ARCHITECTURE: Menggunakan method enkapsulasi dari Model User
        return redirect()->route($user->getDashboardRouteName());
    }
}
