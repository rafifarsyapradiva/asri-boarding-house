<?php

namespace App\Rules;

use App\Models\Penyewa;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TanpaPenyewaAktifLainRule implements ValidationRule
{
    public const STATUS_AKTIF = 'aktif';

    /**
     * Create a new rule instance.
     *
     * @param int|string|null $kamarId ID kamar yang dipilih
     * @param int|null $penyewaId ID penyewa yang dikecualikan (untuk update)
     */
    public function __construct(
        protected int|string|null $kamarId,
        protected ?int $penyewaId = null
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== self::STATUS_AKTIF || empty($this->kamarId)) {
            return;
        }

        $otherActiveTenantExists = Penyewa::query()
            ->where('kamar_id', $this->kamarId)
            ->where('status', self::STATUS_AKTIF)
            ->when($this->penyewaId, fn ($query) => $query->where('id', '!=', $this->penyewaId))
            ->exists();

        if ($otherActiveTenantExists) {
            $fail('Kamar yang dipilih sudah diisi oleh penyewa aktif lain.');
        }
    }
}
