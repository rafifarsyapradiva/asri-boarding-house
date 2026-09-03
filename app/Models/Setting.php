<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Default configurations for the landing page and system settings.
     */
    public static array $defaults = [];

    /**
     * Cache for setting values to prevent duplicate queries during a single request lifecycle.
     */
    protected static $requestCache = [];

    /**
     * Boot model events to sync request cache and defaults.
     */
    protected static function booted()
    {
        self::$defaults = config('settings.defaults', []);

        static::saved(function ($setting) {
            self::$requestCache[$setting->key] = $setting->value;
            \Illuminate\Support\Facades\Cache::forget('setting:' . $setting->key);
        });

        static::deleted(function ($setting) {
            unset(self::$requestCache[$setting->key]);
            \Illuminate\Support\Facades\Cache::forget('setting:' . $setting->key);
        });
    }

    /**
     * Get a setting value by key with optional custom default fallback.
     */
    public static function get(string $key, $default = null)
    {
        if (array_key_exists($key, self::$requestCache)) {
            return self::$requestCache[$key];
        }

        $value = \Illuminate\Support\Facades\Cache::remember('setting:' . $key, 86400, function () use ($key, $default) {
            $setting = self::find($key);
            if ($setting && $setting->value !== null && $setting->value !== '') {
                return $setting->value;
            }
            return $default ?? config("settings.defaults.{$key}");
        });

        self::$requestCache[$key] = $value;
        return $value;
    }

    /**
     * Get discount percentage for a specific rental type and duration.
     */
    public static function getDiscountForDuration(string $tipeSewa, int $durasi): float
    {
        for ($i = 1; $i <= 3; $i++) {
            $pkgType = self::get("promo_pkg{$i}_type");
            $pkgDuration = (int) self::get("promo_pkg{$i}_duration");

            if ($pkgType === $tipeSewa && $pkgDuration === $durasi) {
                return (float) self::get("promo_pkg{$i}_discount");
            }
        }

        $packages = config('settings.defaults.promo_section.packages', []);
        foreach ($packages as $pkg) {
            if (($pkg['type'] ?? '') === $tipeSewa && ((int) ($pkg['duration'] ?? 0)) === $durasi) {
                return (float) ($pkg['discount_percentage'] ?? 0);
            }
        }

        return 0.0;
    }

    /**
     * Format WhatsApp phone number for wa.me links.
     */
    public static function formatWhatsapp(string $nomor): string
    {
        $nomor = preg_replace('/[^0-9]/', '', $nomor);

        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        } elseif (!str_starts_with($nomor, '62')) {
            $nomor = '62' . $nomor;
        }

        return $nomor;
    }

    /**
     * Sanitasi tag iframe Google Maps agar aman dan responsif (Delegated ke SanitizerService).
     */
    public static function sanitizeGoogleMapsEmbed(string $value): string
    {
        return \App\Services\SanitizerService::sanitizeGoogleMapsEmbed($value);
    }

    /**
     * Clear the static request cache (used in testing).
     */
    public static function clearRequestCache(): void
    {
        self::$requestCache = [];
    }
}

