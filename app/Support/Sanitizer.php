<?php

namespace App\Support;

class Sanitizer
{
    /**
     * Regex pattern untuk menghapus semua karakter kecuali angka, titik, dan koma.
     */
    private const CURRENCY_CLEAN_PATTERN = '/[^\d.,]/';

    /**
     * Regex pattern heuristik format ribuan Indonesia (misal "1.500" atau "150.000").
     */
    private const IDR_THOUSANDS_HEURISTIC_PATTERN = '/^\d{1,3}\.\d{3}$/';

    /**
     * Regex pattern untuk menghapus semua karakter non-numerik.
     */
    private const PHONE_CLEAN_PATTERN = '/[^0-9]/';

    /**
     * Kode negara default untuk normalisasi nomor HP (Indonesia).
     */
    private const DEFAULT_COUNTRY_CODE = '62';

    /**
     * Memformat string mata uang/angka menjadi nilai float standar database.
     * Mampu menangani format Indonesia, format Inggris/Internasional, serta simbol mata uang (Rp, $, USD, dll).
     */
    public static function currency(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        // Hapus semua variasi spasi (termasuk non-breaking space)
        $cleaned = preg_replace('/\s+/u', '', $value);

        // Deteksi nilai negatif (minus prefix atau tanda kurung akuntansi)
        $isNegative = self::isNegativeCurrency($cleaned);

        // Hapus simbol mata uang dan karakter non-numerik (kecuali angka, titik, koma)
        $cleaned = preg_replace(self::CURRENCY_CLEAN_PATTERN, '', $cleaned);

        if ($cleaned === '') {
            return $value;
        }

        $cleaned = self::normalizeNumericSeparators($cleaned);

        if ($isNegative) {
            $cleaned = '-' . $cleaned;
        }

        return is_numeric($cleaned) ? (float) $cleaned : $value;
    }

    /**
     * Membersihkan dan menormalisasi nomor HP ke format Indonesia standar Fonnte (628xxx).
     */
    public static function phoneNumber(?string $value): string
    {
        if ($value === null || trim($value) === '') {
            return '';
        }

        // Hapus karakter non-numerik
        $cleaned = preg_replace(self::PHONE_CLEAN_PATTERN, '', $value);

        if ($cleaned === '') {
            return '';
        }

        // Normalisasi format prefix khusus Indonesia (08xxx -> 628xxx, 6208xxx -> 628xxx, 8xxx -> 628xxx)
        if (str_starts_with($cleaned, self::DEFAULT_COUNTRY_CODE . '0')) {
            return self::DEFAULT_COUNTRY_CODE . substr($cleaned, 3);
        }

        if (str_starts_with($cleaned, '0')) {
            return self::DEFAULT_COUNTRY_CODE . substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '8')) {
            return self::DEFAULT_COUNTRY_CODE . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Membersihkan string email (trim whitespace dan konversi ke huruf kecil).
     */
    public static function email(?string $value): string
    {
        return is_string($value) ? trim(strtolower($value)) : '';
    }

    /**
     * Mengecek apakah string mata uang bernilai negatif.
     */
    private static function isNegativeCurrency(string $value): bool
    {
        return str_starts_with($value, '-') || (str_contains($value, '(') && str_contains($value, ')'));
    }

    /**
     * Menormalisasi pemisah ribuan dan desimal menjadi format standar database (titik sebagai desimal).
     */
    private static function normalizeNumericSeparators(string $cleaned): string
    {
        $dotCount = substr_count($cleaned, '.');
        $commaCount = substr_count($cleaned, ',');

        // Kasus 1: Terdapat titik dan koma sekaligus (e.g., "1.500.000,50" atau "1,500,000.50")
        if ($dotCount > 0 && $commaCount > 0) {
            $lastDot = strrpos($cleaned, '.');
            $lastComma = strrpos($cleaned, ',');

            if ($lastComma > $lastDot) {
                // Format Indonesia: titik = ribuan, koma = desimal
                $cleaned = str_replace('.', '', $cleaned);
                return str_replace(',', '.', $cleaned);
            }

            // Format Inggris/Internasional: koma = ribuan, titik = desimal
            return str_replace(',', '', $cleaned);
        }

        // Kasus 2: Hanya terdapat titik
        if ($dotCount > 0) {
            if ($dotCount > 1) {
                // Lebih dari satu titik pasti ribuan Indonesia (e.g. 1.500.000)
                return str_replace('.', '', $cleaned);
            }

            // Satu titik: heuristik ribuan Indonesia (e.g. 1.500 vs 12.3456)
            if (preg_match(self::IDR_THOUSANDS_HEURISTIC_PATTERN, $cleaned) && !str_starts_with($cleaned, '0.')) {
                return str_replace('.', '', $cleaned);
            }

            return $cleaned;
        }

        // Kasus 3: Hanya terdapat koma
        if ($commaCount > 0) {
            if ($commaCount > 1) {
                // Lebih dari satu koma pasti ribuan Internasional (e.g. 1,500,000)
                return str_replace(',', '', $cleaned);
            }

            // Satu koma: di Indonesia koma tunggal selalu desimal (e.g. 1500,50)
            return str_replace(',', '.', $cleaned);
        }

        return $cleaned;
    }
}

