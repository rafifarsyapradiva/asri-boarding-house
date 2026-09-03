<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

// Models
use App\Models\Penyewa;
use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Pengeluaran;
use App\Models\Setting;

// Observers
use App\Observers\PenyewaObserver;
use App\Observers\FasilitasObserver;
use App\Observers\KamarObserver;
use App\Observers\PengeluaranObserver;
use App\Observers\SettingObserver;

// Services & Composers
use App\Services\PdfGeneratorInterface;
use App\Services\DompdfGenerator;
use App\Http\View\Composers\LayoutSettingComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Nama Kunci Rate Limiter untuk Chat Tamu
     */
    public const GUEST_CHAT_LIMITER = 'guest_chat_limiter';

    /**
     * Konstanta Default Rate Limit (Jika tidak didefinisikan di config)
     */
    public const DEFAULT_GUEST_CHAT_LIMIT = 30;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Dependency Inversion: Bind Interface ke Concrete Implementation
        $this->app->singleton(
            PdfGeneratorInterface::class,
            DompdfGenerator::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Modularisasi Booting demi mematuhi Single Responsibility Principle
        // Note: Policies dan Event Listeners ditangani oleh Laravel 11 Auto-Discovery
        $this->registerObservers();
        $this->registerRateLimiters();
        $this->registerViewComposers();
    }

    /**
     * Mendaftarkan Model Observers
     */
    private function registerObservers(): void
    {
        Penyewa::observe(PenyewaObserver::class);
        Fasilitas::observe(FasilitasObserver::class);
        Kamar::observe(KamarObserver::class);
        Pengeluaran::observe(PengeluaranObserver::class);
        Setting::observe(SettingObserver::class);
    }

    /**
     * Mendaftarkan Custom Rate Limiters
     */
    private function registerRateLimiters(): void
    {
        RateLimiter::for(self::GUEST_CHAT_LIMITER, function (Request $request) {
            
            // Clean Logic & Cognitive Complexity reduction: Menggunakan null-safe operator
            $user = $request->user();
            if ($user?->isAdmin()) {
                return Limit::none();
            }

            // Ambil token dari cookie, header, atau input parameter dengan null-coalescing
            $token = $request->cookie('guest_chat_token') 
                ?? $request->header('X-Guest-Chat-Token') 
                ?? $request->input('session_token');

            // Clean Logic: Validasi tipe data token (mencegah manipulasi input bertipe array / empty string)
            // Hashing token menggunakan SHA-256 untuk keamanan data di cache/database
            $rateKey = (is_string($token) && trim($token) !== '') 
                ? hash('sha256', $token) 
                : ($request->ip() ?? '127.0.0.1');

            // Bersifat testable: mengambil limit dari config, fallback ke konstanta kelas
            $limitAmount = config('services.chat.guest_limit', self::DEFAULT_GUEST_CHAT_LIMIT);

            // Batasi request per menit per token/IP sesuai konfigurasi
            return Limit::perMinute($limitAmount)->by($rateKey);
        });
    }

    /**
     * Mendaftarkan View Composers untuk menyuntikkan data settings ke layout views.
     */
    private function registerViewComposers(): void
    {
        View::composer(
            [
                'layouts.landing',
                'layouts.navigation-landing',
                'layouts.guest',
                'layouts.navigation',
                'layouts.app',
                'layouts.admin-sidebar',
                'landing.*',
                'penyewa.*',
                'auth.*',
            ],
            LayoutSettingComposer::class
        );
    }
}
