<?php

use App\Services\Auth\PortalRedirectResolver;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'ensure.profile.complete' => \App\Http\Middleware\EnsureProfileIsComplete::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'ensure.tenant.active' => \App\Http\Middleware\EnsureTenantIsActive::class,
            'ensure.password.changed' => \App\Http\Middleware\EnsurePasswordChanged::class,
        ]);

        $middleware->redirectGuestsTo(
            fn (Request $request) => app(PortalRedirectResolver::class)->resolveGuestRedirect($request)
        );

        $middleware->redirectUsersTo(
            fn (Request $request) => app(PortalRedirectResolver::class)->resolveAuthenticatedRedirect($request)
        );

        $middleware->validateCsrfTokens(except: [
            'api/midtrans/callback',
            'api/midtrans/callback-reservasi',
        ]);

        $middleware->encryptCookies(except: [
            'guest_chat_token',
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withEvents(discover: [
        __DIR__.'/../app/Listeners',
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                return redirect()->back()
                    ->withInput($request->except('_token'))
                    ->withErrors(['email' => 'Sesi keamanan halaman telah habis karena dibiarkan terlalu lama. Silakan coba tekan tombol login lagi.']);
            }
        });
    })->create();
