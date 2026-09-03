<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Guard Clause: User belum terautentikasi
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => __('Unauthenticated.')], 401);
            }
            return redirect()->route('login');
        }

        // Guard Clause: Role user tidak sesuai dengan hak akses rute
        if (!in_array($user->role, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => __('Akses ditolak.')], 403);
            }
            abort(403, __('Akses ditolak'));
        }

        return $next($request);
    }
}
