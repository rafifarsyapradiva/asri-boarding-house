<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        \Illuminate\Support\Facades\Cache::flush();
        \App\Models\Setting::clearRequestCache();

        \App\Models\User::creating(function ($user) {
            if (empty($user->no_hp)) {
                $user->no_hp = '08' . rand(1000000000, 9999999999);
            }
            if (empty($user->nik)) {
                $user->nik = rand(1000000000000000, 9999999999999999);
            }
            if (empty($user->nama_wali)) {
                $user->nama_wali = 'Test Wali';
            }
            if (empty($user->no_wali)) {
                $user->no_wali = '08' . rand(1000000000, 9999999999);
            }
        });

        \Illuminate\Support\Facades\Http::fake([
            'api.fonnte.com/*' => \Illuminate\Support\Facades\Http::response([
                'status' => true,
                'message' => 'Pesan berhasil diisolasikan oleh Anti-Gravity Guard (HP Fisik Aman)',
            ], 200),
        ]);
    }
}
