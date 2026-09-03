<?php

namespace App\Helpers;

class PhoneNumberHelper
{
    /**
     * Normalisasi nomor telepon ke format internasional Indonesia (62...).
     */
    public static function formatInternational(?string $nomor): string
    {
        if (empty($nomor)) {
            return '';
        }

        // 1. Hilangkan semua karakter non-numerik
        $nomor = preg_replace('/[^0-9]/', '', $nomor);

        if (empty($nomor)) {
            return '';
        }

        // 2. Koreksi prefix '620...' -> '62...'
        if (str_starts_with($nomor, '620')) {
            $nomor = '62' . substr($nomor, 3);
        }

        // 3. Ubah awalan menjadi format internasional '62'
        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        } elseif (!str_starts_with($nomor, '62')) {
            $nomor = '62' . $nomor;
        }

        // 4. Validasi panjang nomor (10-15 digit)
        $panjang = strlen($nomor);
        if ($panjang < 10 || $panjang > 15) {
            return '';
        }

        return $nomor;
    }

    /**
     * Dapatkan semua variasi format nomor telepon (08... dan 62...).
     *
     * @return array<int, string>
     */
    public static function getPhoneVariations(?string $phone): array
    {
        if (empty($phone)) {
            return [];
        }

        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (empty($cleaned)) {
            return [];
        }

        $variations = [$cleaned];
        if (str_starts_with($cleaned, '0')) {
            $variations[] = '62' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '62')) {
            $variations[] = '0' . substr($cleaned, 2);
        }

        return array_values(array_unique($variations));
    }
}
