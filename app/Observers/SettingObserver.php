<?php

namespace App\Observers;

use App\Models\Setting;
use App\Models\NotifikasiKhusus;
use Illuminate\Support\Facades\Cache;

class SettingObserver
{
    public const CACHE_PREFIX = 'setting:';
    public const LOG_SOURCE = 'admin';
    public const EVENT_SETTING_CHANGED = 'pengaturan_konten_diubah';

    public function saved(Setting $setting): void
    {
        Cache::forget(self::CACHE_PREFIX . $setting->key);
        \App\Http\View\Composers\LayoutSettingComposer::resetCache();

        $deskripsi = sprintf(
            "Pengaturan konten Landing Page diperbarui oleh Admin (Kunci: %s)",
            $setting->key
        );

        NotifikasiKhusus::log(
            self::LOG_SOURCE,
            self::EVENT_SETTING_CHANGED,
            $deskripsi,
            [
                'key' => $setting->key,
                'value' => $setting->value,
            ]
        );
    }

    public function deleted(Setting $setting): void
    {
        Cache::forget(self::CACHE_PREFIX . $setting->key);
        \App\Http\View\Composers\LayoutSettingComposer::resetCache();
    }
}
