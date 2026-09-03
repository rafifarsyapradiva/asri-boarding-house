<?php

namespace App\Listeners\Traits;

use Illuminate\Support\Facades\Date;

trait StaggersJobs
{
    /** @var int Default step delay dalam detik */
    public const DEFAULT_STAGGER_STEP = 3;

    /** @var int Default maksimal delay dalam detik */
    public const DEFAULT_STAGGER_MAX_DELAY = 1800;

    /** @var float Default threshold reset (detik) */
    public const DEFAULT_STAGGER_RESET_THRESHOLD = 5.0;

    protected static int $delaySeconds = 0;
    protected static ?float $lastDispatchTime = null;

    /**
     * Hitung delay stagger secara dinamis dan aman.
     * Menggunakan getter untuk fallback nilai konfigurasi agar mudah di-override di tingkat class.
     */
    protected function getStaggerDelay(?int $step = null, ?int $maxDelay = null): int
    {
        $now = $this->getCurrentTimeInSeconds();

        $resetThreshold = $this->getStaggerResetThreshold();
        $actualStep = $step ?? $this->getStaggerStep();
        $actualMaxDelay = $maxDelay ?? $this->getStaggerMaxDelay();

        // Reset delay jika request berikutnya terpaut lebih dari threshold (misal: ganti batch/request baru)
        if (static::$lastDispatchTime !== null && ($now - static::$lastDispatchTime) > $resetThreshold) {
            static::resetStaggerDelay();
        }

        static::$lastDispatchTime = $now;
        $current = static::$delaySeconds;

        // Batasi delay maksimum agar tidak menumpuk ke waktu yang tidak wajar
        static::$delaySeconds = min($actualMaxDelay, static::$delaySeconds + $actualStep);

        return $current;
    }

    /**
     * Mengambil waktu sekarang dalam format detik (presisi mikrodetik).
     * Dibungkus ke dalam metode agar dapat dimock secara penuh menggunakan helper Carbon/Laravel.
     */
    protected function getCurrentTimeInSeconds(): float
    {
        return Date::now()->getPreciseTimestamp(6) / 1e6;
    }

    /**
     * Getter dinamis untuk step delay (dapat dioverride dengan properti $staggerStep di class tujuan atau config).
     */
    protected function getStaggerStep(): int
    {
        if (property_exists($this, 'staggerStep') && $this->staggerStep !== null) {
            return (int) $this->staggerStep;
        }

        return (int) config('queue.stagger.step', self::DEFAULT_STAGGER_STEP);
    }

    /**
     * Getter dinamis untuk batas maksimal delay (dapat dioverride dengan properti $staggerMaxDelay di class tujuan atau config).
     */
    protected function getStaggerMaxDelay(): int
    {
        if (property_exists($this, 'staggerMaxDelay') && $this->staggerMaxDelay !== null) {
            return (int) $this->staggerMaxDelay;
        }

        return (int) config('queue.stagger.max_delay', self::DEFAULT_STAGGER_MAX_DELAY);
    }

    /**
     * Getter dinamis untuk batas waktu reset delay (dapat dioverride dengan properti $staggerResetThreshold di class tujuan atau config).
     */
    protected function getStaggerResetThreshold(): float
    {
        if (property_exists($this, 'staggerResetThreshold') && $this->staggerResetThreshold !== null) {
            return (float) $this->staggerResetThreshold;
        }

        return (float) config('queue.stagger.reset_threshold', self::DEFAULT_STAGGER_RESET_THRESHOLD);
    }

    /**
     * Reset status delay untuk testing atau pembersihan memori.
     */
    public static function resetStaggerDelay(): void
    {
        static::$delaySeconds = 0;
        static::$lastDispatchTime = null;
    }
}
