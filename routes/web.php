<?php

use Illuminate\Support\Facades\Route;

// Auth Controllers
use App\Http\Controllers\Auth\ReservasiAuthController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\PenyewaLoginController;
use App\Http\Controllers\Auth\PenyewaPasswordResetController;
use App\Http\Controllers\Auth\AdminPasswordResetController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

// Application Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\ProfileCompletionController;
use App\Http\Controllers\DashboardRedirectController;

// Admin Controllers
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KamarController as AdminKamarController;
use App\Http\Controllers\Admin\FasilitasController as AdminFasilitasController;
use App\Http\Controllers\Admin\CustomerReviewController as AdminCustomerReviewController;
use App\Http\Controllers\Admin\PenyewaController as AdminPenyewaController;
use App\Http\Controllers\Admin\ReservasiController as AdminReservasiController;
use App\Http\Controllers\Admin\TagihanController as AdminTagihanController;
use App\Http\Controllers\Admin\AdminCalendarController;
use App\Http\Controllers\Admin\PengeluaranController as AdminPengeluaranController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\KeluhanController as AdminKeluhanController;
use App\Http\Controllers\Admin\PeraturanController as AdminPeraturanController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\GuestChatController as AdminGuestChatController;
use App\Http\Controllers\Admin\NotifikasiController as AdminNotifikasiController;
use App\Http\Controllers\Admin\NotifikasiKhususController as AdminNotifikasiKhususController;

// Tenant Controllers
use App\Http\Controllers\Penyewa\DashboardController as PenyewaDashboardController;
use App\Http\Controllers\Penyewa\ReservasiController as PenyewaReservasiController;
use App\Http\Controllers\Penyewa\KeluhanController as PenyewaKeluhanController;
use App\Http\Controllers\Penyewa\NotifikasiController as PenyewaNotifikasiController;
use App\Http\Controllers\Penyewa\TagihanController as PenyewaTagihanController;
use App\Http\Controllers\Api\SnapTokenController;
use App\Http\Controllers\Api\ChatController;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC & LANDING ROUTES
|--------------------------------------------------------------------------
*/
Route::controller(LandingController::class)->group(function () {
    Route::get('/', 'index')->name('landing.index');
    Route::get('/fasilitas', 'fasilitas')->name('landing.fasilitas');
    Route::get('/kamar', 'kamarList')->name('landing.kamar');
    Route::get('/cara-booking', 'caraBooking')->name('landing.caraBooking');
    Route::get('/testimoni', 'testimoni')->name('landing.testimoni');
    Route::get('/tentang-kami', 'tentangKami')->name('landing.tentangKami');
    Route::get('/faq', 'faq')->name('landing.faq');
    Route::get('/galeri', 'galeri')->name('landing.galeri');
    Route::get('/kamar/{kamar}', 'showKamar')->name('landing.show');
    Route::post('/kamar/{kamar}/hitung-harga', 'hitungHarga')->name('landing.hitungHarga');
    Route::post('/cek-ketersediaan', 'cekKetersediaan')->name('landing.cekKetersediaan');
    Route::post('/analytics/track-whatsapp', 'trackWhatsappClick')->name('analytics.track-whatsapp');
});

// Alias Redirects
Route::redirect('/login', '/reservasi/login')->name('login');
Route::redirect('/register', '/reservasi/register')->name('register');

/*
|--------------------------------------------------------------------------
| 2. GUEST AUTHENTICATION PORTALS
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Guest Portal: Reservasi User
    Route::get('/reservasi/login', [ReservasiAuthController::class, 'showLogin'])->name('reservasi.login');
    Route::post('/reservasi/login', [ReservasiAuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/reservasi/register', [ReservasiAuthController::class, 'showRegister'])->name('reservasi.register');
    Route::post('/reservasi/register', [ReservasiAuthController::class, 'register'])->middleware('throttle:5,1');

    // Google OAuth
    Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Password Reset: User/Penyewa
    Route::get('/forgot-password', [PenyewaPasswordResetController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [PenyewaPasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PenyewaPasswordResetController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [PenyewaPasswordResetController::class, 'resetPassword'])->name('password.store');

    // Guest Portal: Admin
    Route::get('/admin/login', [AdminLoginController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/admin/forgot-password', [AdminPasswordResetController::class, 'showForgotPassword'])->name('admin.password.request');
    Route::post('/admin/forgot-password', [AdminPasswordResetController::class, 'sendResetLinkEmail'])->name('admin.password.email');
    Route::get('/admin/reset-password/{token}', [AdminPasswordResetController::class, 'showResetPassword'])->name('admin.password.reset');
    Route::post('/admin/reset-password', [AdminPasswordResetController::class, 'resetPassword'])->name('admin.password.update');

    // Guest Portal: Penyewa Login Direct
    Route::get('/penyewa/login', [PenyewaLoginController::class, 'showLogin'])->name('penyewa.login');
    Route::post('/penyewa/login', [PenyewaLoginController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/penyewa/forgot-password', [PenyewaPasswordResetController::class, 'showForgotPassword'])->name('penyewa.password.request');
    Route::post('/penyewa/forgot-password', [PenyewaPasswordResetController::class, 'sendResetLinkEmail'])->name('penyewa.password.email');
    Route::get('/penyewa/reset-password/{token}', [PenyewaPasswordResetController::class, 'showResetPassword'])->name('penyewa.password.reset');
    Route::post('/penyewa/reset-password', [PenyewaPasswordResetController::class, 'resetPassword'])->name('penyewa.password.update');
});

/*
|--------------------------------------------------------------------------
| 3. ADMIN PORTAL (PROTECTED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin', 'ensure.profile.complete', 'ensure.password.changed'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Security & Profile
        Route::get('/force-change-password', [AdminProfileController::class, 'showForceChangePassword'])->name('force-change-password');
        Route::post('/force-change-password', [AdminProfileController::class, 'updateForcePassword'])->name('force-change-password.update');
        Route::get('/profil', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profil', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::get('/pengaturan-konten', [AdminSettingController::class, 'edit'])->name('settings.edit');
        Route::post('/pengaturan-konten', [AdminSettingController::class, 'update'])->name('settings.update');

        // Master Data Management
        Route::resource('kamar', AdminKamarController::class);
        Route::patch('/kamar/{kamar}/status', [AdminKamarController::class, 'updateStatus'])->name('kamar.updateStatus');
        Route::post('/kamar/{id}/restore', [AdminKamarController::class, 'restore'])->name('kamar.restore');
        Route::get('/fasilitas-list', [AdminFasilitasController::class, 'listHtml'])->name('fasilitas.listHtml');
        Route::resource('fasilitas', AdminFasilitasController::class)->except(['create', 'show', 'edit']);
        Route::resource('reviews', AdminCustomerReviewController::class)->except(['show']);
        Route::resource('peraturan', AdminPeraturanController::class)->except(['show']);
        Route::resource('gallery', AdminGalleryController::class)->except(['show']);
        Route::resource('faq', AdminFaqController::class)->except(['show']);

        // Tenant & Reservation Operations
        Route::get('/penyewa/export-pdf', [AdminPenyewaController::class, 'exportPdf'])->name('penyewa.exportPdf');
        Route::get('/penyewa/export-csv', [AdminPenyewaController::class, 'exportCsv'])->name('penyewa.exportCsv');
        Route::get('/penyewa/exportPdf', [AdminPenyewaController::class, 'exportPdf']);
        Route::get('/penyewa/exportCsv', [AdminPenyewaController::class, 'exportCsv']);
        Route::resource('penyewa', AdminPenyewaController::class);
        Route::get('/penyewa/{penyewa}/checkout', [AdminPenyewaController::class, 'checkoutForm'])->name('penyewa.checkout.form');
        Route::post('/penyewa/{penyewa}/checkout', [AdminPenyewaController::class, 'processCheckout'])->name('penyewa.checkout.process');
        Route::post('/penyewa/{penyewa}/perpanjang', [AdminPenyewaController::class, 'perpanjang'])->name('penyewa.perpanjang');
        Route::post('/penyewa/{id}/restore', [AdminPenyewaController::class, 'restore'])->name('penyewa.restore');

        Route::resource('reservasi', AdminReservasiController::class)->only(['index', 'show', 'destroy']);
        Route::post('/reservasi/{reservasi}/konfirmasi', [AdminReservasiController::class, 'konfirmasi'])->name('reservasi.konfirmasi');
        Route::post('/reservasi/{reservasi}/batal', [AdminReservasiController::class, 'batal'])->name('reservasi.batal');
        Route::post('/reservasi/{id}/restore', [AdminReservasiController::class, 'restore'])->name('reservasi.restore');

        // Financial & Complaints
        Route::resource('tagihan', AdminTagihanController::class)->only(['index', 'show']);
        Route::post('/tagihan/{tagihan}/konfirmasi-cash', [AdminTagihanController::class, 'konfirmasiCash'])->name('tagihan.konfirmasiCash');
        Route::get('/nota/{pembayaran}/cetak', [AdminTagihanController::class, 'cetakNota'])->name('nota.cetak');
        Route::get('/pengeluaran/export-pdf', [AdminPengeluaranController::class, 'exportPdf'])->name('pengeluaran.exportPdf');
        Route::get('/pengeluaran/export-excel', [AdminPengeluaranController::class, 'exportExcel'])->name('pengeluaran.exportExcel');
        Route::resource('pengeluaran', AdminPengeluaranController::class);
        Route::resource('keluhan', AdminKeluhanController::class)->only(['index', 'show', 'update']);

        // Calendar & Reports
        Route::get('/kalender', [AdminCalendarController::class, 'index'])->name('calendar.index');
        Route::get('/kalender/events', [AdminCalendarController::class, 'getEvents'])->name('calendar.events');
        Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-pdf', [AdminLaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
        Route::get('/laporan/export-excel', [AdminLaporanController::class, 'exportExcel'])->name('laporan.exportExcel');

        // Communication & Log Management
        Route::get('/guest-chats', [AdminGuestChatController::class, 'index'])->name('guest-chats.index');
        Route::get('/guest-chats/threads', [AdminGuestChatController::class, 'listThreads'])->name('guest-chats.threads');
        Route::get('/guest-chats/{thread}/messages', [AdminGuestChatController::class, 'fetchMessages'])->name('guest-chats.messages');
        Route::post('/guest-chats/{thread}/send', [AdminGuestChatController::class, 'sendMessage'])->name('guest-chats.send');
        Route::post('/guest-chats/{thread}/close', [AdminGuestChatController::class, 'closeThread'])->name('guest-chats.close');
        Route::delete('/guest-chats/{thread}', [AdminGuestChatController::class, 'destroy'])->name('guest-chats.destroy');

        Route::get('/notifikasi', [AdminNotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::post('/notifikasi/broadcast', [AdminNotifikasiController::class, 'broadcast'])->name('notifikasi.broadcast');
        Route::post('/notifikasi/{log}/retry', [AdminNotifikasiController::class, 'retry'])->name('notifikasi.retry');
        Route::delete('/notifikasi/pengumuman/{pengumuman}', [AdminNotifikasiController::class, 'destroyPengumuman'])->name('notifikasi.destroyPengumuman');

        Route::get('/notifikasi-khusus', [AdminNotifikasiKhususController::class, 'index'])->name('notifikasi-khusus.index');
        Route::delete('/notifikasi-khusus/clear', [AdminNotifikasiKhususController::class, 'clearAll'])->name('notifikasi-khusus.clear');
        Route::delete('/notifikasi-khusus/{notifikasi}', [AdminNotifikasiKhususController::class, 'destroy'])->name('notifikasi-khusus.destroy');
    });

/*
|--------------------------------------------------------------------------
| 4. TENANT / PENYEWA PORTAL (PROTECTED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:penyewa', 'ensure.profile.complete', 'ensure.password.changed'])
    ->prefix('penyewa')
    ->name('penyewa.')
    ->group(function () {
        // Pending Tenant Access
        Route::get('/reservasi-dashboard', [PenyewaDashboardController::class, 'dashboardPending'])->name('reservasi.dashboard');
        Route::post('/reservasi', [PenyewaReservasiController::class, 'store'])->name('reservasi.store');
        Route::resource('keluhan', PenyewaKeluhanController::class)->only(['index', 'create', 'store', 'show']);
        Route::get('/peraturan', [PenyewaDashboardController::class, 'peraturan'])->name('peraturan');
        Route::get('/notifikasi', [PenyewaNotifikasiController::class, 'index'])->name('notifikasi.index');

        // Reservasi & Chat Detail Routes
        Route::get('/reservasi/pemesanan', [PenyewaReservasiController::class, 'index'])->name('reservasi.index');
        Route::get('/reservasi/pembayaran', [PenyewaReservasiController::class, 'riwayatPembayaran'])->name('pembayaran');
        Route::get('/reservasi/riwayat-chat', [PenyewaReservasiController::class, 'riwayatChat'])->name('chat.history');
        Route::get('/reservasi/{reservasi}', [PenyewaReservasiController::class, 'show'])->name('reservasi.show');
        Route::get('/reservasi/{reservasi}/pembayaran', [PenyewaReservasiController::class, 'show'])->name('reservasi.pembayaran');
        Route::post('/reservasi/{reservasi}/token', [SnapTokenController::class, 'generateReservasi'])->middleware('throttle:15,1')->name('reservasi.pembayaran.token');
        Route::post('/reservasi/{reservasi}/batal', [PenyewaReservasiController::class, 'batal'])->name('reservasi.batal');
        Route::get('/reservasi/{reservasi}/chat', [PenyewaReservasiController::class, 'chat'])->name('reservasi.chat');

        // Active Tenant Restricted Access
        Route::middleware('ensure.tenant.active')->group(function () {
            Route::get('/dashboard', [PenyewaDashboardController::class, 'index'])->name('dashboard');
            Route::resource('tagihan', PenyewaTagihanController::class)->only(['index', 'show']);
            Route::get('/nota/{pembayaran}/cetak', [PenyewaTagihanController::class, 'cetakNota'])->name('nota.cetak');
            Route::post('/pembayaran/{tagihan}/token', [SnapTokenController::class, 'generate'])->middleware('throttle:15,1')->name('pembayaran.token');
        });
    });

// Compatibility Redirects for legacy tenant URLs (GET only)
Route::middleware(['auth', 'role:penyewa'])->group(function () {
    Route::get('/penyewa/reservasi', fn() => redirect('/penyewa/reservasi/pemesanan'));
    Route::get('/penyewa/pembayaran', fn() => redirect('/penyewa/reservasi/pembayaran'));
    Route::get('/penyewa/riwayat-chat', fn() => redirect('/penyewa/reservasi/riwayat-chat'));
});

/*
|--------------------------------------------------------------------------
| 5. COMMON AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Profile Completion Workflow
    Route::get('/profil/complete', [ProfileCompletionController::class, 'showForm'])->name('profil.complete');
    Route::post('/profil/complete', [ProfileCompletionController::class, 'store'])->name('profil.complete.store');

    // Profile Management
    Route::middleware('ensure.profile.complete')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Aliases for Tenant Profile Routes
        Route::get('/penyewa/profile', [ProfileController::class, 'edit'])->name('penyewa.profile.edit');
        Route::patch('/penyewa/profile', [ProfileController::class, 'update'])->name('penyewa.profile.update');
        Route::delete('/penyewa/profile', [ProfileController::class, 'destroy'])->name('penyewa.profile.destroy');
    });

    Route::get('/dashboard', [DashboardRedirectController::class, 'redirect'])
        ->middleware('ensure.profile.complete')
        ->name('dashboard');

    // Email Verification & Password Confirmation
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Portal Dedicated Logouts
    Route::post('/reservasi/logout', [ReservasiAuthController::class, 'logout'])->name('reservasi.logout');
    Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
    Route::post('/penyewa/logout', [PenyewaLoginController::class, 'logout'])->name('penyewa.logout');
    Route::post('/logout', [ReservasiAuthController::class, 'logout'])->name('logout');

    // Authenticated Realtime Chat Web Endpoint (Session & CSRF Protected)
    Route::prefix('chat-box/{reservasi}')->group(function () {
        Route::get('/', [ChatController::class, 'fetch'])->middleware('throttle:120,1')->name('api.chat.fetch');
        Route::post('/', [ChatController::class, 'send'])->middleware('throttle:30,1')->name('api.chat.send');
        Route::get('/fetch', [ChatController::class, 'fetch'])->middleware('throttle:120,1')->name('chat.fetch');
        Route::post('/send', [ChatController::class, 'send'])->middleware('throttle:30,1')->name('chat.send');
    });

    // Legacy Route Aliases for Reservation
    Route::get('/penyewa/reservasi-alias/{reservasi}', [PenyewaReservasiController::class, 'show'])->name('reservasi.show');
    Route::get('/penyewa/reservasi-alias/{reservasi}/pembayaran', [PenyewaReservasiController::class, 'show'])->name('reservasi.pembayaran');
    Route::get('/penyewa/reservasi-alias/{reservasi}/chat', [PenyewaReservasiController::class, 'chat'])->name('reservasi.chat');
    Route::post('/penyewa/reservasi-alias/{reservasi}/token', [SnapTokenController::class, 'generateReservasi'])->middleware('throttle:15,1')->name('reservasi.pembayaran.token');
});


