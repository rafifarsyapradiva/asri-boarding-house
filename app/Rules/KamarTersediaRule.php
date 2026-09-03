<?php

namespace App\Rules;

use App\Models\Kamar;
use App\Models\Penyewa;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class KamarTersediaRule implements ValidationRule
{
    public const STATUS_TERSEDIA = 'tersedia';
    public const STATUS_AKTIF = 'aktif';
    public const STATUS_NONAKTIF = 'nonaktif';

    /**
     * Create a new rule instance.
     *
     * @param Penyewa|null $penyewaModel Model penyewa yang sedang di-update (jika ada)
     * @param string|null $newStatus Status baru yang dikirim pada request
     */
    public function __construct(
        protected ?Penyewa $penyewaModel = null,
        protected ?string $newStatus = null
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_numeric($value)) {
            $fail('ID Kamar harus berupa angka.');
            return;
        }

        $kamar = Kamar::find($value);
        if (!$kamar) {
            $fail('Kamar tidak ditemukan.');
            return;
        }

        // Guard Clause: Jika kamar dalam status 'tersedia', maka kamar selalu dapat dipilih
        if ($kamar->status === self::STATUS_TERSEDIA) {
            return;
        }

        // Logika jika kamar TIDAK dalam status 'tersedia'
        if ($this->penyewaModel) {
            $isSameKamar = (int) $value === (int) $this->penyewaModel->kamar_id;
            $isReactivating = $this->newStatus === self::STATUS_AKTIF && $this->penyewaModel->status === self::STATUS_NONAKTIF;

            if ($isSameKamar && $isReactivating) {
                $fail("Kamar tidak tersedia (status kamar: {$kamar->status}). Kamar harus dalam status tersedia untuk diaktifkan kembali.");
                return;
            }

            if (!$isSameKamar) {
                $fail('Kamar yang dipilih tidak tersedia.');
                return;
            }
        } else {
            $fail('Kamar yang dipilih tidak tersedia.');
        }
    }
}
