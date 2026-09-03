<?php

namespace App\Observers;

use App\Models\Fasilitas;
use Illuminate\Support\Facades\Cache;

class FasilitasObserver
{
    // Clean Code: Gunakan konstanta untuk kunci cache agar terpusat dan mudah diubah
    public const CACHE_KEY_FASILITAS = 'fasilitas_aktif_landing';
    public const CACHE_KEY_KAMAR = 'kamar_aktif_landing';

    /**
     * Bersihkan cache terkait fasilitas untuk memperbarui tampilan landing page.
     */
    private function invalidateCache(): void
    {
        Cache::forget(self::CACHE_KEY_FASILITAS);
        Cache::forget(self::CACHE_KEY_KAMAR);
    }

    public function saved(Fasilitas $fasilitas): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the Fasilitas "deleted" event.
     */
    public function deleted(Fasilitas $fasilitas): void
    {
        $this->invalidateCache();
    }
}
