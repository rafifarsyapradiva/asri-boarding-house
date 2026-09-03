<?php

namespace App\Services;

use App\Helpers\PhoneNumberHelper;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PenyewaAuthService
{
    /**
     * Memverifikasi apakah input password cocok dengan format nomor HP alternatif (08... vs 62...)
     * khusus ketika penyewa masih menggunakan password awal berbasis nomor HP.
     *
     * @param User $user User model yang akan divalidasi
     * @param string $passwordInput Password yang diinput oleh pengguna saat login
     * @return bool True jika password cocok dengan format alternatif nomor HP
     */
    public function attemptInitialPasswordFallback(User $user, string $passwordInput): bool
    {
        $inputAlternatives = $this->getAllPhoneVariations($passwordInput);
        if (empty($inputAlternatives)) {
            return false;
        }

        $rawPasswordInDb = $user->getRawOriginal('password') ?? $user->password;

        // Keamanan: Hanya berlaku jika flag require_password_change = true
        // ATAU jika password yang tersimpan di DB memang cocok dengan salah satu format nomor HP milik user.
        $isDefaultPassword = $user->require_password_change ||
            (!empty($user->no_hp) && $this->checkAnyHashMatch($this->getAllPhoneVariations($user->no_hp), $rawPasswordInDb));

        if (!$isDefaultPassword) {
            return false;
        }

        return $this->checkAnyHashMatch($inputAlternatives, $rawPasswordInDb);
    }

    /**
     * Mengembalikan seluruh variasi format nomor HP yang valid (08... dan 62...).
     *
     * @param string $phone
     * @return array<int, string>
     */
    public function getAllPhoneVariations(string $phone): array
    {
        return PhoneNumberHelper::getPhoneVariations($phone);
    }

    /**
     * Menghasilkan variasi format nomor HP alternatif untuk pencocokan password default.
     *
     * @param string $input
     * @return array<int, string>
     */
    public function generatePhoneNumberPasswordAlternatives(string $input): array
    {
        return PhoneNumberHelper::getPhoneVariations($input);
    }

    /**
     * Helper privat untuk mengecek apakah salah satu string kandidat cocok dengan hash password.
     */
    private function checkAnyHashMatch(array $candidates, string $hash): bool
    {
        foreach ($candidates as $candidate) {
            if (Hash::check($candidate, $hash)) {
                return true;
            }
        }

        return false;
    }
}
