<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MidtransCallbackController;
use App\Http\Controllers\Api\MidtransReservasiCallbackController;
use App\Http\Controllers\Api\GuestChatApiController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Public\LandingController;
use App\Http\Middleware\VerifyMidtransSignature;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authenticated User Info
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Midtrans Webhooks (Server-to-Server)
Route::middleware(VerifyMidtransSignature::class)->group(function () {
    Route::post('/midtrans/callback', [MidtransCallbackController::class, 'handle'])
        ->name('api.midtrans.callback');

    Route::post('/midtrans/callback-reservasi', [MidtransReservasiCallbackController::class, 'handle'])
        ->name('api.midtrans.callback-reservasi');
});

// Public Availability Check API
Route::post('/cek-ketersediaan', [LandingController::class, 'cekKetersediaan'])
    ->name('api.cekKetersediaan');

// Public Guest Chat API Routes
Route::controller(GuestChatApiController::class)->prefix('guest-chat')->name('guest-chat.')->group(function () {
    Route::post('/start', 'startThread')->middleware('throttle:5,1')->name('start');
    Route::get('/messages', 'fetchMessages')->middleware('throttle:guest_chat_limiter')->name('messages');
    Route::post('/send', 'sendMessage')->middleware('throttle:guest_chat_limiter')->name('send');
});


